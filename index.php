<?php
	session_start();

	$login_enable = true;
	if( $_SESSION['special2'] == "yes" ){
		$login_enable = true;
	}
        //$_SESSION['special2'] = "yes";  //   remove this to disable login.
	if( $_GET['enable'] == "special" ){
		$_SESSION['special2'] = "yes";
		header("Location: /?special_login_enabled");
		exit;
	}

	include('../config_global.php');
	include('db.php');
	include('config.php');
	//include('config_telugu_names.php');
	include("smtp_ses.php");
	include("router.php");
	include("actions.php");

	/*echo "<pre>";
	print_r( $config_categories );
	echo "</pre>";*/

	$config_school_types = array(
		'sub_jrs'=>[
			"Pr. Up Pr. and Secondary Only"=>1,
			"Pr. with Up.Pr. sec. and H.Sec."=>1,
			"primary"=>1,
			"Primary"=>1,
			"Primary with Upper Primary"=>1,
			"-"=>1,
		],
		'jrs'=>[
			"Pr. Up Pr. and Secondary Only"=>1,
			"Pr. with Up.Pr. sec. and H.Sec."=>1,
			"Primary with Upper Primary"=>1,
			"Secondary"=>1,
			"secondary"=>1,
			"Secondary Only"=>1,
			"Secondary School"=>1,
			"Secondary with Higher Secondary"=>1,
			"Senior Secondary"=>1,
			"Up. Pr. Secondary and Higher Sec"=>1,
			"Upper Pr. and Secondary"=>1,
			"Upper Primary only"=>1,
			"-"=>1,		
		],
		'srs'=>[
			"Pr. Up Pr. and Secondary Only"=>1,
			"Pr. with Up.Pr. sec. and H.Sec."=>1,
			"Primary with Upper Primary"=>1,
			"Secondary"=>1,
			"secondary"=>1,
			"Secondary Only"=>1,
			"Secondary School"=>1,
			"Secondary with Higher Secondary"=>1,
			"Senior Secondary"=>1,
			"Up. Pr. Secondary and Higher Sec"=>1,
			"Upper Pr. and Secondary"=>1,
			"Upper Primary only"=>1,
			"-"=>1,
		],		
	);

	if( !is_numeric($module) ){
		unset($module);
	}	

	if( $_SESSION['loggedin'] == "y" ){
		$school_res = mysqli_query( $connection, "select * from kriya_schools where id =" . $_SESSION["user_id"]);
		$data = mysqli_fetch_assoc( $school_res );
		if( !$data ){
			session_destroy();
			header("Location: /?event=SchoolNotFound");
			exit;
		}

		if( !$module ){
			header("Location: /" . $data['school_id'] . "?event=LoginFound");exit;
		}

		$school_res1 = mysqli_query( $connection, "select * from kriya_school_list where school_id =" . $data["school_id"]);
		$data1 = mysqli_fetch_assoc( $school_res1 );
		$data1['school_category'] = $data1['school_category']?$data1['school_category']:"-";
		$items_data = array();
		$item_res = mysqli_query( $connection,  "select * from kriya_options where school_id = " . $_SESSION['user_id'] );
		echo mysqli_error( $connection );
		while( $r = mysqli_fetch_assoc( $item_res ) ){
			$items_data[ $r['item_id'] ] = $r;
		}
		$selection = json_decode($data['selection'],true);
	}
