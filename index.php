<?php
	session_start();
	include('db.php');
	include('config.php');
	include("actions.php");
	include("router.php");
	require("captcha/captcha.php");

	/*echo "<pre>";
	print_r( $config_categories );
	echo "</pre>";*/
	$_SESSION['special'] = "yes";
	if( $_GET['enable'] == "special" ){
		$_SESSION['special'] = "yes";
		header("Location: ".$site_path."?special_login_enabled");
		exit;
	}

	if( !is_numeric($module) ){
		unset($module);
	}	

        if( $_SESSION['loggedin'] != "y" ){
		if( $module ){
			if( is_numeric($module) ){
				if( $module != $_SESSION['school_id'] ){
					header("Location: ".$site_path."?retry");exit;
				}
			}
		}
	}

	if( $_SESSION['loggedin'] == "y" ){
		$school_res = mysqli_query( $connection, "select * from kriya_schools where id =" . $_SESSION["user_id"]);
		$data = mysqli_fetch_assoc( $school_res );
		$school_res1 = mysqli_query( $connection, "select * from kriya_school_list where school_id =" . $data["school_id"]);
		$data1 = mysqli_fetch_assoc( $school_res1 );
		$items_data = array();
		$item_res = mysqli_query( $connection,  "select * from kriya_options where school_id = " . $_SESSION['user_id'] );
		echo mysqli_error( $connection );
		while( $r = mysqli_fetch_assoc( $item_res ) ){
			$items_data[ $r['item_id'] ] = $r;
		}
		$selection = json_decode($data['selection'],true);
		/* echo "<pre>";
		 print_r($items_data);
                 print_r($selection);//exit;
                 print_r($data1);//exit;
                 print_r($data);exit;*/ 
	}
	
