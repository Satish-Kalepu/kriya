<?php

include("db.php");
include("config.php");
require("smtp_ses.php");

echo "<pre>";
print_r( $_SERVER );

exit;

if( 1==5 ){

	$st = send_mail_smtp_ses( "ksatish21@gmail.com", "", "", "testing", "Testing" );
	print_r( $st );

        exit;
}

sendemail( 4 );

function sendemail( $vid ){

	global $connection;
	global $config_categories;

	$res = mysqli_query( $connection, "select * from kriya_schools where id = " . $vid );
	$row = mysqli_fetch_assoc( $res );
	if( $row ){

        	$selection = json_decode($row['selection'],true);
        	//print_r( $selection );exit;

		$message = "<p>Dear " . preg_replace("/\W+/", " ", $row['contact_person'] ) . "</p>";
		$message .= "<p>Thank you for your participation.</p>";
		$message .= "<p>Your registration number: <b>".str_pad($row['id'], 3, "0", STR_PAD_LEFT)."</b></p>";
		$message .= "<p>School: " . htmlspecialchars($row['school_name']) . " - ". htmlspecialchars($row['village_name'])."</p>";
		$message .= "<p>Phone: " . $row['phone'] . "</p>";
		$message .= "<p>Email: " . $row["email"] . "</p>";
		$message .= "<p>Total Students Nominated: " . $row["total_students"] . "</p>";
		$message .= "<p>&nbsp;</p>";
		$message .= "<p>Note: Take enough time to analyze availability, skills, and interests of the students.</p>";
		$message .= "<p>Properly plan participating groups and preparation with your students for the best outcome</p>";
		$message .= "<p>You can pick/change nominations of your choice until Feb 24th 2019.</p>";

		ob_start();
		?>		
		
	<table border="1" style="border-collapse:collapse;">
		<thead>
			<tr bgcolor='#f0f0f0'>
				<td >SNo</td>
				<td >Event</td>
				<td align='center'>Sub Juniors</td>
				<td align='center'>Juniors</td>
				<td align='center'>Seniors</td>
			</tr>
		</thead>
		<tbody>
	<?php	foreach( $config_categories as $key => $value ){
			$key = (int)$key;
			if( $selection[$key]['sub_jrs'][0] || $selection[$key]['jrs'][0] || $selection[$key]['srs'][0] ){
			?>
			<tr>
				<td><?=$value['sno'] ?></td>
				<td><?=$value['name'] ?></td>
				<td>
				<?php	if( $value["enabled"][0] ){ ?>
						<?php if($value['group']){ for($k=1;$k<=$value['enabled'][0];$k++){ if($selection[$key]['sub_jrs'][$k-1]){ ?>
							<div><?=$value['enabled'][0]>1?"Group ". $k .": ":"Group: " ?><?=$selection[$key]['sub_jrs'][$k-1] ?></div>
						<?php }}}else{ ?>
							<div><?=$selection[$key]['sub_jrs'][0]?$selection[$key]['sub_jrs'][0]:"-" ?></div>
						<?php } ?>
				<?php	}else{
					echo " - ";
				} ?>
				</td>
				<td>
				<?php	if( $value["enabled"][1] ){ ?>
						<?php if($value['group']){ for($k=1;$k<=$value['enabled'][1];$k++){  if($selection[$key]['jrs'][$k-1]){ ?>
							<div><?=$value['enabled'][1]>1?"Group ". $k .": ":"Group: " ?><?=$selection[$key]['jrs'][$k-1] ?></div>
						<?php } }}else{ ?>
							<div><?=$selection[$key]['jrs'][0]?$selection[$key]['jrs'][0]:"-" ?></div>
						<?php } ?>
				<?php	}else{
					echo " - ";
				} ?>
				</td>
				<td>
				<?php	if( $value["enabled"][2] ){ ?>
						<?php if($value['group']){ for($k=1;$k<=$value['enabled'][2];$k++){  if($selection[$key]['srs'][$k-1]){ ?>
							<div><?=$value['enabled'][2]>1?"Group ". $k .": ":"Group: " ?><?=$selection[$key]['srs'][$k-1] ?></div>
						<?php } }}else{ ?>
							<div><?=$selection[$key]['srs'][0]?$selection[$key]['srs'][0]:"-" ?></div>
						<?php } ?>
				<?php	}else{
					echo " - ";
				} ?>
				</td>
			</tr>
			<?php } 
			}
			?>
		</tbody>
	</table>		
	
		<?php
		$dd = ob_get_clean();
		$message .= $dd;
		$message .= "<p>&nbsp;</p>";
		$message .= "<p>Best Regards,</p>";
		$message .= "<p>&nbsp;</p>";
		$message .= "<p>Kriya Team</p>";
		$message .= "<p>7288822599, 8332993993</p>";

		$subject = "Kriya Participation Confirmation";

		//echo $row['email'];exit;

		$st = send_mail_smtp_ses( $row['email'], "", "ksatish21@gmail.com", $subject, $message );
		//$st = send_mail_smtp_ses( "ksatish21@gmail.com", "", "", $subject, $message );
		print_r( $st );
	}
}


