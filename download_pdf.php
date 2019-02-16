<?php
session_start();
include('db.php');
include('config.php');
include("actions.php");
//echo "<pre>";
//print_r($_SESSION);
if( $_GET['event'] == 'download_pdf' ){


	$query = "select * from kriya_schools where id = '".$_GET['record_id']."' ";
	$res1 = mysqli_query($connection,$query);
	if(mysqli_error($connection)){
		echo $query;
		echo mysqli_error($connection);
		exit;
	}
	$row1 = mysqli_fetch_assoc($res1);
	$query = "select * from kriya_options where school_id = '".$_GET['record_id']."' ";
	$res2 = mysqli_query($connection,$query);
	if(mysqli_error($connection)){
		echo $query;
		echo mysqli_error($connection);
		exit;
	}
	$options = array();
	while( $row = mysqli_fetch_assoc($res2) ){
		$options[ $row['item_id'] ] = $row;
	}
	
	$html .= "
	<html>
	<head>
	<style>
	body {font-family: sans-serif;
	    font-size: 8pt;
	}
	tbody td { vertical-align: top; line-height:25px;  }
	thead td { vertical-align: middle; }
	table.ddd td{ border:1px solid #cdcdcd; font-size:10pt; }	 
	</style>
	</head>
	<body>
	<center>
	<div id='registration_form_div' align='center' style=''>
	<table width='100%' cellpadding='5' cellspacing='1' border='0' style='border-collapse:collapse;' align='center'>
	<tr>
	<td>Registration No</td>
	<td>".$row1['id']."</td>
	</tr>
	<tr>
	<td>School Code</td>
	<td>".$row1['school_id']."</td>
	</tr>
	<tr>
	<td>School Name</td>
	<td>".$row1['school_name']."</td>
	</tr>
	<tr>
	<td>City</td>
	<td>".$row1['village_name']." , ".$row1['mandal_name'].$row1['district_name']."</td>
	</tr>
	<tr>
	<td>Contact Person</td>
	<td>".$row1['contact_person']."</td>
	</tr>
	<tr>
	<td>Email</td>
	<td>".$row1['email'] . "</td>
	</tr>
	<tr>
	<td>Phone 1</td>
	<td>".$row1['phone'] . "</td>
	</tr>
	<tr>
	<td>Phone 2</td>
	<td>".($row1['phone2']?$row1['phone2']:" - ")."</td>
	</tr>
	</table>
	</div>
	</center>
	<div style='height:30px;'></div>
	<table class='ddd' width='100%' border=1 style='font-size: 8pt; border-collapse: collapse;' cellpadding='3'>
	<tr valign='middle' bgcolor='#f8f8f8' style='font-weight:bold;'>
	<td class=\"col_\" align='center' width=\"40\" rowspan=\"2\" style=\"font-weight:bold;\" >Sno</td>
	<td class='hcol3' align='center' rowspan=\"2\" style=\"font-weight:bold;\" >Competition Name</td>
	<td colspan='3' align='center' style=\"font-weight:bold;\" >Allowed children / groups</td></tr>
	<tr valign='middle' bgcolor='#f8f8f8' style='font-weight:bold;'>
	<td width='60' align='center' style=\"font-weight:bold;\">Sub juniors</td>
	<td width='60' align='center' style=\"font-weight:bold;\">Juniors</td>
	<td width='60' align='center' style=\"font-weight:bold;\">Seniors</td></tr>";
	foreach ($config_categories as $key => $value) 
	{
		$key = (int)$key;
		$html .="<tr>
		<td class=\"col_\" align='right'>".$value['sno']."</td>
		<td class=\"col2\" width='250'>".$value['english']."</td>
		<td class=\"col3\" align='center'>";

		if( $value["enabled"][0] )
		{
			//print_r( $value["enabled"][0]);
			if( $value['group'] ){
			$html .="".($options[ $key ]['sub_jrs']?$options[ $key ]['sub_jrs']. "/" . $options[ $key ]['sub_jrs_cnt']:"")."";
			}else{
			$html .="".($options[ $key ]['sub_jrs']?$options[ $key ]['sub_jrs']:"")."";
			}
		}else{
			$html .="--";
		}
		$html .= "</td><td class=\"col4\" align='center'>";
		if( $value["enabled"][1] ){
			if( $value['group'] ){
			$html .="".($options[ $key ]['jrs']?$options[ $key ]['jrs']. "/". $options[ $key ]['jrs_cnt']:"")."";
			}else{
			$html .="".($options[ $key ]['jrs']?$options[ $key ]['jrs']:"")."";
			}
		}else{
			$html .="--";
		}
		$html .= "</td><td class=\"col5\" align='center'>";
		if( $value["enabled"][2] ){
			if( $value['group'] ){
			$html .="".($options[ $key ]['srs']?$options[ $key ]['srs']."/".$options[ $key ]['srs_cnt']:"")."";
			}else{
			$html .="".($options[ $key ]['srs']?$options[ $key ]['srs']:"")."";
			}
		}else{
			$html .="--";
		}
		$html .="</td>
		</tr>";
	}
	$html .="</table>";
	$html .="</div>";

	//echo $html;exit;
	include('mpdf60/mpdf.php');
	$mpdf=new mPDF('tel');
	$mpdf->WriteHTML($html);
	$mpdf->autoScriptToLang = true;
	$mpdf->autoLangToFont = true;
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->Output();
	exit;
} 
?>