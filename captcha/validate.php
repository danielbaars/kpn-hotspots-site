<?php

require_once("../libs/jsonwrapper/jsonwrapper.php");

$form = addslashes(urldecode($_GET['form']));
$email = addslashes(urldecode($_GET['email']));
$ip = $_SERVER['REMOTE_ADDR'];

if(!preg_match("/^(?!.{255,})(?!.{65,}@)([!#-'*+\\/-9=?^-~-]+)(?>\\.(?1))*@(?!.*[^.]{64,})(?>[a-z0-9](?>[a-z0-9-]*[a-z0-9])?\\.){1,126}[a-z]{2,6}$/iD", $email)) {
	die(json_encode(array('response' => 'incorrect email')));
}

if(!is_numeric($form)) {
	die(json_encode(array('response' => 'incorrect form')));
}

require_once("captcha.php");

print json_encode(array('response' => use_captcha($form, $email, $ip) ? 'show' : 'hide'));