exit;

$schools = array();
$query = "select * from kriya_options order by school_id, item_id";
$res = mysqli_query( $connection, $query );
while( $row = mysqli_fetch_assoc( $res ) ){
	if( !$schools[ $row['school_id'] ] ){
		$schools[ $row['school_id'] ] = array();
	}
	if( !$schools[ $row['school_id'] ][ $row['item_id'] ] ){
		$schools[ $row['school_id'] ][ $row['item_id'] ] = array(
			"sub_jrs"=>[],
			"jrs"=>[],
			"srs"=>[]
		);
	}
	if( $row['sub_jrs_cnt'] ){
		$schools[ $row['school_id'] ][ $row['item_id'] ][ 'sub_jrs' ][] = $row['sub_jrs_cnt'];
	}
	if( $row['jrs_cnt'] ){
		$schools[ $row['school_id'] ][ $row['item_id'] ][ 'jrs' ][] = $row['jrs_cnt'];
	}
	if( $row['srs_cnt'] ){
		$schools[ $row['school_id'] ][ $row['item_id'] ][ 'srs' ][] = $row['srs_cnt'];
	}
}

echo "<pre>";
print_r( $schools );
echo "</pre>";

echo "<pre>";
foreach( $schools as $school_id=>$items ){
	$cnt = 0;
	foreach( $items as $i=>$k ){
		foreach( $k['sub_jrs'] as $m=>$n ){
			$cnt+= $n;
		}
		foreach( $k['jrs'] as $m=>$n ){
			$cnt+= $n;
		}
		foreach( $k['srs'] as $m=>$n ){
			$cnt+= $n;
		}
	}
	echo "<div>". $school_id . ": " . $cnt . "</div>";
	//echo $cnt;
	//echo json_encode($items,JSON_PRETTY_PRINT);
	//$query = "update kriya_schools set `selection` = \"" . mysqli_escape_string( $connection, json_encode($items,JSON_PRETTY_PRINT) ) . "\" where id='" . $school_id . "' ";
	//echo "<div>". $query . "</div>";
	//mysqli_query( $connection, $query );
	//echo "<div>". mysqli_affected_rows($connection)."</div>";
	$query = "update kriya_schools set total_students = ".$cnt.",boys=".$cnt.",girls=0 where id='" . $school_id . "' ";
	echo "<div>". $query . "</div>";
	//mysqli_query( $connection, $query );
	//echo "<div>". mysqli_affected_rows($connection)."</div>";
}

exit;

echo "<pre>";
print_r( $schools );
echo "</pre>";
exit;

echo "<table>";
foreach( $config_categories as $i=>$j ){
	echo "<tr><td>" . $i . "</td><td>" . $j['old'] . "</td><td>" . $j['name'] . "</td></tr>";
	$query = "update kriya_options set item_id = " . $j['old'] . " where id = " . $i;
	echo "<div>". $query . "</div>";
}
echo "</table>";
foreach( $config_categories as $i=>$j ){
	if( $i != $j['old'] ){
		$query = "update kriya_options set item_id = " . $j['old'] . " where item_id = " . $i;
		echo "<div>". $query . "</div>";
		//mysqli_query( $connection, $query );
		//echo "<div>". mysqli_affected_rows($connection)."</div>";
	}
}

