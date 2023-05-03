<?php
namespace Catpow;
use PHPMailer;

class MailForm{
	public $nonce,$created,$expire,$inputs=array(),$allowed_actions=array(),$allowed_inputs=array(),$agreements=array(),$config,$values=array(),$received=array(),$errors=array();
	public function __construct(){
		include \FORM_DIR.'/config.php';
		$this->config=$config;
		foreach($this->config['inputs'] as $name=>$conf){
			$className='\\Catpow\\input\\'.(isset($conf['type'])?$conf['type']:'text');
			$this->inputs[$name]=new $className($name,$conf,$this);
		}
		$this->created=time();
		$this->refresh();
	}
	public static function get_instance(){
		if(!isset($_SESSION)){session_start();}
		if(
			!isset($_SESSION['MailForm'][\FORM_DIR]) || 
			$_SESSION['MailForm'][\FORM_DIR]->expire < time() ||
			$_SESSION['MailForm'][\FORM_DIR]->created < filemtime(\FORM_DIR.'/config.php')
		){
			$_SESSION['MailForm'][\FORM_DIR]=new self();
		}
		return $_SESSION['MailForm'][\FORM_DIR];
	}
	
	public function input($name){
		$this->allow_input($name);
		return sprintf(
			'<div class="cmf-input %s" data-input="%s">%s</div>',
			$this->inputs[$name]->blockClassName,
			$name,$this->inputs[$name]->input()
		);
	}
	public function output($name){
		if(isset($this->values[$name])){return $this->inputs[$name]->output();}
		if(isset($this->config['inputs'][$name]['default'])){return $this->config['inputs'][$name]['default'];}
		return '';
	}
	public function button($label,$action,$class='secondary'){
		$this->allow_action($action);
		return sprintf('<div class="cmf-button %s" data-action="%s">%s</div>',$class,$action,$label);
	}
	public function allow_input($name){
		$this->allowed_inputs[$name]='';
	}
	public function is_allowed_input($name){
		return !isset($this->allowed_inputs[$name]);
	}
	public function allow_action($action){
		$this->allowed_actions[$action]='';
	}
	public function is_allowed_action($action){
		return isset($this->allowed_actions[$action]);
	}
	
	public function agreement($label,$conf=null){
		$conf=isset($conf)?$conf:array();
		$conf['type']='checkbox';
		$conf['value']=array($label);
		$name=isset($conf['name'])?$conf['name']:'agreement';
		$this->agreements[$name]=$conf;
		$input=new input\checkbox($name,$conf,$this);
		return sprintf('<div class="cmf-input cmf-input_%s cmf-agreement" data-input="%s">%s</div>',$input->type,$name,$input->input());
	}
	public function receive($post=null){
		$post=isset($post)?$post:$_POST;
		$this->errors=array();
		if(!empty($this->agreements)){
			foreach($this->agreements as $key=>$conf){
				if(empty($post[$key])){
					$this->errors[$key]=validation\agreement::get_message($conf);
				}
			}
		}
		$this->received=array_merge(
			$this->allowed_inputs,
			array_intersect_key($post,$this->allowed_inputs)
		);
		foreach($this->received as $key=>$val){
			$input=$this->inputs[$key];
			if(empty($val)){
				if($input->required){$this->errors[$key]=validation\required::get_message($input->conf);}
				continue;
			}
			foreach($input->validation as $validation){
				$validationClass='\\Catpow\\validation\\'.$validation;
				if(!$validationClass::is_valid($this->received[$key],$input)){
					$this->errors[$input->name]=$validationClass::get_message($input->conf);
					continue;
				}
			}
		}
		if(!empty($this->errors)){
			$this->received=array();
			throw new MailFormException($this->errors);
		}
		$this->values=array_merge($this->values,$this->received);
		$this->received=array();
	}
	
