<?php
namespace Catpow;
use PHPMailer\PHPMailer\PHPMailer;
use Pelago\Emogrifier\CssInliner;

class MailForm{
	public static
		$karma_format="%-20s %10d %12d\n",
		$default_karma_settings=array(
			'recovery'=>100,
			'thredshold'=>1000,
			'values'=>array(
				'send_mail'=>10,
				'put_log'=>10,
				'doubt_input'=>10,
				'danger_input'=>500,
				'file_upload'=>50,
				'heavy_file_upload'=>200,
				'danger_file_upload'=>500
			)
		);
	private $id=null,$timer=array();
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
	public function output_as_text($name){
		if(isset($this->values[$name])){return $this->inputs[$name]->output_as_text();}
		if(isset($this->config['inputs'][$name]['default_text'])){return $this->config['inputs'][$name]['default_text'];}
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
		$this->reset_errors();
		if(!empty($this->agreements)){
			foreach($this->agreements as $key=>$conf){
				if(empty($post[$key])){
					$this->errors[$key]=validation\agreement::get_message($conf);
				}
			}
		}
		$this->received=array_merge(
			$this->allowed_inputs,
			array_intersect_key($post,$this->allowed_inputs),
			$this->receive_files()
		);
		foreach($this->received as $key=>$val){
			$input=$this->inputs[$key];
			if(empty($val)){
				if($input->required){$this->errors[$key]=validation\required::get_message($input->conf);}
				continue;
			}
			foreach($input->validation as $validation){
				$validationClass='\\Catpow\\validation\\'.$validation;
				if(($validationClass::$phase & validation\validation::INPUT_PHASE)){
					$this->add_karma($validationClass::get_karma($val,$input));
					if(!$validationClass::is_valid($this->received[$key],$input)){
						$this->errors[$input->name]=$validationClass::get_message($input->conf);
						continue;
					}
				}
			}
		}
		if(!empty($this->errors)){
			$this->received=array();
			throw new MailFormException($this->errors);
		}
		foreach($this->received as $key=>$val){
			$input=$this->inputs[$key];
			foreach($input->validation as $validation){
				$validationClass='\\Catpow\\validation\\'.$validation;
				if(($validationClass::$phase & validation\validation::CONFIRM_PHASE)){
					$this->add_karma($validationClass::get_karma($val,$input));
					if(!$validationClass::is_valid($this->received[$key],$input)){
						$this->errors[$input->name]=$validationClass::get_message($input->conf);
						continue;
					}
				}
			}
		}
		if(!empty($this->errors)){
			$this->received=array();
			throw new MailFormException($this->errors);
		}
		$this->merge_values();
	}
	public function reset_errors(){
		$this->errors=array();
	}
	public function merge_values($values=null){
		if(isset($values)){
			$this->values=array_merge($this->values,$values);
		}
		else{
			$this->values=array_merge($this->values,$this->received);
			$this->received=array();
		}
	}
	public function receive_files($files=null){
		$this->create_log_dir_if_not_exists();
		$files=isset($files)?$files:$_FILES;
		$files=array_intersect_key(array_filter($files,function($file){
			return !empty($file['tmp_name']);
		}),$this->allowed_inputs);
		foreach($files as $name=>$file){
			$input=$this->inputs[$name];
			foreach($input->validation as $validation){
				$validationClass='\\Catpow\\validation\\'.$validation;
				if(($validationClass::$phase & validation\validation::UPLOAD_PHASE)){
					$this->add_karma($validationClass::get_karma($file,$input));
					if(!$validationClass::is_valid($file,$input)){
						$this->errors[$input->name]=$validationClass::get_message($input->conf);
						continue;
					}
				}
			}
		}
		if(!empty($this->errors)){
			throw new MailFormException($this->errors);
		}
		$f=$this->get_gc_file();
		$h=fopen($f,'a');
		$expire=strtotime('+ 1 hour');
		foreach($files as $name=>$file){
			$input=$this->inputs[$name];
			$fname=uniqid().strtolower(strrchr($file['name'],'.'));
			$f=\TMP_DIR.'/'.$fname;
			if(!is_dir($d=dirname($f))){mkdir($d,0755,true);}
			if(move_uploaded_file($file['tmp_name'],$f)){
				$files[$name]['tmp_name']=$f;
				$files[$name]['file_name']=$fname;
				fputcsv($h,array($fname,$expire));
			}
		}
		fclose($h);
		$this->reduce_files();
		return $files;
	}
	public function save_file($fname,$save_as=null){
		$this->create_log_dir_if_not_exists();
		if(empty($save_as)){$save_as=$fname;}
		if(!is_dir(\UPLOADS_DIR)){mkdir(\UPLOADS_DIR,0755,true);}
		copy(\TMP_DIR.'/'.$fname,\UPLOADS_DIR.'/'.$save_as);
		return true;
	}
	public function reduce_files(){
		$f=$this->get_gc_file();
		$h=fopen($f,'r');
		$remain=array();
		$has_update=false;
		while($row=fgetcsv($h)){
			if($row[1]<time()){
				$has_update=true;
				if(file_exists(\TMP_DIR.'/'.$row[0])){
					unlink(\TMP_DIR.'/'.$row[0]);
				}
			}
			else{
				array_push($remain,$row);
			}
		}
		fclose($h);
		if($has_update){
			$h=fopen($f,'w');
			foreach($remain as $row){
				fputcsv($h,$row);
			}
		}
	}
	public function get_gc_file(){
		$this->create_log_dir_if_not_exists();
		$f=\LOG_DIR.'/gc.csv';
		if(!file_exists($f)){touch($f);chmod($f,0600);}
		return $f;
	}
	
