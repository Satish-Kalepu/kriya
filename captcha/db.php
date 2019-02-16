<?php
ini_set( "default_charset", "utf-8" );

if( $_SERVER['HTTP_HOST'] == "kriyaonline.org"){
	$user = "kriya";
	$pass = "Ch!ldrenFest!va1";
	$db = "kriya";
	$host = "kriya.db.9917486.8a2.hostedresource.net";
}else{
	/*$user = "kriya";
	$pass = "kriya";*/
	$user = "root";
	$pass = "";
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