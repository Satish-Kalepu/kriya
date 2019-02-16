<?php
session_start();
include('db.php');
include('config.php');
include("actions.php");

require("captcha/simple-php-captcha.php");
$_SESSION['captcha'] = simple_php_captcha();

if( $_SESSION['loggedin'] == "y" ){
	$res = mysqli_query( $connection, "select * from kriya_schools where id = " . $_SESSION['user_id']);
	$data = mysqli_fetch_assoc( $res );
	if( !$data ){
		session_destroy();
		header("Location: ?error=DataError");
		exit;
	}

	$items_data = array();
	$q = "select * from kriya_options where school_id = " . $_SESSION['user_id'];
	//echo $q;
	//exit;
	$res = mysqli_query( $connection, $q );
	echo mysqli_error( $connection );
	while( $r = mysqli_fetch_assoc( $res ) ){
		$items_data[ $r['item_id'] ] = $r;
	}
	//echo "<pre>";
	//print_r( $data );
	//exit;

}
echo "<pre>";
print_r( $_SESSION );
exit;

?><html>
<head>
<title>
Kriya Registration Form
</title>
<style>
body { 	background-color: #4285F4; }
* {
	font-size:14px;
	font-family:arial;
	padding:2px;
}
input { border:1px solid white; border-radius:5px; line-height:25px; width:100%; background-color:white !important;  }
tbody td { vertical-align: top; line-height:25px;  }
thead td { vertical-align: middle; }
table.ddd td{ border-bottom:1px solid white; border-right:1px solid white; }

@media only screen and (max-width: 700px) {

	body{ margin:0px; padding:0px; }
    .hcol6{ display:none; }
    .col6, .col_ {
        display:none;
    }
    thead td{ font-size:10px; }
    #registration_form_div{ width:95% !important; padding:0px !important; border-radius:0px !important; }
    .headcol1{ display:block; float:none !important; width:100% !important; }
    .headcol1 img{ width:100% !important; }
    .headcol2{ display:block; float:none !important; width:100% !important;  }
    .headcol2 div{ font-size:24px !important; text-align: center; }
    table.ddd { width:95% !important; margin:0px; }
}

</style>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
</head>
<body>
<script>
function get_request( vurl, vcallback ) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     	vcallback( this.responseText );
    }
  };
  xhttp.open("GET", vurl, true);	
  xhttp.send();
}
function check_email(vemail)
{
	vurl = "actions.php?action=check_email&email="+vemail;
	get_request( vurl, email_availability_status );

}
var is_email_available = false;
function email_availability_status( vdata ){
	//alert(vdata);
	eval("response="+vdata);
	if( response["status"] == "found")
	{
		is_email_available = true;
		document.getElementById("email_message").innerHTML = "An account is already exists with this email.";
		document.getElementById("email_message").style.color='red';
	}
} 
function validate_this( vobj ){
	if( Number(vobj.value) < 1 ){
		vobj.value = "";
		return;
	}
	if( Number(vobj.getAttribute("data-max")) < Number(vobj.value) ){
		vobj.value= vobj.getAttribute("data-max");
	}
}
</script>
<div class="logo">
	<div class="headcol1" style="border:0px solid black;width:40%;float:left;">
		<img src="http://www.kriyaonline.org/img/logo_kriya.png" >
	</div>
	<div class="headcol2"  style="border:0px solid black;width:40%;height:120;float:right;">
		<div style="font-size:40px;color:white;padding:5px;margin-top:30px;">ఆన్-లైన్ రిజిస్ట్రేషన్</div>
	</div>
	<div style="clear:both;"></div>
</div>
<?php