exit;
ini_set("display_errors", "On");
error_reporting(E_ALL );	

require 'vendor/vendor/autoload.php';

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

// Create an SesClient. Change the value of the region parameter if you're 
// using an AWS Region other than US West (Oregon). Change the value of the
// profile parameter if you want to use a profile in your credentials file
// other than the default.
$SesClient = new SesClient([
    'version' => '2010-12-01',
    'region'  => 'ap-south-1',
        'credentials' => array(
                'key'    => 'AKIAU4FESPPFGHQPKU7K',
                'secret' => 'XyytJZQ7OjFoj0DJS+CCFiIdzkiVGn03gkpPYxwA',
        )
]);

// Replace sender@example.com with your "From" address.
// This address must be verified with Amazon SES.
$sender_email = 'kriyaonline.alert@gmail.com';

// Replace these sample addresses with the addresses of your recipients. If
// your account is still in the sandbox, these addresses must be verified.
$recipient_emails = ['ksatish21@gmail.com'];

// Specify a configuration set. If you do not want to use a configuration
// set, comment the following variable, and the
// 'ConfigurationSetName' => $configuration_set argument below.
//$configuration_set = 'ConfigSet';

$subject = 'Amazon SES test (AWS SDK for PHP)';
$plaintext_body = 'This email was sent with Amazon SES using the AWS SDK for PHP.' ;
$html_body =  '<h1>AWS Amazon Simple Email Service Test Email</h1>'.
              '<p>This email was sent with <a href="https://aws.amazon.com/ses/">'.
              'Amazon SES</a> using the <a href="https://aws.amazon.com/sdk-for-php/">'.
              'AWS SDK for PHP</a>.</p>';
$char_set = 'UTF-8';

try {
    $result = $SesClient->sendEmail([
        'Destination' => [
            'ToAddresses' => $recipient_emails,
        ],
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
    ]);
    $messageId = $result['MessageId'];
    echo("Email sent! Message ID: $messageId"."\n");
} catch (AwsException $e) {
    // output error message if fails
    echo $e->getMessage();
    echo("The email was not sent. Error message: ".$e->getAwsErrorMessage()."\n");
    echo "\n";
}

exit;

require("smtp.php");

$mail = new SMTPMailer();
echo $mail->password;
$mail->addTo('ksatish21@gmail.com');
$mail->Subject('Testing');
$mail->Body(
    '<h3>Mail message</h3>
    This is a <b>html</b> message.<br>
    Greetings!'
);
if ($mail->Send()) echo 'Mail sent successfully';
else               echo 'Mail failure';

print_r( $mail->ShowLog() );


exit;
set_time_limit(6000);
session_start();
include('db.php');
include('config.php');
include("actions.php");
//echo "<pre>";
//print_r($_SESSION);
	include('mpdf60/mpdf.php');
