<?php
mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");
require_once(__DIR__.'/vendor/autoload.php');
define('MAILER_DIR',__DIR__);
define('MAILER_URI',dirname($_SERVER['REQUEST_URI']));
define('FORM_DIR',dirname(__DIR__));
define('FORM_URI',dirname(MAILER_URI));
global $res,$form;
$form=Catpow\MailForm::get_instance();
define('LOG_DIR',isset($form->config['log_dir'])?$form->config['log_dir']:FORM_DIR.'/log');
define('TMP_DIR',LOG_DIR.'/tmp');
define('UPLOADS_DIR',LOG_DIR.'/uploads');
if($_SERVER['REQUEST_METHOD']=='GET'){
	if(!$form->check_karma()){
		header('HTTP/1.1 403 Forbidden');
		die();
	}
	if(isset($_GET['render'])){
		try{
			preg_match('/(\w+)(?:\[(\w+)\])?/',$_GET['render'],$matches);
			$form->inputs[$matches[1]]->render(isset($matches[2])?$matches[2]:0);
		}
		catch(Throwable $e){
			header('HTTP/1.1 403 Forbidden');
		}
		die();
	}
	$form->refresh();
	header("Content-Type: text/javascript; charset=utf-8");
	readfile(MAILER_DIR.'/js/script.js');
	$form->render_nonce_register_script();
	$form->render_ui_register_script();
	die();
}
$res=new Catpow\REST_Response();
while(ob_get_level()){
	break;
	$maybe_error_message=ob_get_clean();
	if(!empty($maybe_error_message)){
		$form->add_karma(100);
		$form->save_karma();
		$res['status']=500;
		$res['error']=array('@form'=>$maybe_error_message);
		echo $res;
		die();
	}
}
ob_start();
try{
	$form->verify_nonce();
	if(isset($_POST['action'])){
		$action=preg_replace('/\W/','',$_POST['action']);
		$f=FORM_DIR.'/form/'.$action.'.php';
		if(!file_exists($f)){throw new Exception('Forbidden',403);}
		include MAILER_DIR.'/functions.php';
		include $f;
		$form->start_timer($action,true);
	}
	elseif(!empty($_FILES)){
		$form->reset_errors();
		$files=$form->receive_files();
		if(!empty($files)){
			$form->merge_values($files);
			$res['files']=$files;
		}
	}
	else{
		throw new Exception('Forbidden',403);
	}
	$res['status']='200';
	$res['html']=ob_get_clean();
}
catch(Catpow\MailFormException $e){
	ob_end_clean();
	$res['error']=$e->errors;
}
catch(Throwable $e){
	ob_end_clean();
	$res['status']=$e->getCode();
	$res['error']=array('@form'=>$e->getMessage());
}
$form->save_karma();
header("Content-Type: application/json; charset=utf-8");
echo $res;