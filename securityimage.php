<?php
/*
Copyright (c) 2008 ramui.com. All right reserved.
This product is protected by copyright and distributed under licenses restricting copying, distribution. Permission is granted to the public to download and use this script provided that this Notice and any statement of authorship are reproduced in every page on all copies of the script.
*/
session_start();
$img = @imagecreatetruecolor(50, 16) or die("Unable to create verification image!");
$black = imagecolorallocate($img, 0, 0, 0);
$white = imagecolorallocate($img, 255, 255, 255);
imagefill($img, 0, 0, $white);
$n= rand(1000,9999);
imagestring($img,5,0,0,$n,$black);
$_SESSION['dcaptcha_code'] = md5($n);
unset($n);
imagejpeg($img);
imagedestroy($img);
?>