?>
<!DOCTYPE html>
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
		<img src="<?=$site_path?>kriya_9.jpg" style="width:100%; max-width:1248px;" >
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
	if($module){
	
		
	
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
			$_SESSION['captcha'] = simple_php_captcha();
		?>
		<form method='post' >
			<table width='100%' cellpadding='5' cellspacing='1' border='0' style='border-collapse:collapse;' align='center'>
				<tr bgcolor='#ffef96'>
					<td align="right"><?=$school_details['school_name']?> : </td>
					<td>
						<div style="margin-left:10px"><?=$data['school_name']?></div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td align="right"><?=$school_details['village_name']?> : </td>
					<td>
						<div style="margin-left:10px"><?=$data['village_name']?></div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td align="right"><?=$school_details['district_name']?> : </td>
					<td>
						<div style="margin-left:10px"><?=$data['district_name']?></div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td align="right"><?=$school_details['email']?> : </td>
					<td>
						<div style="margin-left:10px">
							<input type='text'  name='email' id='email_id' class="input_div" style="width:200px" required placeholder="Please Enter Email" autocomplete="off" value="<?=$_COOKIE['email'] ?>">
						</div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td align="right"><?=$school_details['pno']?> : </td>
					<td>
						<div style="margin-left:10px">
							<input type='number' name='phone' id='phone' class="input_div" style="width:200px"  required  placeholder="Please Enter Phone Number" autocomplete="off" value="<?=$_COOKIE['phone'] ?>" >
						</div>
					</td>
				</tr>
				<tr>
					<td align="right">Security : </td>
					<td>
						<div style="margin-left:10px"><img id="captchaimg" src="<?=$_SESSION['captcha']['image_src'] ?>" ><a href="Javascript:reloadcaptcha()">Refresh</a></div>
						<script>
							function reloadcaptcha(){
								document.location.reload();
							}
						</script>
					</td>
				</tr>
				<tr>
					<td align="right">Code : </td>
					<td>	
						<div style="margin-left:10px">
							<input type='text' value='' placeholder="Enter Above Code" required name='code'  class="input_div" style="width:200px" autocomplete="off">
						</div>
					</td>
				</tr>
				<tr>
					<td align="right">&nbsp;</td>
					<td>
						<div style="margin-left:10px">
							<input type='submit' value='Login' class="input_div" style="cursor:pointer;width:50px">
						</div>
					</td>
				</tr>
			</table>
			<input type='hidden' value='login' name='action' >	
			<input type='hidden' value='<?=$module?>' name='school_code' >
		</form>
		<div align='right'><a href='/?' >Back</a></div>
		</div>	
<?php	}else{ ?>

	

	<form method="post" onsubmit="return validate_school_code()">
		<div align="center" style="width:400px; border:2px solid white; padding:20px; margin:auto" >

			<?php 
			if( $_SESSION['special'] == "yes" ){ ?>
		
			<div  align='center' style="font-size:40px;color:white;">ఆన్-లైన్ రిజిస్ట్రేషన్</div>
		
			<?php	if($_GET["event"] == "failed"){ ?>
			<div style='text-align:center;color:red;'>School not found!</div>
			<?php } ?>
			
			<table width="300" align="center" >
				<tr>
					<td><input type="number" name="school_code" id="school_code" class="input_div" style="width:200px;"  placeholder="Enter School UDISE Code" required autocomplete="off" ></td>
					<td><input type="submit" value="LOGIN" class="input_div" style="width:50px;" ></td>
				</tr>
			</table>
			<div id="school_code_div" align="center"></div>
			<input type='hidden' value='check_school_code' name='action' > 


			<?php }else{ ?>


			<div  align='center' style="font-size:40px;color:white;">ఆన్-లైన్ రిజిస్ట్రేషన్</div>
		

			<p style='text-align:center; font-size:30px; color:white;'>CLOSED</p>
			
			<?php } ?>
		</div>
	</form>
	<center>
	<p style='color:white; font-weight:bold;'>రిజిస్ట్రేషన్: దయచేసి పాల్గొనే పరిమితులు మరియు సమూహాలను పూర్తిగా అర్థం చేసుకోవడానికి ప్రయత్నించండి. </p>
	<p style='color:white; font-weight:bold;'>విద్యార్థులు, నైపుణ్యాలు మరియు ఆసక్తుల లభ్యత ఆధారంగా నామినేషన్లను సమర్పించండి. </p>
	<p style='color:white; font-weight:bold;'>మీరు ఫిబ్రవరి 11 వరకు ఎంట్రీలను సవరించవచ్చు.  ఒక పాఠశాలకు 40 మందికి మాత్రమే అనుమతి ఉంది.</p>	
	</center>
	<div align="center">
		<img src="<?=$site_path?>kriya_7.jpg" >
	</div>
	<div align="center">
		<img src="<?=$site_path?>kriya_8.jpg" >
	</div>		
			
<?php	}?>
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
<?php	}else{ ?>
	<form method='post' onsubmit="return validate_form()" >
		<div id='registration_form_div' align='left' style='width:100%;max-width:750px;border:2px solid white; background-color:#ffef96; border-radius:5px; padding:10px; margin:auto;' >
			<div align="right"><a href='?action=logout' >Logout</a><BR><BR></div>
			<table width='100%' cellpadding='5' cellspacing='1' border='0' style='border-collapse:collapse;' align='center'>
				<tr bgcolor='#ffef96'>
					<td align="right"><?=$school_details['school_name'] ?> : </td>
					<td>
						<div style="margin-left:10px"><?=($data1['school_name']) ?></div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td align="right">&nbsp;</td>
					<td><div style="margin-left:10px"><?=($data['email']) ?>, <?=($data['phone']) ?></div></td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td align="right"><?=$school_details['teacher_name']?> : </td>
					<td>
						<div style="margin-left:10px"><input type='text'  class="input_div"  value="<?=(htmlspecialchars($data['contact_person']) )?>" name='teacher_name' id='teacher_id' required autocomplete="off" style="width:200px"></div>
					</td>
				</tr>
				<tr>
					<td align="right"><?=$school_details['pno2']?> : </td>
					<td><div style="margin-left:10px"><input type='number' class="input_div"  value="<?=($data['phone2']) ?>" name='phone2' id='pho2' style="width:200px" autocomplete="off"></div></td>
				</tr>
				<tr>
					<td align="right">మొత్తం వెంట వచ్చు టీచర్స్ సంఖ్య  : </td>
					<td>
						<div style="margin-left:10px">
							<input type='number' name='total_teachers'  id='total_teachers_id' class="input_div" style='width:60px;' value="<?=$data['total_teachers']?>"  autocomplete="off">
						</div>
					</td>
				</tr>
			<?php	if( $data1["district_code"]!=2814 ){?>
				<tr  bgcolor='#ffef96' >
					<td align="right">వసతి సదుపాయం కావాలా?  : </td>
					<td>
						<div style="margin-left:10px"><input type='checkbox' name='accommodation'  id='accommodation_id' style='width:15px; height:15px;' value="y" <?=$data['accommodation']?"checked":""?>" ></div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td colspan='2' align='center' style='font-size:10px;color:gray;'>పైన తెలిపిన విషయం ఆహార మరియు ఇతర ఏర్పాట్ల కొరకు</td>
				</tr>
			<?php	}?>
			</table>
			<input type='hidden' name='total_students'  id='total_students_id' value="<?=$data['total_students'] ?>" >
	</div>	
	<center>
	<p style='color:white; font-weight:bold;'>దయచేసి పాల్గొనే పరిమితులు మరియు సమూహాలను పూర్తిగా అర్థం చేసుకోవడానికి ప్రయత్నించండి. </p>
	<p style='color:white; font-weight:bold;'>విద్యార్థులు, నైపుణ్యాలు మరియు ఆసక్తుల లభ్యత ఆధారంగా నామినేషన్లను సమర్పించండి. </p>
	<p style='color:white; font-weight:bold;'>మీరు ఫిబ్రవరి 11 వరకు ఎంట్రీలను సవరించవచ్చు.  ఒక పాఠశాలకు 40 మందికి మాత్రమే అనుమతి ఉంది.</p>
	</center>
	<table class='ddd satfixed' border=0 style="border-collapse:collapse;" width="100%" cellpadding='5' cellspacing='1'>
		<thead>
			<tr valign='middle' bgcolor='#3B5998' style='color:white;font-weight:bold;'>
				<td class="col_"  align='center' width="40" rowspan="2" >క్రమ సంఖ్య</td>
				<td class='hcol3' align='center' rowspan="2"  >పోటీ పేరు</td>
				<td colspan='3' align='center' >అనుమతించబడే పిల్లలు/గ్రూపులు</td>
				<td class='hcol6' align='center' rowspan="2" >నియమాలు</td>
			</tr>
			<tr valign='middle' bgcolor='#3B5998' style='color:white;font-weight:bold;'>
				<td width='60'align='center'>సబ్ జూనియర్స్</td>
				<td width='60' align='center'>జూనియర్స్</td>
				<td width='60' align='center'>సీనియర్స్</td>
			</tr>
		</thead>
		<tbody>
	<?php	foreach ($config_categories as $key => $value) {
			$key = (int)$key;?>
			<tr bgcolor='#ffef96'>
				<td class="col_" align='right'><?=$value['sno']?></td>
				<td class="col2" width='250'><?=$value['name']?></td>
				<td class="col3" align='center' nowrap >
				<?php	if( $value["enabled"][0] ){ ?>

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
				<?php	}else{?>
						<div>-</div>
				<?php	}?>
				</td>
				<td class="col4" align='center' nowrap >
				<?php	if( $value["enabled"][1] ){   ?>
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
						<div>-</div>
				<?php	}?>
				</td>
				<td class="col5" align='center' nowrap >
				<?php	if( $value["enabled"][2] ){   ?>
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
						<div>-</div>
				<?php	}?>
				</td>
				<td class="col6" width='450'><?=$value['details']?></td>
			</tr>
	<?php	}?>	
		</tbody>
	</table>
	<div align='center' id="footer_msg" style='display:none;padding:10px;position:fixed; left:0px; bottom:0px; width:100%; background-color:rgba(0,0,0,0.8);' align=center >
		<div style="display:inline-block; font-size:18px; color:white; margin-right:50px;" >మొత్తం పాల్గొనే విద్యార్దుల సంఖ్య : <span style='font-weight:bold; font-size:22px; ' id='total_students_div'><?=$data['total_students'] ?></span></div> <input type='submit' value='Save Details &amp; Confirm Participation' name='Register' id="register_btn" class="input_div" style=' padding:5px; cursor:pointer; margin:10px;width:300px !important; font-weight:bold;'>
	</div>
	<input type='hidden' name='action' value='register'></div>
	</form>
	<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
	<script>
		function validate_this( vobj ){
			cnt = document.getElementById("total_students_id").value;
			if( cnt > 40 ){
				vobj.value = "0";
			}
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
				document.getElementById("total_students_id").value = cnt;
				document.getElementById("total_students_div").innerHTML = cnt;
			}
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
			if( Number(document.getElementById("total_students_id").value) > 40 ){
				alert("Number of students per school is limited\n\nPlease keep your participation size not more than 40 students.");
				return false;
			}
			$("#register_btn").val("Saving Information");
			return true;
		}
	</script>





