<?php
exit;

require("smtp.php");

$mail = new SMTPMailer();
$mail->addTo('ksatish21@gmail.com');
$mail->Subject('Testing');
$mail->Body(
    '<h3>Mail message</h3>
    This is a <b>html</b> message.<br>
    Greetings!'
);
if ($mail->Send()) echo 'Mail sent successfully';
else               echo 'Mail failure';


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