?>
<html>
<head>
	<title>Kriya Registration Form</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<style>
		
	body { 	background-color: #2c95da; }
	* {
		font-size:14px;
		font-family:arial;
	}
	div,p,input,select{ padding:2px; }
	.input_div{ border:1px solid #f0a0a0;  line-height:25px; width:100%; background-color:white !important;  }
	.int{ border:1px solid #f0a0a0; border-radius:5px; line-height:25px; width:100%; background-color:white !important;  }
	//.btn{background-color: #4CAF50;border: none;color: white;margin: 4px 2px;cursor: pointer;}
	.btn:hover {background-color: #008CBA;color: white;}
	.btn {background-color: white;color: black;border: 2px solid #008CBA;}
	tbody td { vertical-align: top; line-height:25px;  }
	thead td { vertical-align: middle; }
	@media only screen and (min-width: 700px) {
		table.ddd td{ border-bottom:1px solid white; border-right:1px solid white; }
	}

	.sticky1{ position: sticky; top:0px; }
	.sticky2{ display:none; }
	
	@media only screen and (max-width: 700px) {
	
		body{ margin:0px; padding:0px; }
	    .hcol6{ display:none; }
	    thead td{ font-size:10px; }
	    #registration_form_div{ width:95% !important; padding:0px !important; border-radius:0px !important; }
	    .headcol1{ display:block; float:none !important; width:100% !important; }
	    .headcol1 img{ width:100% !important; }
	    .headcol2{ display:block; float:none !important; width:100% !important;  }
	    .headcol2 div{ font-size:24px !important; text-align: center; }
	    table.ddd { width:100%; margin:0px; }
	    table.ddd thead{ display:none; }
	    table.ddd tr{ display:block; border:1px solid #ccc; padding:5px; margin:5px; }
	    table.ddd td:first-Child{ width:10%; display:inline-block;  }
	    table.ddd td:nth-Child(2){ width:80%; display:inline-block; font-weight:bold; font-size:16px; }
		table.ddd td:nth-child(3), table.ddd td:nth-child(4), table.ddd td:nth-child(5){ display:inline-block; width:29%; border-right:1px solid #ccc; }
		table.ddd td:last-Child{ width:100%; display:block; }
		table.ddd .sticky2{ display:table-row; position: sticky; top:0px;  }
		table.ddd .sticky2 td{ display:inline-block; width:28%; border:0px !important; font-size:16px; }
	}
	
	select.sel{ font-size:16px; padding:3px; }

	</style>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" ></script>
</head>
<body>
<?php	
//echo "<pre>";print_r( $_SESSION );	echo "</pre>";
?>

	<script>
	config_categories = <?=json_encode( $config_categories ) ?>;
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
		function validate_school_code(){               //school_code
			school_code = document.getElementById("school_code").value;
			return true;
		}
		function check_email(vemail){
			vurl = "?action=check_email&email="+vemail;
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
	</script>
	<div class="logo" align="center">
		<img src="/kriya-head1.jpg" style=" max-width:100%;" >
	</div>
<?php	
if( $_GET['action'] == "logout" ){?>
	<center>
		<div style="margin:50px; padding: 20px; border:2px solid #ffef96; background-color:#ffef96; border-radius:10px; " >
			THANK YOU<BR><BR><BR><BR><BR>

			<a href='/?' >Login</a>
		</div>
	</center>
<?php	
}else if( $_SESSION['loggedin'] != "y" ){
	if( $module ){

		$school_res = mysqli_query( $connection, "select * from kriya_school_list where school_id ='" . $module . "' ");
		$data = mysqli_fetch_assoc( $school_res );
		
		if( !$data ){
			echo "<div align=center style='color:red; color:white; font-weight:bold; padding:20px; border:1px solid red;' >SCHOOL NOT FOUND!</div>";
			exit;
		}?>
		<div id='registration_form_div' align='left' style='width:100%;max-width:600px;border:2px solid white; background-color:#ffef96; border-radius:5px; padding:10px;margin:auto' >
		<?php	
			if($_GET["event"] == 1){
				echo "<div align='center'>";
				echo "<div style='color:red' >School Code doesnot match with given email and phone!</div>";
				echo "<div style='color:red'>Note: Specified school may have already nominated by other person! If you consider it improper please contact event organizer for corrections!</div>";
				echo "</div>";
			}else if($_GET["event"] == 2){
				echo "<div align='center'>";
				echo "<div style='color:red'>Given Email & Phone already used for nominating other school!</div>";
				echo "<div style='color:red'>Please use exactly the same email & phone details which were used at the first time.</div>";
				echo "<div style='color:red'>It is a measure of security. If you are having difficulty login, please contact event organizer for help!</div>";
				echo "</div>";
			}else{
				echo "<div style='color:red' align='center'>".$_GET["event"]."</div>";
			}
		?>
		<form method='post' >

			<?php if( $_GET['login'] == "expired" ){ ?>
				<p align="center" style="color:red;">Your login session expired. Please try again</p>
			<?php } ?>

			<table width='100%' cellpadding='5' cellspacing='1' border='0' style='border-collapse:collapse;' align='center'>
				<tr bgcolor='#ffef96'>
					<td align="right">School: </td>
					<td>
						<div style="margin-left:10px"><?=htmlspecialchars($data['school_name']) ?></div>
						<div style="margin-left:10px"><?=htmlspecialchars($data['village_name']) ?></div>
						<div style="margin-left:10px"><?=htmlspecialchars($data['district_name']) ?></div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td align="right"><?=htmlspecialchars($school_details['email']) ?>: </td>
					<td>
						<div style="margin-left:10px">
							<input type='text'  name='email' id='email_id' class="input_div" style="width:200px" required placeholder="Please Enter Email" autocomplete="off" value="<?=$_COOKIE['email'] ?>">
						</div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td align="right"><?=$school_details['pno'] ?>: </td>
					<td>
						<div style="margin-left:10px">
							<input type='number' name='phone' id='phone' class="input_div" style="width:200px"  required  placeholder="Please Enter Phone Number" autocomplete="off" value="<?=$_COOKIE['phone'] ?>" >
						</div>
					</td>
				</tr>
				<tr>
					<td align="right">Security: </td>
					<td>
						<div style="margin-left:10px"><img id="captchaimg" src="" ><a href="Javascript:reloadcaptcha()">Refresh</a></div>
					</td>
				</tr>
				<tr>
					<td align="right">Code: </td>
					<td>	
						<div style="margin-left:10px">
							<input type='text' value='' placeholder="Enter Above Code" required name='code'  id='code'  class="input_div" style="width:200px" autocomplete="off">
						</div>
					</td>
				</tr>
				<tr id="otp_send_div">
					<td align="right">&nbsp;</td>
					<td>
						<div style="margin-left:10px">
							<input type='button' value='Send OTP' class="input_div" onclick="sendotp(this)" style="cursor:pointer;width:initial;">
							<div id="otp_msg" style='color:blue;' ></div>
							<div id="email_error_code" style="color:red; padding:5px;" ></div>
						</div>
					</td>
				</tr>
				<tr id="otp_div" style="display:none;">
					<td align="right">Enter OTP: </td>
					<td>
						<div style="margin-left:10px">
							<input type='text' value='' placeholder="Enter email OTP" required name='email_otp'  id='email_otp'  class="input_div" style="width:200px" autocomplete="off">
							<div>An OTP has been sent to your email address. If you did not get your otp, check your email address for typo mistakes and try again.</div>
						</div>
					</td>
				</tr>
				<tr id="login_div" style="display:none;">
					<td align="right">&nbsp;</td>
					<td>
						<div style="margin-left:10px">
							<input type='button' value='Login' class="input_div" onclick="dologin()" style="cursor:pointer;width:50px">
						</div>
						<div id="error_code" style="color:red; padding:10px;" ></div>
					</td>
				</tr>
			</table>
			<input type='hidden' value='login' name='action' >
			<input type='hidden' value='' name='captcha_code' id='captcha_code' >
			<input type='hidden' value='<?=$module?>' name='school_code' >
		</form>
			<p align='center' style="border-top:1px solid #aaa;padding:10px;"><span style="font-size:18px;" >If it is not your school. </span><BR>Please go back and search again with correct UDISE code. <BR><a href='/?' >Click here to go Back</a></p>
			<p align='center' style="border-top:1px solid #aaa;padding:10px;"><span style="font-size:18px;" >Having problem with Email/Phone?</span><BR>Please do not hesitate to call the organizer for help.</p>
		</div>	
		<script>
			function reloadcaptcha(){
				document.getElementById("code").value = "";
				var con = new XMLHttpRequest();
				con.open("GET", "?action=getcaptcha", true );
				con.onload = function(v){
					var v= JSON.parse(this.responseText);
					document.getElementById("captchaimg").src = v['img'];
					document.getElementById("captcha_code").value = v['code'];
				};
				con.send();
			}
			function dologin(){

				var email = document.getElementById("email_id").value.trim().toLowerCase();
				var phone = document.getElementById("phone").value.trim().toLowerCase();
				var code = document.getElementById("code").value.trim();
				var otp = document.getElementById("email_otp").value.trim();

				if( email.match(/^[a-z0-9\-\.\_]{2,50}\@[a-z0-9\-\.\_]{2,50}\.[a-z\.]{2,10}$/)  == null ){
					alert("Enter proper email");return false;
				}
				if( phone.match(/^[9876][0-9]{9}/)  == null ){
					alert("Enter proper phone number");return false;
				}
				if( code == "" ){
					alert("Enter security code");return false;
				}
				if( otp.match(/^[0-9]{5,8}/)  == null ){
					alert("Enter proper otp");return false;
				}

				document.getElementById("error_code").innerHTML  = 'Connecting...';

				vpostdata = "email="+encodeURIComponent(email);
				vpostdata = vpostdata + "&phone="+encodeURIComponent(phone);
				vpostdata = vpostdata + "&captcha_code="+encodeURIComponent(document.getElementById("captcha_code").value);
				vpostdata = vpostdata + "&code="+encodeURIComponent(code);
				vpostdata = vpostdata + "&school_code="+encodeURIComponent(`<?=$module?>`);
				vpostdata = vpostdata + "&email_otp="+otp;
				vpostdata = vpostdata + "&action=login";
				var con = new XMLHttpRequest();
				con.open("POST", "?", true );
				con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
				con.onload = function(v){
					var v= JSON.parse(this.responseText);
					if( v['status'] == "fail" ){
						if( v['error'] == "SchoolDuplicate" ){
							document.getElementById("error_code").innerHTML = `School Code does not match with given email and phone!
							<BR>Note: Specified school may have already nominated by other person! <BR>If you consider it improper, please contact event organizer for corrections!`;
						}else if( v['error'] == "ContactDuplicate" ){
							document.getElementById("error_code").innerHTML = `Given Email & Phone already used for nominating other school!<br>
							Please use exactly the same email & phone details which were used at the first time.<BR>
							It is a measure of security. If you are having difficulty login, please contact event organizer for help!`;
						}else if( v['error'] == "SchoolNotFound" ){
							document.getElementById("error_code").innerHTML  = 'School not found. Reloading...';
							document.location = "?event=SchoolNotFound";
						}else{
							document.getElementById("error_code").innerHTML  = v['error'].replace("\n", "<BR>");
							setTimeout(alert,500,v['error']);
						}
						reloadcaptcha();
					}else{
						document.getElementById("error_code").innerHTML  = 'Success';
						document.location = "?event=LoginSuccess";
					}
				};
				con.send(vpostdata);
			}
			function sendotp(v){

				var email = document.getElementById("email_id").value.trim().toLowerCase();
				var phone = document.getElementById("phone").value.trim().toLowerCase();
				var code = document.getElementById("code").value.trim();

				v.value = "Resend OTP";

				if( email.match(/^[a-z0-9\-\.\_]{2,50}\@[a-z0-9\-\.\_]{2,50}\.[a-z\.]{2,10}$/)  == null ){
					alert("Enter proper email");return false;
				}
				if( phone.match(/^[9876][0-9]{9}/)  == null ){
					alert("Enter proper phone number");return false;
				}
				if( code == "" ){
					alert("Enter security code");return false;
				}

				document.getElementById("otp_msg").innerHTML  = 'Connecting...';
				document.getElementById("email_error_code").innerHTML = "";

				vpostdata = "email="+encodeURIComponent(email);
				vpostdata = vpostdata + "&phone="+encodeURIComponent(phone);
				vpostdata = vpostdata + "&captcha_code="+encodeURIComponent(document.getElementById("captcha_code").value);
				vpostdata = vpostdata + "&code="+encodeURIComponent(code);
				vpostdata = vpostdata + "&school_code="+encodeURIComponent(`<?=$module?>`);
				vpostdata = vpostdata + "&action=sendotp";
				var con = new XMLHttpRequest();
				con.open("POST", "?", true );
				con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
				con.onload = function(v){
					document.getElementById("otp_msg").innerHTML = "";
					var v= JSON.parse(this.responseText);
					if( v['error'] == "SchoolDuplicate" ){
						document.getElementById("email_error_code").innerHTML = `School Code does not match with the given email!
							<BR>Note: Specified school may have already nominated by other person! <BR>If you consider it improper, please contact event organizer for corrections!`;
					}else if( v['error'] == "ContactDuplicate" ){
						document.getElementById("email_error_code").innerHTML = `Given Email & Phone already used for nominating other school!<br>
							Please use exactly the same email & phone details which were used at the first time.<BR>
							It is a measure of security. If you are having difficulty login, please contact event organizer for help!`;
					}else if( v['status'] == "fail" ){
						document.getElementById("otp_msg").innerHTML  = v['error'];
						setTimeout(alert,500,v['error']);
						reloadcaptcha();
					}else if( v['status'] == "OTPSent" ){
						document.getElementById("otp_msg").innerHTML = "OTP Sent to your email address";
						document.getElementById("otp_div").style.display = 'table-row';
						document.getElementById("login_div").style.display = 'table-row';
					}else{
						document.getElementById("error_code").innerHTML  = 'Success';
					}
				};
				con.send( vpostdata );
			}
			setTimeout("reloadcaptcha()",2000);
		</script>

<?php }else{ ?>

	<form method="post" onsubmit="return validate_school_code()">
		<div align="center" style=" border:2px solid white; padding:20px; margin:auto" >

			<?php 
			if( $_SESSION['special2'] == "yes" || $login_enable ){ ?>
		
				<div  align='center' style="font-size:40px;color:white;">ఆన్-లైన్ రిజిస్ట్రేషన్</div>
			
				<?php	if($_GET["event"] == "failed"){ ?>
				<div style='text-align:center;color:red;'>School not found!</div>
				<?php } ?>
				
				<table align="center" style="max-width:300px;" >
					<tr>
						<td><input type="number" name="school_code" id="school_code" class="input_div" style="width:200px;padding:5px;"  placeholder="Enter School UDISE Code" required autocomplete="off" ></td>
						<td><input type="submit" value="LOGIN" class="input_div" style="width:80px;padding:5px;" ></td>
					</tr>
				</table>
				<div id="school_code_div" align="center"></div>
				<input type='hidden' value='check_school_code' name='action' > 

			<?php }else{ ?>

			<div  align='center' style="font-size:40px;color:white;">REGISTRATION</div>
			<p style='text-align:center; font-size:30px; color:white;'>CLOSED</p>

			<?php } ?>
		</div>
	</form>
	<center>
	<p style='color:white; font-weight:bold;'>Please check all the conditions and age groups before submitting.</p>
	<p style='color:white; font-weight:bold;'>Last date for submission and corrections 22nd November 2023. Max 60 members are allowed from a school.</p>
	</center>
	<div align="center">
		<img src="/kriya-head2.jpg" style="max-width:100%;" >
	</div>
			
<?php } ?>
<?php
}else{
	if( $_GET['show'] == "thanks" ){?>
	<div style='width:300px;border:2px solid #ffef96; background-color:#ffef96; border-radius:10px; padding:10px;margin:auto;text-align:center' >
		Information Saved<BR><BR>Thank you for participation<BR><BR>
		<BR><BR>Your registration number: <BR><BR>
		<strong><span style='font-size:16px; color:#ff790e;' ><?=str_pad($_SESSION['user_id'], 3, "0", STR_PAD_LEFT) ?></span></strong>

		<BR><BR><a href='?event=recheck' >Recheck Submission</a><BR><BR><BR>
		<a href='?action=logout' >Logout</a><BR><BR>
	</div>
<?php }else if( $module && $param1 == "review" ){ ?>
	<p>Review</p>
	<?php require("index_review.php"); ?>
<?php }else{ ?>
	<form method='post' onsubmit="return validate_form()" >
		<div id='registration_form_div' align='left' style='width:100%;max-width:100%;border:2px solid white; background-color:#ffef96; border-radius:5px; padding:10px; margin:auto;' >
			<div align="right" style="padding-right:50px;"><a href='?action=logout' >Logout</a><BR><BR></div>
			<table width='100%' cellpadding='5' cellspacing='1' border='0' style='border-collapse:collapse;' align='center'>
				<tr bgcolor='#ffef96'>
					<td align="right">School:</td>
					<td>
						<div style="margin-left:10px"><?=htmlspecialchars($data1['school_name']) ?></div>
						<div style="margin-left:10px"><?=htmlspecialchars($data1['school_category']) ?></div>
						<div style="margin-left:10px"><?=htmlspecialchars($data['email']) ?>, <?=htmlspecialchars($data['phone']) ?></div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td align="right">Teacher Name:</td>
					<td>
						<div style="margin-left:10px"><input type='text'  class="input_div"  value="<?=(htmlspecialchars($data['contact_person']) )?>" name='teacher_name' id='teacher_id' required autocomplete="off" style="width:200px"></div>
					</td>
				</tr>
				<tr>
					<td align="right">Phone 2:</td>
					<td><div style="margin-left:10px"><input type='number' class="input_div"  value="<?=($data['phone2']) ?>" name='phone2' id='pho2' style="width:200px" autocomplete="off"></div></td>
				</tr>
				<tr>
					<td align="right">Count of teachers accompanied</td>
					<td>
						<div style="margin-left:10px">
							<input type='number' name='total_teachers'  id='total_teachers_id' class="input_div" style='width:60px;' value="<?=$data['total_teachers']?>"  autocomplete="off">
						</div>
					</td>
				</tr>
			<?php if( $data1["district_code"]!=2814 ){ ?>
				<tr  bgcolor='#ffef96' >
					<td align="right">Need accommodation?:</td>
					<td>
						<div style="margin-left:10px"><input type='checkbox' name='accommodation'  id='accommodation_id' style='width:15px; height:15px;' value="y" <?=$data['accommodation']?"checked":""?> ></div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td colspan='2' align='center' style='font-size:10px;color:gray;'>above details required for food preparation.</td>
				</tr>
			<?php	} ?>
			</table>
			<input type='hidden' name='total_students'  id='total_students_id' value="<?=$data['total_students'] ?>" >
	</div>	
	<center>
		<p style='color:white; font-weight:bold;'>Please check all the conditions and age groups before submitting.</p>
		<p style='color:white; font-weight:bold;'>Last date for submission and corrections 22nd November 2023. Max 60 members are allowed from a school.</p>
	</center>
	<table class="ddd satfixed" border=0 style="border-collapse:collapse;" width="100%" cellpadding='5' cellspacing='1'>
		<thead class="sticky1">
			<tr valign='middle' bgcolor='#3B5998' style='color:white;font-weight:bold;'>
				<td class="col_"  align='center' width="40" rowspan="2" >ID</td>
				<td class='hcol3' align='center' rowspan="2"  >Category</td>
				<td colspan='3' align='center' >Persons/Groups</td>
				<td class='hcol6' align='center' rowspan="2" >Rules</td>
			</tr>
			<tr valign='middle' bgcolor='#3B5998' style='color:white;font-weight:bold;'>
				<td width='60'align='center'>Sub Junior</td>
				<td width='60' align='center'>Junior</td>
				<td width='60' align='center'>Senior</td>
			</tr>
		</thead>
		<thead class="sticky2">
			<tr bgcolor='#3B5998' style='color:white;font-weight:bold;'>
				<td width='30' align='center'>Sub Junior</td>
				<td width='30' align='center'>Junior</td>
				<td width='30' align='center'>Senior</td>
			</tr>
		</thead>
		<tbody>
	<?php	foreach ($config_categories as $key => $value) {
			$key = (int)$key;
			$vshow = false;			
			if( ($value["enabled"][0] && $config_school_types[ 'sub_jrs' ][ $data1['school_category'] ]) || ($value["enabled"][1] && $config_school_types[ 'jrs' ][ $data1['school_category'] ])  ||  ($value["enabled"][2] && $config_school_types[ 'srs' ][ $data1['school_category'] ] ) ){
				$vshow = true;
			}
			if( $vshow || true){
			?>
			<tr bgcolor='#ffef96'>
				<td class="col_" align='right'><?=$value['sno'] ?></td>
				<td class="col2" width='250'><?=$value['name'] ?></td>
				<td class="col3" align='center' nowrap >
				<?php	if( $value["enabled"][0] && $config_school_types[ 'sub_jrs' ][ $data1['school_category'] ] ){ ?>

						<?php if($value['group']){ for($k=1;$k<=$value['enabled'][0];$k++){ ?>
							<div><?=$value['enabled'][0]>1?"Group ". $k .": ":"Group: " ?> 
						<select title="Select Number of Students in the Group" class="sel" name='stu[<?=$key?>][sub_jrs][<?=$k-1 ?>]' id='sub_jrs_<?=$key ?>_<?=$k-1 ?>' data-max='<?=$value['max'][0]?>' onchange="validate_this(this)" >
							<option value='0'>-</option>";
							<?php 
							for($kk=$value['max'][0][0];$kk<=$value['max'][0][1];$kk++){
								echo "<option ".($selection[$key]['sub_jrs'][$k-1]==$kk?"selected":""). " value='".$kk."'>".$kk."</option>";
							}
							?>
						</select>
							</div>
						<?php }}else{ ?>
						<select class="sel" name='stu[<?=$key?>][sub_jrs][0]' id='sub_jrs_<?=$key ?>_0' data-max='<?=$value['max'][0]?>' onchange="validate_this(this)" >
							<option value='0'>-</option>";
							<?php 
							for($kk=$value['max'][0][0];$kk<=$value['max'][0][1];$kk++){
								echo "<option ".($selection[$key]['sub_jrs'][0]==$kk?"selected":""). " value='".$kk."'>".$kk."</option>";
							}
							?>
						</select>
						<?php } ?>
				<?php	}else{ ?>
						<div title="not available for <?=$data1['school_category'] ?> and in sub juniors">-</div>
				<?php	} ?>
				</td>
				<td class="col4" align='center' nowrap >
				<?php	if( $value["enabled"][1] && $config_school_types[ 'jrs' ][ $data1['school_category'] ] ){   ?>
						<?php if($value['group']){ for($k=1;$k<=$value['enabled'][1];$k++){ ?>
							<div><?=$value['enabled'][1]>1?"Group ". $k .": ":"Group: " ?> 
						<select title="Select Number of Students in the Group"  class="sel"  name='stu[<?=$key?>][jrs][<?=$k-1 ?>]' id='jrs_<?=$key ?>_<?=$k-1 ?>' data-max='<?=$value['max'][1]?>'  onchange="validate_this(this)">
							<option value='0'>-</option>";
							<?php 
							for($kk=$value['max'][1][0];$kk<=$value['max'][1][1];$kk++){
								echo "<option ".($selection[$key]['jrs'][$k-1]==$kk?"selected":""). " value='".$kk."'>".$kk."</option>";
							}
							?>
						</select></div>
						<?php }}else{ ?>
						<select class="sel"  name='stu[<?=$key?>][jrs][0]' id='jrs_<?=$key ?>_0' data-max='<?=$value['max'][1]?>'  onchange="validate_this(this)">
							<option value='0'>-</option>";
							<?php 
							for($kk=$value['max'][1][0];$kk<=$value['max'][1][1];$kk++){
								echo "<option ".($selection[$key]['jrs'][0]==$kk?"selected":""). " value='".$kk."'>".$kk."</option>";
							}
							?>
						</select>
						<?php } ?>					
				<?php	}else{?>
						<div title="not available for <?=$data1['school_category'] ?> and in juniors ">-</div>
				<?php	}?>
				</td>
				<td class="col5" align='center' nowrap >
				<?php	if( $value["enabled"][2] && $config_school_types[ 'srs' ][ $data1['school_category'] ] ){   ?>
						<?php if($value['group']){ for($k=1;$k<=$value['enabled'][2];$k++){ ?>
							<div><?=$value['enabled'][2]>1?"Group ". $k .": ":"Group: " ?> 
						<select title="Select Number of Students in the Group"  class="sel"  name='stu[<?=$key?>][srs][<?=$k-1 ?>]' id='srs_<?=$key ?>_<?=$k-1 ?>' data-max='<?=$value['max'][2]?>'  onchange="validate_this(this)">
							<option value='0'>-</option>";
							<?php 
							for($kk=$value['max'][2][0];$kk<=$value['max'][2][1];$kk++){
								echo "<option ".($selection[$key]['srs'][$k-1]==$kk?"selected":""). " value='".$kk."'>".$kk."</option>";
							}
							?>
						</select></div>
						<?php }}else{ ?>
						<select class="sel"  name='stu[<?=$key?>][srs][0]' id='srs_<?=$key ?>_0' data-max='<?=$value['max'][2]?>'  onchange="validate_this(this)">
							<option value='0'>-</option>";
							<?php 
							for($kk=$value['max'][2][0];$kk<=$value['max'][2][1];$kk++){
								echo "<option ".($selection[$key]['srs'][0]==$kk?"selected":""). " value='".$kk."'>".$kk."</option>";
							}
							?>
						</select>
						<?php } ?>
				<?php	}else{?>
						<div title="not available for <?=$data1['school_category'] ?> and for seniors ">-</div>
				<?php	}?>
				</td>
				<td class="col6" width='450'><?=$value['details'] ?></td>
			</tr>
			<?php } ?>
	<?php } ?>	
		</tbody>
	</table>
	<div align='center' id="footer_msg" style='padding:10px;position:fixed; left:0px; bottom:0px; width:100%; background-color:rgba(0,0,0,0.8);' align=center >
		<div style="display:inline-block; font-size:18px; color:white; margin-right:50px;" >Total Students:  
		<span style='font-weight:bold; font-size:18px; ' id="total_students_div" ><?=$data['total_students'] ?></span>
		</div> 
		<input type='submit' value='Save Details &amp; Confirm Participation' name='Register' id="register_btn" class="input_div" style=' padding:5px; cursor:pointer; margin:10px;width:300px !important; font-weight:bold;'>
	</div>
	<input type='hidden' name='action' value='register'>
	<input type='hidden' name='school_id' value='<?=$module ?>'>
</div>
	</form>
	<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
	<script>
		//cnt = Number(document.getElementById("total_students_id").value);
		function validate_this( vobj ){
			cnt = Number(document.getElementById("total_students_id").value)+Number(vobj.value);
			if( cnt > 60 ){
				vobj.value = "0";
			}
			find_total();
		}
		function find_total(){
			var cnt=0;
			for(i in config_categories){
				c1=0;c2=0;c3=0;
				for(j=0;j<5;j++){
					try{
					if( document.getElementById("sub_jrs_"+i+"_"+j) != undefined ){
						c1 = c1+Number(document.getElementById("sub_jrs_"+i+"_"+j).value);
					}
					}catch(e){console.log("c1:"+e);}
				}
				for(j=0;j<5;j++){
					try{
					if( document.getElementById("jrs_"+i+"_"+j) != undefined ){
						c2 = c2+Number(document.getElementById("jrs_"+i+"_"+j).value);
					}
					}catch(e){console.log("c2:"+e);}
				}
				for(j=0;j<5;j++){
					try{
						if( document.getElementById("srs_"+i+"_"+j) != undefined ){
							c3 = c3+Number(document.getElementById("srs_"+i+"_"+j).value);
						}
					}catch(e){console.log("c3:"+e);}
				}
				cnt = Number(cnt + c1 + c2 + c3);
			}
			document.getElementById("total_students_id").value = cnt;
			document.getElementById("total_students_div").innerHTML = cnt;
		}
		function validate_form(){
			v = document.getElementById("teacher_id").value;
			if( v.match(/^[a-zA-Z0-9\ \.\-\_\,\(\)]{3,50}$/) == null ){
				alert("Enter Teacher/Contact Person Name\n\nspecial characters not accepted!");
				return false;
			}
			if( Number(document.getElementById("total_students_id").value) < 1 ){
				alert("You have not selected any studnets for participation!");
				return false;
			}
			if( Number(document.getElementById("total_students_id").value) > 60 ){
				alert("Number of students per school is limited\n\nPlease keep your participation size not more than 60 students.");
				return false;
			}
			$("#register_btn").val("Saving Information");
			return true;
		}
	</script>






<?php	}
}

?>	







</body>
</html>	
