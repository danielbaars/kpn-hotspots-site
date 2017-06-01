<?php

function submission_db() {

	$db = new mysqli("localhost", "kpnwifih_submits", "Gy5,7Pcs&CPdPy", "kpnwifih_submits");

	if($db->connect_errno > 0) {
		die("Unable to connect to database [" . $db->connect_error . "]");
	}

	$db->set_charset("utf8");

	return $db;

}

function use_captcha($form, $email, $ip, $insert = false) {

	$db = submission_db();

	$sql = <<<SQL
    SELECT id
    FROM submissions
    WHERE form = '$form'
    AND (email = '$email'
    OR ip = '$ip')
SQL;

	$result = $db->query($sql);

	if($insert) {

		$sql = <<<SQL
    INSERT INTO submissions
    (email, ip, form)
    VALUES ('$email', '$ip', '$form')
SQL;

		$db->query($sql);

	}

	return $result->num_rows >= 2;

}

function verify_captcha($response, $ip) {

	require_once("../libs/jsonwrapper/jsonwrapper.php");

	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$data = array('secret' => '6Lf1Hw8TAAAAAHvAatuns3eTGAz29t2U_LFg71jd', 'response' => $response, 'remoteip' => $ip);

	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => http_build_query($data),
	    ),
	);

	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	$json = json_decode($result, true);

	return $json['success'] == 1;

}