$query = "select * from kriya_schools where id >301 order by id ";
$res1 = mysqli_query($connection,$query);
if(mysqli_error($connection))
{
	echo $query;
	echo mysqli_error($connection);
	exit;
}
while( $row1 = mysqli_fetch_assoc($res1) ){

	echo "<div>Creating PDF: " . $row1['id'] . "</div>";

	$html = "";

	$query = "select * from kriya_options where school_id = " . $row1['id'];
	$res2 = mysqli_query($connection,$query);
	if(mysqli_error($connection))
	{
		echo $query;
		echo mysqli_error($connection);
		exit;
	}
	$options = array();
	while( $row = mysqli_fetch_assoc($res2) ){
		$options[ $row['item_id'] ] = $row;
	}
	//print_r( $options );
	//exit;

	$html .= "<html>
	<head>
	<style>
	body {font-family: sans-serif;
	    font-size: 10pt;
	}
	tbody td { vertical-align: top; line-height:25px;  }
	thead td { vertical-align: middle; }
	table.ddd td{ border-bottom:1px solid white; border-right:1px solid white; }
	
	 
	</style>
	</head>
	<body>
	<center>
	<div id='registration_form_div' align='center' style='width:400px;border:2px solid #ffef96; border-radius:10px; padding:10px;margin-left:150px;'>
	<table width='100%' cellpadding='5' cellspacing='1' border='0' style='border-collapse:collapse;' align='center'>
	<tr >
	<td>Registration No</td>
	<td>".$row1['id']."</td>
	</tr>
	<tr >
	<td>School Name</td>
	<td>".$row1['school_name']."</td>
	</tr>
	<tr >
	<td>City</td>
	<td>".$row1['city']."</td>
	</tr>
	<tr >
	<td>District</td>
	<td>".$row1['district']."</td>
	</tr>
	<tr >
	<td>Contact Person</td>
	<td>".$row1['contact_person']."</td>
	</tr>
	<tr >
	<td>Email</td>
	<td>".$row1['email'] . "</td>
	</tr>
	<tr >
	<td>Phone 1</td>
	<td>".$row1['phone'] . "</td>
	</tr>
	<tr >
	<td>Phone 2</td>
	<td>".$row1['phone2']."</td>
	</tr>
	</table>
	</div>
	</center>
	<div style='height:30px;'></div>
	<table class='ddd' width='100%' style='font-size: 9pt; border-collapse: collapse;' cellpadding='5'>
	<tr valign='middle' style='font-weight:bold;'>
	<td class=\"col_\" align='center' width=\"40\" rowspan=\"2\" style=\"font-weight:bold;\" >Sno</td>
	<td align='center' width='50' rowspan=\"2\" style=\"font-weight:bold;\">Time</td>
	<td class='hcol3' align='center' rowspan=\"2\" style=\"font-weight:bold;\" >Competition Name</td>
	<td colspan='3' align='center' style=\"font-weight:bold;\" >Allowed children / groups</td>
	x</tr>
	<tr valign='middle'  style='font-weight:bold;'>
	<td width='60'align='center'  style=\"font-weight:bold;\">Sub juniors</td>
	<td width='60' align='center' style=\"font-weight:bold;\">Juniors</td>
	<td width='60' align='center' style=\"font-weight:bold;\">Seniors</td></tr></thead></tbody>";
	foreach ($config_categories as $key => $value) 
	{
		$key = (int)$key;
		$html .="<tr >
		<td class=\"col_\" align='right'>".$value['sno']."</td>
		<td class=\"col1\" align='center'>".$value['time']."</td>
		<td class=\"col2\" width='250'>".$value['english']."</td>
		<td class=\"col3\" align='center'>";

		if( $value["enabled"][0] )
		{
			//print_r( $value["enabled"][0]);
			 $html .="".($options[ $key ]['sub_jrs']?$options[ $key ]['sub_jrs']:"")."";
		}else{
			$html .="--";
		}
		$html .= "</td><td class=\"col4\" align='center'>";
		if( $value["enabled"][1] ){
			$html .="".($options[ $key ]['jrs']?$options[ $key ]['jrs']:"")."";
		}else{
			$html .="--";
		}
		$html .= "</td><td class=\"col5\" align='center'>";
		if( $value["enabled"][2] ){
			$html .="".($options[ $key ]['srs']?$options[ $key ]['srs']:"")."";
		}else{
			$html .="--";
		}
		$html .="</td>
		</tr>";
	}
	$html .="</tbody></table>";
	$html .="</div>";
	$mpdf=new mPDF( 'School_'. $row1['id'] );
	$mpdf->WriteHTML($html);
	$mpdf->autoScriptToLang = true;
	$mpdf->autoLangToFont = true;
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->Output("School_". str_pad($row1['id'],3,"0",STR_PAD_LEFT) . ".pdf" );
	//exit;
	unset($mpdf);

}

?>