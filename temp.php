<?php

	require("smtp_ses.php");

	// mail("murarimaniram@gmail.com", "testing", 'testing', "From: webmaster@brighttechindia.com", "-f webmaster@brighttechindia.com");
	// mail("ksatish21@gmail.com", "testing", 'testing', "From: webmaster@brighttechindia.com", "-f webmaster@brighttechindia.com");
	// exit;

	$res= send_mail_smtp_ses("ksatish21@gmail.com", "", "", "testing ", "testing" );
	print_r( $res );

?>