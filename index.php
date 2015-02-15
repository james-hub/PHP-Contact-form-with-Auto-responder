<?php
/*
Copyright (c) 2008 ramui.com. All right reserved.
This product is protected by copyright and distributed under licenses restricting copying, distribution. Permission is granted to the public to download and use this script provided that this Notice and any statement of authorship are reproduced in every page on all copies of the script.
*/
class admin
{
private $admin;
private $response='';
private $con;

function __construct()
{
		$config=file('database/config.php',FILE_IGNORE_NEW_LINES);
		if(isset($config[2])){$this->response=$config[2];}
		if(strlen($config[1])<10){$this->install_now();return;}
		$this->con=trim($config[1]);
		$arr=explode('<>',$config[1]);
		if(isset($arr[3])){if(isset($_COOKIE['contact_admin'])&&($_COOKIE['contact_admin']==md5($arr[3]))){$this->admin=true;return;}}
		if(isset($_GET['forgetpassword'])){$this->forget_password($arr[0],$arr[1],$arr[2]);}
		if((isset($_POST['password']))&&(isset($_POST['name']))){
			$_SESSION['try']=((isset($_SESSION['try']))? 1+$_SESSION['try'] : 1);
			if($_SESSION['try'] > 3){$this->log_in('Your session expired. Please restart your browser.');return;}
			if((trim(stripslashes($_POST['name']))==$arr[0])&&(trim(stripslashes($_POST['password']))==$arr[1])){
				$rand=$this->generate_random();
				if(setcookie('contact_admin',md5($rand))){
					$_COOKIE['contact_admin']=md5($rand);
					unset($_SESSION['try']);
					$this->admin=true;
					file_put_contents('database/config.php','<?php exit;?>'."\r\n".$arr[0].'<>'.$arr[1].'<>'.$arr[2].'<>'.$rand."\r\n".$config[2]);
				}
			}
			else{$this->log_in('Invalid user name and / or password.');}
		}
		else{$this->log_in();}
}
private function generate_random()
{
         $str=str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_?/:(){}[]0123456789');
         return substr($str,0,rand(8,12));
}
private function build_page($title,$install=false)
{
?>        
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title><?php echo $title;?></title>
<link rel="stylesheet" type="text/css" href="screen.css" media="screen">
<script type="text/javascript" src="script/admin.js"></script>
</head>
<body>
<center>
<div id="main">
<div id="topmenubar">
<a href="http://<?php echo getenv('HTTP_HOST');?>">Home</a>&nbsp;|&nbsp;&nbsp;|&nbsp;<a target="_blank" href="http://ramui.com/documentation/ajax-contact-form-with-auto-responder.html">Documentation</a>&nbsp;|&nbsp;<a target="_blank" href="http://ramui.com/forum/viewforum.php?f=2">Help</a></div>
<table style="padding: 0 20px 0 30px; clear: both;"><tr><td>
<img src="images/logo.gif"></td></tr></table>
<div id="vmenu"><a href="http://<?php echo getenv('HTTP_HOST');?>">Home</a>
<?php if((!$this->admin)&&!$install){echo '<a href="index.php">Login</a><a href="index.php?forgetpassword=true">Forget password</a>';}
if($this->admin){echo '<a href="index.php">Admin center</a><a href="index.php?action=1">Auto responder</a><a href="index.php?action=2">Sendmail TEXT</a><a href="index.php?action=3">Sendmail HTML</a><a href="index.php?action=5">Logout</a>';}
?>
<a target="_blank" href="http://ramui.com/documentation/ajax-contact-form-with-auto-responder.html">Documentation</a>
<a target="_blank" href="http://ramui.com/forum/index.php?f=3">Report bug</a>
</div><div class="round" id="right">
<?php
}
private function build_footer()
{
        echo '<div id="bottom">PHP contact-form version-3.0 &copy;&nbsp<a target="_blank" href="http://ramui.com/">http://ramui.com</a>. All rights reserved.</div></div></center></body></html>';
        exit;
}
private function log_in($message='')
{
        $this->build_page("Login");
		echo '<form method="POST" style="text-align:left; padding-left:10px;" action="index.php" onsubmit="return validateLogin(this);">'.(empty($message)? '':'<div style="margin:5px;clear:both;color:red;">'.$message.'</div>').'<br/>';
?>
<big><u>Administrator login:</u></big>
<table style="margin:10px;width:330px;"><tr><td width="70px">User:</td>
<td><input type="text" style="border:1px solid #c0c0c0;" name="name" size="20"></td></tr>
<tr><td>Password:</td>
<td><input type="password" style="border:1px solid #c0c0c0;" name="password" size="20"></td></tr>
<tr><td colspan="2" style="padding-top:20px;">
<input type="submit" value="Submit" class="button" name="B1">&nbsp;&nbsp;<input type="reset" class="button" value="Reset" name="B2"></td></tr></table></form></div>
<?php
        $this->build_footer();
}
private function forget_password($admin,$pw,$email)
{
        $this->build_page("Recover password");
        if(!(isset($_POST['admin'])&&isset($_POST['email']))){
			echo '<form method="POST" style="text-align:left; padding-left:10px;" action="index.php?forgetpassword=true" onsubmit="return ForgetPassword(this);">
			<br /><big><u>Recover password:</u></big>
			<table style="margin:10px;width:330px;"><tr><td width="70px">User:</td>
			<td><input type="text" style="border:1px solid #c0c0c0;" name="admin" size="20"></td></tr>
			<tr><td>Email:</td>
			<td><input type="text" style="border:1px solid #c0c0c0;" name="email" size="20"></td></tr>
			<tr><td colspan="2" style="padding-top:20px;">
			<input type="submit" value="Submit" class="button" name="B1">&nbsp;&nbsp;<input type="reset" class="button" value="Reset" name="B2"></td></tr></table></form></div>';}
        else{
			echo '<div style="margin:10px">';
			if(($admin==trim($_POST['admin']))&&($email==trim($_POST['email']))){
				$message="Message from: http://ramui.com\n\nI.P.: ".($_SERVER['REMOTE_ADDR'])."\n\n"."Date: ".date('d.m.Y H:i')."\n\n";
				$message.="Hello,\nSomeone requests to recover password for your PHP contact form control panel.\n\nYour passord is: $pw\n\nRegards,\nAdmin,\nhttp://ramui.com";
				$message.="\n---------------------------\nThis is an auto generated email please do not reply.";
				if(@mail($email, 'Recover password', $message, 'From: contact@ramui.com')){echo '<p style="text-align: center; padding: 60px;"><span style="border:1px solid #aaffcc; padding: 10px; color: #006633; font-size: 12px; font-weight: 600;"><img style="border:none;" src="images/right.gif">Your password has been send.</span></p>';}
				else{echo '<p style="text-align: center; padding: 60px;"><span style="border:1px solid #ffaacc; padding: 10px; color: #660033; font-size: 12px; font-weight: 600;"><img style="border:none;" src="images/fail.gif" />Sorry! Unable send password at this moment.</span></p>';}
			}
			else{echo '<p style="text-align: center; padding: 60px;"><span style="border:1px solid #ffaacc; padding: 10px; color: #660033; font-size: 12px; font-weight: 600;"><img style="border:none;" src="images/fail.gif" />Sorry! Unable send password at this moment.</span></p>';}
			echo '</div></div>';
			}
        $this->build_footer();
}
private function log_out()
{
		setcookie('contact_admin','');
		unset($_COOKIE['contact_admin']);
		$this->admin=false;
		$this->log_in();
}

private function show_autoresponder()
{
        $this->build_page("Auto-responder");?>
		<iframe name="formSubmit" id="formSubmit" style="display:none;"></iframe>
        <form name="frmContact" action="index.php?action=4" method="post" target="formSubmit" style="margin:0 10px 10px 10px; text-align:left;">
        <div id="feedback"></div>

        <table id="filelist" style="width:100%; float:right; margin-top:20px;">
        <tr><td width="80px">Auto-responder:</td><td>
        <fieldset style="padding:0; border:none;">
        <textarea class="text" style="width:100%; height:300px;" name="response" id="fw_response" cols="6" rows="5"><?php echo htmlentities(str_replace('\r',"\r",str_replace('\n',"\n",$this->response)),ENT_QUOTES); ?></textarea></fieldset></td></tr>
        </table>
        <div style="text-align:center; clear:both;">
        <input id="Reset" type="reset" class="button" value="Reset" />&nbsp;
        <input name="submit" type="submit" class="button" value="Submit" /></div>
        </form></div><div class="round">
		<div class="header"><img style="float:none;clear:none;vertical-align:middle; margin:0 2px;border:none;" src="images/help.gif" /><b>Quick reference:</b></div>
        <div style="padding:10px;">The following variables will be replaced while sending real messages.<ul><li>
		%%SENDER%% = Sender name;</li>
		<li>%%EMAIL%% = Sender email;</li>
		<li>%%MESSAGE%% = Message sent by user;</li></ul>
		Please keep the field blank to turn off <b>Autoresponder</b></div></div>
        <?php
        $this->build_footer();
}

private function show_admin()
{
        $this->build_page("Admin center");
?>
<div style="margin:0 10px 10px 10px;">
<div style="margin:5px 0 0 0; border-bottom:0px solid #aaaaaa;"><big><u>Quick start manual:</u></big></div>
<ul><li><a href="index.php?action=1" target="_blank">Set auto-responder text:</a>&nbsp;&nbsp;
Write some text to be sent as immediate response to the message sent by your viewer. Please do not use HTML, message will be sent in text format.</li>
<li><a href="index.php?action=2" target="_blank">Sent custom TEXT message:</a>&nbsp;&nbsp;
Send message in text format from your site. To send message more than one recipient use multiple email separated by ",".</li>
<li><a href="index.php?action=3" target="_blank">Sent custom HTML message:</a>&nbsp;&nbsp;
Send message in HTML format from your site. Use HTML code in the message field. To send message more than one recipients use multiple email separated by ", "(comma followed by space).</li></ul></div></div>
<?php
        $this->build_footer();
}
private function show_mailtext()
{
        $this->build_page("Sendmail TEXT");
        $this->mail_form(6);
        $this->build_footer();
}
private function show_mailhtml()
{
        $this->build_page("Sendmail HTML");
        $this->mail_form(7);
        $this->build_footer();
}
private function mail_form($action_number)
{
        if($action_number==7){$message="<html><head>\n<title>Sample message</title>\n</head><body>\n\n<!-- write message here -->\n\n</body>\n</html>";}
        else{$message='//Write text message here.';}
		echo '<iframe name="formSubmit" id="formSubmit" style="display:none;"></iframe><form name="frmMail" action="index.php?action='.$action_number.'" method="post" target="formSubmit" onsubmit="return validateForm()">';
?>
<div id="feedback"></div>
<table id="filelist" style="width:100%; float:right; margin-top:20px;">
<tr><td width="80px">To:</td><td><input class="text" style="width:100%;" type="text" name="to" size="20"></td></tr>
<tr><td>Cc:</td><td><input class="text" style="width:100%;" type="text" name="cc" size="20"></td></tr>
<tr><td>Bcc:</td><td><input class="text" style="width:100%;" type="text" name="bcc" size="20"></td></tr>
<tr><td>From:</td><td><input value="noreply@<?php echo str_replace("www.","", strtolower(getenv("HTTP_HOST")));?>" class="text" style="width:100%;" type="text" name="from" size="20"></td></tr>
<tr><td>Reply to:</td><td><input class="text" style="width:100%;" type="text" name="reply" size="20"></td></tr>
<tr><td>Subject:</td><td><input class="text" style="width:100%;" type="text" name="subject" size="20"></td></tr>
<tr><td>Message:</td><td>
<fieldset style="padding:0; border:none;">
<textarea class="text" style="width:100%; height:300px;" name="message" cols="6" rows="5"><?php echo htmlspecialchars($message); ?></textarea></fieldset></td></tr>
</table>
<div style="text-align:center; clear:both;">
<input id="Reset" type="reset" class="button" value="Reset">&nbsp;
<input name="submit" type="submit" class="button" value="Submit" /></div>
</form></div>
<?php
}
private function custom_mail($type)
{
        $headers="";
        $to=stripslashes($_POST['to']);
        $cc=stripslashes($_POST['cc']);
        $bcc=stripslashes($_POST['bcc']);
        $from=stripslashes($_POST['from']);
        $reply=stripslashes($_POST['reply']);
        $subject = htmlspecialchars(stripslashes($_POST["subject"]));
        $message = stripslashes($_POST["message"]);
        if($type=='html'){
              $headers  = "MIME-Version: 1.0" . "\r\n";
              $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
        }
		else{$message = htmlspecialchars($message);}
        $headers.="From: ".$from."\r\n";
        if(!(empty($reply))){$headers.="Reply-To: ".$reply."\r\n";}
        if(!(empty($cc))){$headers.="Cc: ".$cc."\r\n";}
        if(!(empty($bcc))){$headers.="Bcc: ".$bcc."\r\n";}
        if(@mail($to,$subject,$message,$headers)){
             echo '<script>parent.postMessage("'.addslashes('<span style="color:green;">Your message has been successfully sent!</span>').'");</script>';}
        else{echo '<script>parent.postMessage("'.addslashes('<span style="color:red; background-color:white;">Fail to send message! Please try again later.</span>').'");</script>';}
        exit;
}

private function save_text()
{
        $msg=trim(stripslashes($_POST['response']));
		$msg='<?php exit;?>'."\r\n".$this->con."\r\n".str_replace("\r",'\r',str_replace("\n",'\n',$msg));
		$e=file_put_contents('database/config.php',$msg);
		$message=($e===false)? '<span style="color:red;">Fail to update! Please check file permission.</span>':'<span style="color:green;">Data has been updated successfully!</span>';
		echo '<script>parent.postMessage("'.addslashes($message).'");</script>';
}
private function install_now()
{
        if(isset($_POST['setname'])&&isset($_POST['setpassword'])&&isset($_POST['setto'])){
			$name=stripslashes(trim($_POST['setname']));
			$pw=stripslashes(trim($_POST['setpassword']));
			$to=stripslashes(trim($_POST['setto']));
			echo (file_put_contents('database/config.php','<?php exit;?><>'."\r\n".$name.'<>'.$pw.'<>'.$to."\r\n".$this->response))? '<script>parent.location.reload(true);</script>':'<script>parent.postMessage("'.addslashes('<span style="color:red;">Fail to Install! Please check file permission.</span>').'");</script>';
		}
		else{$this->build_page("Install",true);
?>
<iframe name="formSubmit" id="formSubmit" style="display:none;"></iframe>
<form name="frmInstall" method="POST" target="formSubmit" style="text-align:left; padding-left:10px;" action="index.php" OnSubmit="return validateInstall(this)">
<div id="feedback"></div>
<table style="margin-top: 20px;">
<tr><td style="border-right: 1px solid #888888; padding: 0 15px; width: 50%; line-height: 150%;">
<span style="color:#ee4444; display: block; margin-bottom: 5px;"><b>PLEASE READ CAREFULLY:</b></span>
<b><u>Set permission to "0777" or "777" of the followings:</u></b><br />
file=> mail/database/config.php;<br /><br />
<b><u>About the fields:</u></b><br />
User name and Password required to login to admin center where you can edit autoresponder text, send emails.<ul>
<li><b>User Name</b>: Put only alphanumeric characters (A-Z,a-z,0-9) of 5 to 10 characters long.</li>
<li><b>Password</b>: Password may be 6 to 12 characters long. The valid character set is<br />
A-Za-z0-9_-?/:(){}[]</li>
<li>Please write your correct email address. Messages sent by your viewers shall be sent to this address.</li></ul>
</td><td style="padding-left: 20px;">
<big><u>Install:</u></big>
<table style="margin: 10px; width: 300px;"><tr><td width="80">User:</td>
<td><input style="border: 1px solid #c0c0c0;" name="setname" size="15" type="text"></td></tr>
<tr><td><a href="javascript:generatePW()">Password</a>:</td>
<td><input style="border: 1px solid #c0c0c0;" name="setpassword" size="15" type="text"></td></tr>
<tr><td>Your email:</td>
<td><input style="border: 1px solid #c0c0c0;" name="setto" size="15" type="text"></td></tr>
</table></td></tr>
<tr><td colspan="2" style="padding-top: 10px; text-align: center;">
<input value="Submit" class="button" name="B1" type="submit">&nbsp;&nbsp;<input class="button" value="Reset" name="B2" type="reset"></td></tr></table></form></div>
<?php 
        $this->build_footer();}
}
public function get_query()
{
    if($this->admin){
		if(isset($_GET['action'])&&(preg_match('/^([0-9])$/',$_GET['action']))){
			$query=$_GET['action'];
			switch ($query){
				case "1":
					$this->show_autoresponder();
				break;
				case "2":
					$this->show_mailtext();
				break;
				case "3":
					$this->show_mailhtml();
				break;
				case "4":
					$this->save_text();
				break;
				case "5":
					$this->log_out();
				break;
				case "6":
					$this->custom_mail("text");
				break;
				case "7":
					$this->custom_mail("html");
				break;
				default :
					$this->show_admin();
				break;
			}
		}
		else{
			$this->show_admin();
		}
	}
}

}
session_start();
header('Content-Type: text/html; charset=utf-8');
$page=new admin();
$page->get_query();
?>