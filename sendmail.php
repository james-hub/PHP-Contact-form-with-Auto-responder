<?php
/*
Copyright (c) 2008 ramui.com. All right reserved.
This product is protected by copyright and distributed under licenses restricting copying, distribution. Permission is granted to the public to download and use this script provided that this Notice and any statement of authorship are reproduced in every page on all copies of the script.
*/
session_start();
if((empty($_SESSION['dcaptcha_code']))||(md5($_POST['dcaptcha_code'])!==($_SESSION['dcaptcha_code']))){$msg=1;}
else{
	$subject = stripslashes(trim($_POST["subject"]));
	if (empty($subject)){$subject="No Subject";}
	$message = stripslashes(trim($_POST["message"]));
	$from = stripslashes(trim($_POST["from"]));
	$name = stripslashes(trim($_POST["name"]));
	$message1='Message from: '.$name."\n\n".'I.P.: '.($_SERVER['REMOTE_ADDR'])."\n\n".'Date: '.date('d.m.Y H:i')."\n\n".$message;
	$config=file('database/config.php',FILE_IGNORE_NEW_LINES);
	$to=explode('<>',$config[1]);
	$to=$to[2];
	if(@mail($to, $subject, $message1, 'From: '.$from)){
		if(!empty($config[2])){
			$response=str_replace(array('\r','\n','%%SENDER%%','%%EMAIL%%','%%MESSAGE%%'),array("\r","\n",$name,$from,$message),$config[2]);
			@mail($from,'Re: '.$subject, $response, 'From: noreply@'.str_replace('www.','', strtolower(getenv('HTTP_HOST'))));
		}
		$msg=2;
	}
	else{$msg=3;}
}
unset($_SESSION['dcaptcha_code']);
echo '<script>parent.postMessage('.$msg.')</script>';
?>