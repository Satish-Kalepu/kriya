<?php

$connection = mysqli_connect( $host, $user, $pass, $db );
if( mysqli_connect_error() ){
	echo "there was an error with database connection<BR>";
	echo mysqli_connect_error();
	exit;
}

header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
header( "Cache-Control: post-check=0, pre-check=0", false );
header( "Pragma: no-cache" );
header( "Content-Type: text/html; charset=UTF-8" );