	public function add_karma($val){
		$this->get_karma()->value+=$this->get_karma_value($val);
	}
	public function get_karma_value($name){
		if(is_numeric($name)){return (int)$name;}
		if(isset($this->config['karma']['values'][$name])){
			return $this->config['karma']['values'][$name];
		}
		elseif(isset(self::$default_karma_settings['values'][$name])){
			return self::$default_karma_settings['values'][$name];
		}
		return 0;
	}
	public function save_karma(){
		$karma=$this->get_karma();
		if(isset($karma->offset)){
			$h=fopen($this->get_karma_file(),'w');
			fseek($h,$karma->offset);
		}
		else{
			$h=fopen($this->get_karma_file(),'a');
		}
		fputs($h,sprintf(self::$karma_format,$karma->ip,$karma->value,$karma->time));
	}
	public function get_karma(){
		static $karma;
		if(isset($karma)){return $karma;}
		$ip=$_SERVER['REMOTE_ADDR'];
		$h=fopen($this->get_karma_file(),'r');
		while($line=fgets($h)){
			sscanf($line,'%s %d %d',$key,$val,$time);
			if($ip===$key){
				$threshold=isset($this->config['karma']['threshold'])?$this->config['karma']['threshold']:10000;
				$karma=(object)array('ip'=>$ip,'value'=>$val,'time'=>$time,'offset'=>ftell($h)-strlen($line),'suspend'=>$val > $threshold);
				if($karma->suspend){
					$pardon=isset($this->config['karma']['pardon'])?$this->config['karma']['pardon']:'1 day';
					$date=new \DateTime('now');
					$date->sub(\DateInterval::createFromDateString($pardon));
					if($karma->time < $date->getTimestamp()){
						$karma->value=0;
						$karma->time=time();
					}
				}
				else{
					$recovery=isset($this->config['karma']['recovery'])?$this->config['karma']['recovery']:1000;
					$karma->value-=$recovery*(time()-$time)/86400;
					if($karma->value<0){$karma->value=0;}
					$karma->time=time();
				}
				return $karma;
			}
		}
		return $karma=(object)array('ip'=>$ip,'value'=>0,'time'=>time(),'offset'=>null,'suspend'=>false);
	}
	public function check_karma(){
		return empty($this->get_karma()->suspend);
	}
	public function get_karma_file(){
		$this->create_log_dir_if_not_exists();
		$f=\LOG_DIR.'/karma.list';
		if(!file_exists($f)){touch($f);chmod($f,0600);}
		return $f;
	}
	
	public function get_mailer(){
		mb_language("japanese");
		mb_internal_encoding("UTF-8");
		$mailer=new PHPMailer();
		if(!empty($this->config['smtp'])){
			$smtp=$this->config['smtp'];
			$mailer->isSMTP();
			$mailer->SMTPAuth=true;
			$mailer->Host=$smtp['host'];
			$mailer->Username=$smtp['username'];
			$mailer->Password=$smtp['password'];
			$mailer->SMTPSecure=isset($smtp['smtpsecure'])?$smtp['smtpsecure']:'ssl';
			$mailer->Port=isset($smtp['port'])?$smtp['port']:(!empty($mailer->SMTPSecure)?465:25);
			if(isset($smtp['sender'])){$mailer->Sender=$smtp['sender'];}
		}
		$mailer->CharSet="UTF-8";
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
		$mailer->Subject=isset($subject)?$subject:$defaultHeaders['subject'];
		if(!empty($isHTML)){
			$html=ob_get_clean();
			$html=preg_replace_callback('@src=([\'"])(.+?\.(jpe?g|gif|png|webp|svg))\1@i',function($matches){
				if(strpos($matches[2],'://')!==false){return $matches[0];}
				if(file_exists($f=($matches[2][0]==='/'?$_SERVER['DOCUMENT_ROOT']:\FORM_DIR.'/mail/').$matches[2])){
					return 'src="data: '.mime_content_type($f).';base64,'.base64_encode(file_get_contents($f)).'"';
				}
				return $matches[0];
			},$html);
			$html=
				'<!DOCTYPE html><html lang="ja">'.
				'<head>'.
				'<meta name="viewport" content="width=device-width" />'.
				'<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'.
				'<title>'.$mailer->Subject.'</title>'.
				'</head>'.
				'<body class="mail_body">'.$html.'</body>'.
				'</html>';
			if(file_exists($f=\FORM_DIR.'/mail/css/'.$mail.'.css')){
				$css=file_get_contents($f);
			}
			else{
				$css=null;
			}
			$mailer->isHTML(true);
			$mailer->Body=CssInliner::fromHtml($html)->inlineCss($css)->render();
		}
		else{
			$mailer->Body=ob_get_clean();
		}
		
		if(file_exists($f=\FORM_DIR.'/mail/'.$mail.'-alt.php')){
			ob_start();
			include $f;
			$mailer->AltBody=ob_get_clean();
		}
		$mailer->send();
		$this->add_karma('send_mail');
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
		$this->id=null;
		$this->timer=array();
	}
	public function verify_nonce(){
		if($this->nonce!==$_SERVER['HTTP_X_CMF_NONCE']){throw new \Exception('Forbidden',403);};
	}
	public function refresh(){
		$this->nonce=bin2hex(openssl_random_pseudo_bytes(8));
		$this->expire=strtotime(isset($this->config['expire'])?$this->config['expire']:'+ 1 hour');
		$this->timer=array();
	}
	
