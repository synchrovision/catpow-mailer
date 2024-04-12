<?php
if(php_sapi_name()!=='cli'){die('This program should be executed with CLI');}
chdir(__DIR__);
echo "download Mail Form\n";
$ch=curl_init('https://github.com/synchrovision/catpow-mailform/archive/refs/heads/master.zip');
$f='tmp.zip';
$fp=fopen($f,'w');
curl_setopt($ch,CURLOPT_FAILONERROR,true);
curl_setopt($ch,CURLOPT_HEADER,0);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
curl_setopt($ch,CURLOPT_AUTOREFERER,true);
curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
curl_setopt($ch,CURLOPT_TIMEOUT,10);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0); 
curl_setopt($ch,CURLOPT_FILE,$fp);
if(empty(curl_exec($ch))){
	die('Failed to download Catpow Mail Form : '.curl_error($ch));
}
echo "extract Mail Form\n";
$zip=new ZipArchive;
if($zip->open($f)!==true){
	die('Failed to expand Zip');
}

$zip->extractTo('tmp');
$zip->close();

if(!is_dir('dist')){mkdir('dist');}
$tmp_dir='tmp/catpow-mailform-master';
foreach(scandir($tmp_dir) as $fname){
	if(in_array($fname,['.','..','mailer','.gitmodules'])){continue;}
	if(file_exists("../{$fname}")){continue;}
	rename("{$tmp_dir}/{$fname}","../{$fname}");
}
unlink($f);
passthru('rm -rf tmp');