if( $_SESSION['loggedin'] != "y" ){
	?>
	<center>
		<div align="left" style="width:300px; border:2px solid #ffef96; background-color:#ffef96; border-radius:10px;" >

			<form method='post' >
			<table width="100%" >
			<tr>
			<td>Email</td>
			<td><input type='email' value='<?=$_SESSION['post']['email'] ?>' placeholder="Enter Email" required name='email'></td>
			</tr>
			<tr>
			<td>Phone</td>
			<td><input type='number' value='<?=$_SESSION['post']['phone'] ?>'  placeholder="Enter Phone" required name='phone'></td>
			</tr>
			<tr>
				<td>Security</td>
				<td><img src="<?=$_SESSION['captcha']['image_src'] ?>" ></td>
			<tr>
			<tr>
				<td>Code</td>
				<td><input type='text' value='' placeholder="Enter Above Code" required name='code'></td>
			<tr>
			<td>&nbsp;</td>
			<td><input type='submit' value='Login' name='btn' style='cursor:pointer;' ></td>
			</tr>
			</table>
			<input type='hidden' value='login' name='action' >
			</form>
		<?php 
		if( $_GET['event'] ){
			echo "<div align='center' style='color:red; font-size:14px;'>" . $_GET['event'] . "</div>";
		}
		?>

		</div>
	</center>
	<?php

}else if( $_GET['show'] == "thanks" ){
	?>

	<center>
	<div style='width:300px;border:2px solid #ffef96; background-color:#ffef96; border-radius:10px; padding:10px;' >
		Information Saved<BR><BR>Thank you for participation<BR><BR><a href='?event=recheck' >Recheck Submission</a>
	</div>
	</center>

	<?php

}else{

	?>

	<?php

	echo "<center>";

	echo "<form method='post' >";
	echo "<div id='registration_form_div' align='left' style='width:400px;border:2px solid #ffef96; background-color:#ffef96; border-radius:10px; padding:10px;' >";
	echo "<table width='100%' cellpadding='5' cellspacing='1' border='0' style='border-collapse:collapse;' align='center'>";
		
		echo "<tr bgcolor='#ffef96'>";
		echo "<td>".$school_details['school_name']."</td>";
		echo "<td><input type='text' value=\"" . $data['school_name'] . "\" name='school_name' id='school_id'></td>";
		echo "</tr>";

		echo "<tr bgcolor='#ffef96'>";
		echo "<td>".$school_details['village_name']."</td>";
		echo "<td><input type='text' value=\"".$data['city'] . "\" name='village_name' id='village_id'></td>";
		echo "</tr>";

		echo "<tr bgcolor='#ffef96'>";
		echo "<td>".$school_details['district_name']."</td>";
		echo "<td><input type='text' value=\"".$data['district'] . "\" name='district_name' id='district_id'></td>";
		echo "</tr>";

		echo "<tr bgcolor='#ffef96'>";
		echo "<td>".$school_details['teacher_name']."</td>";
		echo "<td><input type='text' value=\"".$data['contact_person'] . "\" name='teacher_name' id='teacher_id'></td>";
		echo "</tr>";

		echo "<tr bgcolor='#ffef96'>";
		echo "<td>".$school_details['email']."</td>";
		echo "<td>".$data['email'] . "</td>";
		echo "</tr>";

		echo "<tr bgcolor='#ffef96'>";
		echo "<td>".$school_details['pno']."</td>";
		echo "<td>".$data['phone'] . "</td>";
		echo "</tr>";

		echo "<tr bgcolor='#ffef96'>";
		echo "<td>".$school_details['pno2']."</td>";
		echo "<td><input type='text' value=\"".$data['phone2']  . "\" name='phone2' id='pho2'></td>";
		echo "</tr>";

	echo "</table>";

	echo "</div>";
	echo "</center>";


	echo "<div style='height:40px;'></div>";
	//print_r( $school_details);
	echo "<table class='ddd' border=0 style=\"border-collapse:collapse;\" width=\"100%\" cellpadding='5' cellspacing='1'>";
	echo "<thead>";

	echo "<tr valign='middle' bgcolor='#3B5998' style='color:white;font-weight:bold;'>";
	echo "<td class=\"col_\"  align='center' width=\"40\" rowspan=\"2\" >క్రమ సంఖ్య</td>
	<td align='center' width='50' rowspan=\"2\"  >సమయం</td>
	<td class='hcol3' align='center' rowspan=\"2\"  >పోటీ పేరు</td>
	<td colspan='3' align='center' >అనుమతించబడే పిల్లలు/గ్రూపులు</td>
	<td class='hcol6' align='center' rowspan=\"2\" >నియమాలు</td>
	</tr>";

	echo "<tr valign='middle' bgcolor='#3B5998' style='color:white;font-weight:bold;'>

	<td width='60'align='center'>సబ్ జూనియర్స్</td>
	<td width='60' align='center'>జూనియర్స్</td>
	<td width='60' align='center'>సీనియర్స్</td>
	";
	echo "</tr>";
	echo "</thead><tbody>";
	foreach ($config_categories as $key => $value) 
	{
		$key = (int)$key;
		echo "<tr bgcolor='#ffef96'>";
		echo "<td class=\"col_\" align='right'>".$value['sno']."</td>
		<td class=\"col1\" align='center'>".$value['time']."</td>
		<td class=\"col2\" width='250'>".$value['name']."</td>
		<td class=\"col3\" align='center'>";
		if( $value["enabled"][0] )
		{
			//print_r( $value["enabled"][0]);
			echo "<input style='background-color:#ffef96;width:60px !important;' type='number' value=\"".$items_data[$key]['sub_jrs'] . "\" name='stu[".$key."][sub_jrs]' id='sub_jrs_id' data-max='".$value['max'][0]."' onchange='validate_this(this)' >";
		}else{
			echo "--";
		}
		echo "</td><td class=\"col4\" align='center'>";
		if( $value["enabled"][1] ){
			echo "<input style='background-color:#ffef96;width:60px !important;' type='number' value=\"".$items_data[$key]['jrs'] . "\" name='stu[".$key."][jrs]' id='jrs_id' data-max='".$value['max'][1]."' onchange='validate_this(this)'  >";
		}else{
			echo "--";
		}
		echo "</td><td class=\"col5\" align='center'>";
		if( $value["enabled"][2] ){
			echo "<input  style='background-color:#ffef96;width:60px !important;' type='number' value=\"".$items_data[$key]['srs'] . "\" name='stu[".$key."][srs]' id='srs_id' data-max='".$value['max'][2]."' onchange='validate_this(this)'  >";
		}else{
			echo "--";
		}
		echo "</td>
		<td class=\"col6\" width='450'>".$value['details']."</td>";
		echo "</tr>";
	}
	echo "</tbody></table>";
	echo "<div align='center' style='padding:5px';>
	<input type='submit' value='Confirm Participation' name='Register' style=' margin:20px; padding:10px; cursor:pointer;	 width:200px !important;'></div>";
	echo "<div><input type='hidden' name='action' value='register'></div>";
	echo "</form>";
	echo "</div>";

}
?>