	public function get_mailer(){
		mb_language("japanese");
		mb_internal_encoding("UTF-8");
		$mailer=new PHPMailer();
		if(!empty($this->config['smtp'])){
			$smtp=$this->config['smtp'];
			$this->mailer->isSMTP();
			$mailer->Host=$smtp['host'];
			$mailer->Username=$smtp['username'];
			$mailer->Password=$smtp['password'];
		}
		$mailer->CharSet="iso-2022-jp";
		$mailer->Encoding="7bit";
		return $mailer;
	}
	public function send($mail){
		$mail=preg_replace('/\W/','',$mail);
		if(!file_exists($f=\FORM_DIR.'/mail/'.$mail.'.php')){return false;}
		$form=$this;
		$mailer=$this->get_mailer();
		ob_start();
		include $f;
		$defaultHeaders=$this->config['defaultHeaders'];
		call_user_func_array(array($mailer,'setFrom'),self::parse_address(isset($from)?$from:$defaultHeaders['from']));
		foreach((array)(isset($to)?$to:$defaultHeaders['to']) as $toAddress){
			call_user_func_array(array($mailer,'addAddress'),self::parse_address($toAddress));
		}
		$mailer->Subject=mb_encode_mimeheader(isset($subject)?$subject:$defaultHeaders['subject']);
		if(!empty($isHTML)){$mailer->isHTML(true);}
		$mailer->Body=ob_get_clean();
		if(file_exists($f=\FORM_DIR.'/mail/'.$mail.'-alt.php')){
			ob_start();
			include $f;
			$mailer->AltBody=ob_get_clean();
		}
		$mailer->send();
	}
	
	public function clear(){
		$this->agreements=array();
		$this->allowed_actions=array();
		$this->allowed_inputs=array();
	}
	public function clear_all(){
		$this->clear();
		$this->values=array();
		$this->received=array();
	}
	public function verify_nonce(){
		if($this->nonce!==$_SERVER['HTTP_X_CMF_NONCE']){throw new \Exception('Forbidden',403);};
	}
	public function refresh(){
		$this->nonce=bin2hex(openssl_random_pseudo_bytes(8));
		$this->expire=strtotime(isset($this->config['expire'])?$this->config['expire']:'+ 1 hour');
	}
	public function put_log(){
		$f=\FORM_DIR.'/log/log.csv';
		
		$inputs=$this->config['inputs'];
		if(file_exists($f) && filectime($f) < filemtime(\FORM_DIR.'/config.php')){
			rename($f,substr($f,0,-4).'-'.date('YmdHi',time()).'.csv');
		}
		if(!file_exists($f)){
			if(!is_dir($d=dirname($f))){
				mkdir($d);
				util\BasicAuth::create(
					$d,
					isset($this->config['user'])?$this->config['user']:'admin',
					isset($this->config['password'])?$this->config['password']:'password',
					"AddType application/octet-stream .csv\n"
				);
			}
			file_put_contents($f,pack('C*',0xEF,0xBB,0xBF));
			$h=fopen($f,'a');
			$labels=array();
			foreach($inputs as $input){$labels[]=$input['label'];}
			$labels=array_merge($labels,array('ipAddress','DateTime'));
			fputcsv($h,$labels);
		}
		else{
			$h=fopen($f,'a');
		}
		$values=array();
		foreach($inputs as $name=>$conf){
			$values[]=$this->inputs[$name]->get_log_value();
		}
		fputcsv($h,array_merge($values,array($_SERVER["REMOTE_ADDR"],date("Y/m/d (D) H:i:s",time()))));
		fclose($h);
	}
	public static function parse_address($address){
		if(preg_match('/^(?P<name>.+)<(?P<email>.+@.+)>$/u',$address,$matches)){
			return array($matches['email'],mb_encode_mimeheader($matches['name']));
		}
		return array($address,'');
	}
	public static function get_json($name){
		$path='/json/'.$name.'.json';
		if(file_exists($f=\FORM_DIR.$path) || file_exists($f=\MAILER_DIR.$path)){
			return json_decode(file_get_contents($f),true);
		}
		return array();
	}
	
