<?php

require 'vendor/autoload.php';

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

// Create an SesClient. Change the value of the region parameter if you're 
// using an AWS Region other than US West (Oregon). Change the value of the
// profile parameter if you want to use a profile in your credentials file
// other than the default.
require("../config_global.php");

$SesClient = new SesClient([
    //'profile' => 'default',
    'version' => 'latest',
    'region'  => 'ap-south-1',
    'credentials' => array(
        'key'    => $config_ses_key,
        'secret' => $config_ses_secret,
    )
]);

function send_mail_smtp_ses( $to, $cc, $bcc, $subject, $body, $sender_email = 'kriyaonline.alert@gmail.com', $sender_name = 'Kriya Registration' ){

	global $SesClient;
	
	//echo $sender_email;exit;
	
	if( 1==2 ){
	echo $to . "<BR>";
	echo $cc . "<BR>";
	echo $bcc . "<BR>";
	echo $subject . "<BR>";
	echo $body . "<BR>";
	echo $sender_email . "<BR>";
	exit;
	}
	
	// Replace sender@example.com with your "From" address.
	// This address must be verified with Amazon SES.
	
	// Replace these sample addresses with the addresses of your recipients. If
	// your account is still in the sandbox, these addresses must be verified.
	$recipient_emails = [$to];
	$cc_emails = explode(",",$cc);
	$bcc_emails = explode(",",$bcc);
	
	$dest = [
            'ToAddresses' => $recipient_emails,
        ];
        if( $cc ){
        	$dest['CcAddresses'] = $cc_emails;
	}
	if( $bcc ){
		$dest['BccAddresses'] = $bcc_emails;
	}

	//print_r( $cc_emails );print_r( $bcc_emails );exit;
	
	// Specify a configuration set. If you do not want to use a configuration
	// set, comment the following variable, and the
	// 'ConfigurationSetName' => $configuration_set argument below.
	$configuration_set = 'ConfigSet';
	
	$plaintext_body = strip_tags($body);
	$html_body =  $body;
	$char_set = 'UTF-8';
	
	try{
	    ob_start();
	    $result = $SesClient->sendEmail([
	        'Destination' => $dest,
	        'ReplyToAddresses' => [$sender_email],
	        'Source' => $sender_email,
	        'Message' => [
	          'Body' => [
	              'Html' => [
	                  'Charset' => $char_set,
	                  'Data' => $html_body,
	              ],
	              'Text' => [
	                  'Charset' => $char_set,
	                  'Data' => $plaintext_body,
	              ],
	          ],
	          'Subject' => [
	              'Charset' => $char_set,
	              'Data' => $subject,
	          ],
	        ],
	        // If you aren't using a configuration set, comment or delete the
	        // following line
	        //'ConfigurationSetName' => $configuration_set,
	    ]);
	    $messageId = $result['MessageId'];
	    $d = ob_get_clean();
	    return ["status"=>"success","msgid"=>$messageId];
	    //echo("Email sent! Message ID: $messageId"."\n");
	} catch (AwsException $e) {
		error_log( "error sending mail: " . $e->getMessage() );
		return ["status"=>"fail","error"=>$e->getMessage()];
	    // output error message if fails
	    //echo "<pre>". $e->getMessage() . "</pre>";
	    //echo("The email was not sent. Error message: ".$e->getAwsErrorMessage()."\n");
	    //echo "\n";
	}

}

?>
