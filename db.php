<?php

$config_ses_key = "AKIAU4FESPPFCJ4IGH7Y";
$config_ses_secret = "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";

if( $_SERVER['HTTP_HOST'] == "kriyaonline.org" ||  $_SERVER['HTTP_HOST'] == "www.kriyaonline.org" ){
	$user = "kriya";
	$pass = "Ch!ldrenFest!va1";
	$db = "kriya";
	$host = "kriya.db.9917486.8a2.hostedresource.net";

}else if( $argv[1] || $_SERVER['HTTP_HOST'] == "register.kriyaonline.org" ){
	$user = "kriya_2023";
	$pass = "kriya_2023";
	$db = "kriya_2023";
	$host = "localhost";
}else{
	$user = "kriya";
	$pass = "kriya";
	$db = "kriya";
	$host = "localhost";
}

$connection = mysqli_connect($host,$user,$pass,$db);
if(mysqli_connect_error()) 
{
	echo "there was an error with database connection<BR>";
	echo mysqli_connect_error();
	exit;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("content-type:text/html; charset=UTF-8");

?>
