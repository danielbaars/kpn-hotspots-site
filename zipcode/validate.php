<?php

require_once("../libs/jsonwrapper/jsonwrapper.php");

$postcode = urldecode($_GET['postcode']);
$regex = '~\A[1-9]\d{3} ?[a-zA-Z]{2}\z~';
$valid = preg_match($regex, $postcode, $matches);

$postcode = str_replace(" ", "", $postcode);

if(!$valid) {
    die("Invalid");
}

$db = new mysqli("localhost", "kpnwifih_zipcode", "dJqkm90zHa_*ru", "kpnwifih_zipcode");

if($db->connect_errno > 0){
    die("Unable to connect to database [".$db->connect_error."]");
}

$db->set_charset("utf8");

$sql = <<<SQL
    SELECT *
    FROM `postcode`
    WHERE `postcode` = '$postcode'
SQL;

if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
}

print json_encode($result->fetch_assoc());