	public function create_log_dir_if_not_exists(){
		if(!is_dir(\LOG_DIR)){
			mkdir(\LOG_DIR,0755,true);
			util\BasicAuth::create(
				\LOG_DIR,
				isset($this->config['log']['username'])?$this->config['log']['username']:'admin',
				isset($this->config['log']['password'])?$this->config['log']['password']:'password',
				"AddType application/octet-stream .csv\n"
			);
		}
	}
	public function put_log(){
		$this->create_log_dir_if_not_exists();
		$f=\LOG_DIR.'/log.csv';
		
		$log=array('id'=>$this->get_id(),'ipAddress'=>$_SERVER["REMOTE_ADDR"],'DateTime'=>date("Y/m/d (D) H:i:s",time()));
		foreach($this->config['inputs'] as $name=>$conf){
			$this->inputs[$name]->reflect_to_log($log);
		}
		if(file_exists($f) && filectime($f) < filemtime(\FORM_DIR.'/config.php')){
			rename($f,substr($f,0,-4).'-'.date('YmdHi',time()).'.csv');
		}
		if(!file_exists($f)){
			file_put_contents($f,pack('C*',0xEF,0xBB,0xBF));
			$h=fopen($f,'a');
			flock($h,\LOCK_EX);
			fputcsv($h,array_keys($log));
		}
		else{
			$h=fopen($f,'a');
			flock($h,\LOCK_EX);
		}
		fputcsv($h,array_values($log));
		fflush($h);
		flock($h,\LOCK_UN);
		fclose($h);
		$this->add_karma('put_log');
	}
	public function get_id(){
		if(isset($this->id)){return $this->id;}
		$this->create_log_dir_if_not_exists();
		$f=\LOG_DIR.'/lastInsertId.txt';
		if(file_exists($f)){
			$h=fopen($f,'r+');
			flock($h,\LOCK_EX);
			fscanf($h,'%d',$this->id);
			$this->id++;
			rewind($h);
		}
		else{
			$this->id=1;
			$h=fopen($f,'w');
			flock($h,\LOCK_EX);
		}
		fputs($h,$this->id);
		fflush($h);
		flock($h,\LOCK_UN);
		fclose($h);
		return $this->id;
	}
	
	public function start_timer($name,$exclusive=false){
		if($exclusive){$this->stop_all_timer();}
		if(isset($this->timer[$name]) && count($this->timer[$name])&1){return;}
		$this->timer[$name][]=time();
	}
	public function stop_timer($name){
		if(isset($this->timer[$name]) && count($this->timer[$name])&1){
			$this->timer[$name][]=time();
		}
	}
	public function stop_all_timer(){
		foreach($this->timer as $name=>$times){
			if(count($times)&1){$this->timer[$name][]=time();}
		}
	}
	public function get_timer_result($name){
		if(is_null($this->timer[$name])){return 0;}
		$value=0;
		for($i=0,$l=count($this->timer[$name]);$i<$l;$i+=2){
			$value+=($this->timer[$name][$i+1]??time())-$this->timer[$name][$i];
		}
		return $value;
	}
	public function get_timer_result_total(){
		$value=0;
		foreach($this->timer as $name=>$times){$value+=$this->get_timer_result($name);}
		return $value;
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
	
	public function render_nonce_register_script(){
		printf("\nCatpow.MailFormNonce=\"%s\";\n",$this->nonce);
	}
	public function render_ui_register_script(){
		$deps=array('script'=>array(),'style'=>array());
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
		printf('Catpow.MailForm.deps=%s;',json_encode([
			'scripts'=>array_keys((array)$deps['script']),
			'styles'=>array_keys((array)$deps['style'])
		],0700));
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