<style>
.table-striped tbody tr:nth-of-type(odd) { background-color:#f8f8ff; }
th{ font-weight:500; }
</style>
<style>
.he { position:fixed; background-color:#fafafb; border-bottom:1px solid #F48024; top:0px; left:0px; width:100%; height:50px !important; z-index:110; }
.lf { position:fixed; left:0px; top:50px; width:200px; height:100%; background-color:white; border-right:1px solid #cdcdcd; z-index:111; }
.cf { margin-top:150px; margin-left:200px; }
.cf_1 { position:fixed; left:0px; top:50px; width:100%; height:100px; z-index:110; background-color:white; }
.cg1{ height:50px; margin-left:200px; }
.cg2{ height:50px; margin-left:200px; }
.cf_2 { position:fixed; margin-left:200px; left:0px; top:100px; width:100%; height:30px; z-index:111; background-color:#f0fff0; }
th, td { white-space:nowrap !important; }
.col01{ white-space:nowrap; }
.col02{ }
.col03{ }
.col04{ }
.col05{ }
.col06{ }
.col07{ }
.col08{ }
.col09{ }
.col10{ }
.col11{ }
.col12{ }

</style>
<script>
var gcnt = 0;
var csslist = ["border-top", "border-left", "border-right", "border-bottom", "border", "padding", "white-space", "text-align", "box-sizing", "vertical-align", "font-weight", "border-collapse", "font-size", "font-style", "color", "font-variant", "display", "border-spacing", "border-color", "margin", "font-family", "background-color" ];
function createTree( vmain, vnew, vlevel ){
	var vlist = vmain.childNodes;
	//console.log( vlevel + ": ChildNodes: " + vlist.length );
	gcnt++;
	if( gcnt > 200 ){
		console.log("count readhed");
		return 0;
	}
	for(var vi=0;vi<vlist.length;vi++){
		var v = vlist[vi];
		//console.log( vlevel + ": " + vi + ": " +  v.nodeName );
		//if( v.nodeName != "#text" )
		{
			if( v.nodeName == "#text" ){
				//console.log( vlevel + ": " + vi + ": " + v.nodeValue );
				var vt = document.createTextNode( v.nodeValue );
				vnew.appendChild( vt );
			}else{
				var vt = document.createElement(v.nodeName);
				if( v.nodeName=="TD" ||  v.nodeName == "TH" || v.nodeName == "TABLE" || v.nodeName == "TR" ){
					//$(vt).width( $(v).width() );
					//$(vt).height( $(v).height() );
					$(vt).innerHeight( $(v).innerHeight() );
					$(vt).outerWidth( $(v).outerWidth() );
					console.log( v.innerHTML );
					console.log( $(v).width() + ": " + $(v).height() );
				}
				for( iss in csslist ){
					try{
					if( $(v).css( csslist[iss] ) ){
						$(vt).css( csslist[iss], $(v).css( csslist[iss] ) );
					}
					}catch(e){}
				}
				vl = v.attributes;
				//console.log( vl );
				for(si=0;si<vl.length;si++){
					//console.log("ATTRIBUTE: " +  vl[si].nodeName + ": " + vl[si].nodeValue );
					//console.log("ATTRIBUTE: " + vl[si] + ": " + vl[si] );
					//break;
					vt.setAttribute( vl[si].nodeName, vl[si].nodeValue );
				}
				vnew.appendChild( vt );
				createTree( v, vt, vlevel+1 );
			}
		}
	}
	vlevel++;
}
function createTree2( vmain, vnew, vlevel ){
	var vlist = vmain.childNodes;
	//console.log( vlevel + ": ChildNodes: " + vlist.length );
	gcnt++;
	if( gcnt > 200 ){
		console.log("count readhed");
		return 0;
	}
	for(var vi=0;vi<vlist.length;vi++){
		var v = vlist[vi];
		//console.log( vlevel + ": " + vi + ": " +  v.nodeName );
		//if( v.nodeName != "#text" )
		{
			if( v.nodeName == "#text" ){
				//console.log( vlevel + ": " + vi + ": " + v.nodeValue );
				var vt = document.createTextNode( v.nodeValue );
				vnew.appendChild( vt );
			}else{
				f = true;
				if( v.nodeName == "TD" || v.nodeName == "TH" ){
					//console.log( $(v).hasClass("fixed") );
					if( $(v).hasClass("fixed") == false ){
						f = false;
					}
				}
				if( f ){
				var vt = document.createElement(v.nodeName);
				if( v.nodeName=="TD" ||  v.nodeName == "TH" || v.nodeName == "TABLE" || v.nodeName == "TR" ){
					//$(vt).width( $(v).width() );
					//$(vt).height( $(v).height() );
					$(vt).innerHeight( $(v).innerHeight() );
					$(vt).outerWidth( $(v).outerWidth() );

				}
				for( iss in csslist ){
					try{
					if( $(v).css( csslist[iss] ) ){
						$(vt).css( csslist[iss], $(v).css( csslist[iss] ) );
					}
					}catch(e){}
				}
				vnew.appendChild( vt );
				createTree2( v, vt, vlevel+1 );
				}
			}
		}
	}
	vlevel++;
}

$(document).ready(function(){

	$( window ).scroll(function(){
		x = $(this).scrollTop();
		y = $(this).scrollLeft();
		console.log( "Scrolled:" + x+":"+y );
		$("#fixedTHead_inner").css( "margin-left", "-"+y+"px" ); 
		//$("#fixedTHead2_inner").css( "margin-left", "-"+y+"px" );
		$("#fixedLeft_inner").css( "margin-top", "-"+x+"px" );
		$("#footerdiv").css('margin-left', y+"px");
		
		theight = $(document).height();
		ftop = theight - 100 - window.innerHeight;
		if( x > ftop ){
			console.log("foootter");
			//$("#footerdiv").css('position','fixed');
		}else{
			//$("#footerdiv").css('position','absolute');
		}

		if( x > 450 ){
			$("#footer_msg").css("display", "block");
		}
		
		if( x > 600 ){
			$("#fixedTHead").css('display','block');
		}else{
			$("#fixedTHead").css('display','none');
		}
		
	  });
	  console.log( "Height: " + $(document).height() );
	  console.log( "Height: " + window.innerHeight );


	global_ttop = 0;

	vmainc = document.getElementsByClassName("satfixed");
	pos = $(vmainc).position();
	for(vi=0;vi<vmainc.length;vi++){
		vele = vmainc[vi];
		vlist = vele.childNodes;
		for(vi2=0;vi2<vlist.length;vi2++){
			var v = vlist[vi2];
			if( v.nodeName == "THEAD" ){

				var vt2 = document.createElement("div");
				vt2.style.backgroundColor = 'white';
				vt2.style.position = 'fixed';
				vt2.style.top = pos.top+"px"; 
				vt2.style.left = pos.left+"px";
				vt2.style.zIndex = 57;
				vt2.style.overflow = 'hidden';
				vt2.style.borderRight = '1px solid #cdcdcd';
				//$(vt2).width( $(v).width() );
				$(vt2).height( $(v).height() );
				vt2.id = "fixedTHead2";
				vt2.className="fixedTHead2";
				//document.body.appendChild( vt2 );
				//document.getElementById("somethinggood").appendChild(vt2);

				var vt3 = document.createElement("table");
				vt3.style.backgroundColor = 'white';
				vt3.id = "fixedTHead2_inner";
				vt3.className="fixedTHead2_inner";
				//$(vt3).width( $(v).width() );
				$(vt3).height( $(v).height() );
				vt2.appendChild( vt3 );
				createTree2( v, vt3, 1, true );

				var vt1 = document.createElement("div");
				//vt1.style.backgroundColor = 'white';
				vt1.style.position = 'fixed';
				vt1.style.top = "0px";
				vt1.style.display = 'none';
				global_ttop = pos.top; 
				vt1.style.left = pos.left+"px";
				vt1.style.zIndex = 55;
				vt1.style.overflow = 'hidden';
				
				$(vt1).width( $(v).width() );
				//$(vt1).height( $(v).height() );
				vt1.id = "fixedTHead";
				vt1.className="fixedTHead";
				document.body.appendChild( vt1 );
				//document.getElementById("somethinggood").appendChild(vt1);

				var vt = document.createElement("table");
				//vt.style.backgroundColor = 'white';
				//vt.style.border = '1px solid #f1f1f1';
				vt.id = "fixedTHead_inner";
				vt.className="fixedTHead_inner";
				vt.setAttribute("cellpadding",5);
				vt.setAttribute("cellspacing",1);
				$(vt).width( $(v).width() );
				$(vt).height( $(v).height() );
				vl = v.attributes;
				vt1.appendChild( vt );
				createTree( v, vt, 1, true );

			}else if( v.nodeName == "TBOsDY" ){
				var vt1 = document.createElement("div");
				vt1.style.backgroundColor = 'white';
				pos2 = $(v).position();
				vt1.style.position = 'fixed';
				vt1.style.top = (pos.top+pos2.top)+"px"; 
				vt1.style.left = (pos.left+pos2.left)+"px";
				vt1.style.overflow = 'hidden';
				vt1.style.zIndex = 54;
				vt1.style.borderRight = '1px solid #cdcdcd';
				vt1.id = "fixedLeft";
				vt1.className="fixedLeft";
				document.getElementById("somethinggood").appendChild(vt1);
				//document.body.appendChild( vt1 );
								
				var vt = document.createElement("table");
				vt.style.backgroundColor = 'white';
				pos2 = $(v).position();
				vt.id = "fixedLeft_inner";
				vt.className="fixedLeft_inner";
				vt1.appendChild( vt );
				createTree2( v, vt, 1, true );
			}
		}
	}
	//vt = document.createElement("");
});
</script>






<?php	}
}?>	







</body>
</html>	