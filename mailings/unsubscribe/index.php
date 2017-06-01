<?php

$hash = $_GET['hash'];
$regex = '/^[a-f0-9]{32}$/i';
$valid = preg_match($regex, $hash);

if(!$valid) {
    die("Invalid");
}

$db = new mysqli("localhost", "kpnhotsp_del", "MBE6Z7n|\"hEkKH", "kpnhotsp_news");

if($db->connect_errno > 0){
    die("Unable to connect to database [".$db->connect_error."]");
}

$db->set_charset("utf8");

$sql = <<<SQL
    DELETE
    FROM emails
    WHERE hash = '$hash'
SQL;

if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
}

@header("Location: http://kpnwifihotspots.welikemilk.nl/unsubscribed.html");

exit;