	public function render_ui_loader_script(){
		$deps=array();
		foreach($this->inputs as $input){
			if(!is_null($input->useScripts)){
				foreach($input->useScripts as $useScript){
					if($useScriptURL=self::get_js_file_url($useScript)){
						$deps['script'][$useScriptURL]=true;
					}
				}
			}
			if(!is_null($input->useStyles)){
				foreach($input->useStyles as $useStyle){
					if($useStyleURL=self::get_css_file_url($useStyle)){
						$deps['style'][$useStyleURL]=true;
					}
				}
			}
			if(is_null($input->ui)){continue;}
			$deps=array_merge_recursive(static::get_deps('/ui/'.$input->ui),$deps);
		}
		foreach(array_keys((array)$deps['script']) as $script){
			echo "Catpow.MailForm.loadScript('{$script}');\n";
		}
		foreach(array_keys((array)$deps['style']) as $style){
			echo "Catpow.MailForm.loadStyle('{$style}');\n";
		}
		printf("Catpow.MailForm.requireReact=%s;\n",empty($deps)?'false':'true');
	}
	public static function get_deps($path){
		$deps=array();
		foreach(array('script'=>'/component.js','style'=>'/style.css') as $type=>$fname){
			foreach(array(\FORM_DIR=>\FORM_URI,\MAILER_DIR=>\MAILER_URI) as $dir=>$uri){
				if(self::file_should_exists($dir.$path.$fname)){
					$deps[$type][$uri.$path.$fname]=true;
					break;
				}
			}
		}
		if(self::file_should_exists(\FORM_DIR.$path.'/style.css')){
			$deps['style'][\FORM_URI.$path.'/style.css']=true;
		}
		elseif(self::file_should_exists(\MAILER_DIR.$path.'/style.css')){
			$deps['style'][\MAILER_URI.$path.'/style.css']=true;
		}
		if(file_exists($f=\FORM_DIR.$path.'/deps.php') || file_exists($f=\MAILER_DIR.$path.'/deps.php')){
			include $f;
			if(!empty($useComponents)){
				foreach($useComponents as $useComponent){
					$deps=array_merge_recursive(self::get_deps('/components/'.$useComponent),$deps);
				}
			}
			if(!empty($useScripts)){
				foreach($useScripts as $useScript){
					if($useScriptURL=self::get_js_file_url($useScript)){
						$deps['script'][$useScriptURL]=true;
					}
				}
			}
			if(!empty($useStyles)){
				foreach($useStyles as $useStyle){
					if($useStyleURL=self::get_css_file_url($useStyle)){
						$deps['style'][$useStyleURL]=true;
					}
				}
			}
		}
		return $deps;
	}
	public static function get_file_url($path){
		if(self::file_should_exists(\FORM_DIR.$path)){return \FORM_URI.$path;}
		if(self::file_should_exists(\MAILER_DIR.$path)){return \MAILER_URI.$path;}
		return null;
	}
	public static function get_js_file_url($path){
		if(strpos($path,'://')!==false){return $path;}
		return self::get_file_url('/js/'.$path.'.js');
	}
	public static function get_css_file_url($path){
		if(strpos($path,'://')!==false){return $path;}
		return self::get_file_url('/css/'.$path.'.css');
	}
	public static function file_should_exists($file){
		if(file_exists($file)){return true;}
		switch(strrchr($file,'.')){
			case '.js':
				if(file_exists($file.'x')){return true;}
				return false;
			case '.css':
				$scss=substr($file,0,-3).'scss';
				if(file_exists($scss)){return true;}
				if(file_exists(str_replace('/css/','/scss/',$scss))){return true;}
				if(file_exists(str_replace('/css/','/_scss/',$scss))){return true;}
				return false;
		}
		return false;
	}
}

?>