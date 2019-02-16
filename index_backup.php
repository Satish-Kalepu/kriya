<?php
	session_start();
	include('db.php');
	include('config.php');
	include("actions.php");
	include("router.php");
	require("captcha/captcha.php");

	if( !is_numeric($module) ){
		unset($module);
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
		padding:2px;
	}
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
	
	</style>
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
			if(school_code.length != "11"){
				document.getElementById("school_code_div").innerHTML ="Enter Valid School Code";
				document.getElementById("school_code_div").style.color='red';
				return false;	
			}
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
		<img src="/kriya_9.jpg" >
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
						<div style="margin-left:10px"><img src="<?=$_SESSION['captcha']['image_src'] ?>" ></div>
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
<?php	}else{?>

	

	<form method="post" onsubmit="return validate_school_code()">
		<div align="center" style="width:400px; border:2px solid white; padding:20px; margin:auto" >
		
			<div  align='center' style="font-size:40px;color:white;">???-???? ?????????????</div>
		
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
		</div>
	</form>
	
	<div align="center">
		<img src="/kriya_7.jpg" >
	</div>
	<div align="center">
		<img src="/kriya_8.jpg" >
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
<?php	}else{?>
	<form method='post' onsubmit="return validate_form()" >
		<div id='registration_form_div' align='left' style='width:100%;max-width:750px;border:2px solid white; background-color:#ffef96; border-radius:5px; padding:10px; margin:auto;' >
			<div align="right"><a href='?action=logout' >Logout</a><BR><BR></div>
			<table width='100%' cellpadding='5' cellspacing='1' border='0' style='border-collapse:collapse;' align='center'>
				<tr bgcolor='#ffef96'>
					<td align="right"><?=$school_details['school_name']?> : </td>
					<td>
						<div style="margin-left:10px"><?=($data1['school_name']) ?></div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td align="right">&nbsp;</td>
					<td>
						<div style="margin-left:10px"><?=$data1['medium1'] . "-" . $data1['school_category']."-".$data1['school_type'] ?></div>
						<div style="margin-left:10px"><?=($data1['village_name']) . ", " .$data1['mandal_name'].",".$data1['district_name']?></div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td align="right"><?=$school_details['email']?> : </td>
					<td><div style="margin-left:10px"><?=($data['email']) ?></div></td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td align="right"><?=$school_details['pno']?> : </td>
					<td><div style="margin-left:10px"><?=($data['phone']) ?></div></td>
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
					<td align="right">?????? ???????? ??????????? ?????  : </td>
					<td>
						<div style="margin-left:10px">
							<input type='hidden' name='total_students'  id='total_students_id' class="input_div" style='width:60px;' value="<?=$data['total_students']?>" required  max='40' readonly autocomplete="off">
							<div style='font-weight:bold;' id='total_students_div'><?=$data['total_students'] ?></div>
						</div>
					</td>
					
				</tr>
				<tr>
					<td align="right">?????? ???? ????? ??????? ?????  : </td>
					<td>
						<div style="margin-left:10px">
							<input type='number' name='total_teachers'  id='total_teachers_id' class="input_div" style='width:60px;' value="<?=$data['total_teachers']?>"  autocomplete="off">
							<div id='total_students_div'></div>
						</div>
					</td>
				</tr>
			<?php	if($data1["mandal_code"] !="281423" && $data1["mandal_code"] !="281424"){?>
				<tr  bgcolor='#ffef96' >
					<td align="right">???? ??????? ???????  : </td>
					<td>
						<div style="margin-left:10px"><input type='checkbox' name='accommodation'  id='accommodation_id' style='width:15px; height:15px;' value="y" <?=$data['accommodation']?"checked":""?>" ></div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td colspan='2' align='center' style='font-size:10px;color:gray;'>??? ??????? ????? ???? ????? ??? ???????? ?????</td>
				</tr>
			<?php	}?>
				
			</table>
			<div align='center' style='padding:5px';>
				<input type='submit' value='Save Details' name='Register' class="input_div" style=' margin:20px; padding:10px; cursor:pointer;width:200px !important;'>
			</div>
	</div>	
	<div style='height:20px;'></div>
	<table class='ddd' border=0 style="border-collapse:collapse;" width="100%" cellpadding='5' cellspacing='1'>
		<thead>
			<tr valign='middle' bgcolor='#3B5998' style='color:white;font-weight:bold;'>
				<td class="col_"  align='center' width="40" rowspan="2" >???? ?????</td>
				<td class='hcol3' align='center' rowspan="2"  >???? ????</td>
				<td colspan='3' align='center' >??????????? ???????/????????</td>
				<td class='hcol6' align='center' rowspan="2" >???????</td>
			</tr>
			<tr valign='middle' bgcolor='#3B5998' style='color:white;font-weight:bold;'>
				<td width='60'align='center'>??? ?????????</td>
				<td width='60' align='center'>?????????</td>
				<td width='60' align='center'>?????????</td>
			</tr>
		</thead>
		<tbody>
	<?php	foreach ($config_categories as $key => $value) {
			$key = (int)$key;?>
			<tr bgcolor='#ffef96'>
				<td class="col_" align='right'><?=$value['sno']?></td>
				<td class="col2" width='250'><?=$value['name']?></td>
				<td class="col3" align='center'>
				<?php	if( $value["enabled"][0] ){?>
						<input class="int" style='background-color:#ffef96;width:60px !important;' type='number' value="<?=$items_data[$key]['sub_jrs']?$items_data[$key]['sub_jrs']:""?>" name='stu[<?=$key?>][sub_jrs]' id='sub_jrs_id_<?=$key?>' data-max='<?=$value['max'][0]?>' onchange='validate_this(this)' >
				<?php	}else{?>
						<div>-</div>
				<?php	}?>
				</td>
				<td class="col4" align='center'>
				<?php	if( $value["enabled"][1] ){   ?>
						<input class="int" style='background-color:#ffef96;width:60px !important;' type='number' value="<?=$items_data[$key]['jrs']?$items_data[$key]['jrs']:""?>" name='stu[<?=$key?>][jrs]' id='jrs_id_<?=$key?>' data-max='<?=$value['max'][1]?>' onchange='validate_this(this)' >
				<?php	}else{?>
						<div>-</div>
				<?php	}?>
				</td>
				<td class="col5" align='center'>
				<?php	if( $value["enabled"][2] ){   ?>
						<input class="int" style='background-color:#ffef96;width:60px !important;' type='number' value="<?=$items_data[$key]['srs']?$items_data[$key]['srs']:""?>" name='stu[<?=$key?>][srs]' id='srs_id_<?=$key?>' data-max='<?=$value['max'][2]?>' onchange='validate_this(this)' >
				<?php	}else{?>
						<div>-</div>
				<?php	}?>
				</td>
				<td class="col6" width='450'><?=$value['details']?></td>
			</tr>
	<?php	}?>	
		</tbody>
	</table>
	<div align='center' style='padding:5px';>
		<input type='submit' value='Confirm Participation' name='Register' class="input_div" style=' margin:20px; padding:10px; cursor:pointer;width:200px !important;'>
	</div>
	<input type='hidden' name='action' value='register'></div>
	</form>
	<script>
		function validate_this( vobj ){
			if( Number(vobj.value) < 1 ){
				vobj.value = "";
				return;
			}
			if( Number(vobj.getAttribute("data-max")) < Number(vobj.value) ){
				vobj.value= vobj.getAttribute("data-max");
			}
			var cnt=0;
			for(i in config_categories){   
				if(document.getElementById("sub_jrs_id_"+i)){
					c1 = Number(document.getElementById("sub_jrs_id_"+i).value);
				}else{
					c1= Number(0);
				}
				if(document.getElementById("jrs_id_"+i)){
					c2 = Number(document.getElementById("jrs_id_"+i).value);
				}else{
					c2=Number(0);
				}
				if(document.getElementById("srs_id_"+i)){
					c3 = Number(document.getElementById("srs_id_"+i).value);
				}else{
					c3 = Number(0);
				}		
				cnt = Number(cnt + c1 + c2 + c3);
				document.getElementById("total_students_id").value = cnt;
				document.getElementById("total_students_div").innerHTML = cnt;
			}
			
			if(cnt > 38){
				if( Number(vobj.getAttribute("data-max")) < Number(vobj.value) ){
					cnt = cnt - Number(vobj.value)+ vobj.getAttribute("data-max");
					console.log(cnt);
					if(cnt >38){
						v = cnt-40;
						if(vobj.getAttribute("data-max") >v){
							vobj.value= vobj.getAttribute("data-max")-1;
							cnt = cnt-1;
						}else{
							vobj.value= v;
							cnt = cnt-v;
						}
					}
					document.getElementById("total_students_id").value = cnt;
				}
				if(cnt >38){
					document.getElementById("total_students_div").innerHTML = "Max 40 Students are allowed";
					alert("Max 40 Students are allowed");
				}
			}
		}
		function validate_form(){
			v = document.getElementById("teacher_id").value;
			if( v.match(/^[a-zA-Z0-9\ \.\-\_\,\(\)]{3,50}$/) == null ){
				alert("Enter Teacher/Contact Person Name\n\nspecial characters not accepted!");
				return false;
			}
			v = document.getElementsByClassName("int");
			f = 0;
			for( i in v ){
				try{
					console.log( v[i].name );
					if( v[i].getAttribute("type") == "number" ){
						if( v[i].value != "" ){
							f=1;
							console.log( "Value: " + v[i].value );
						}
					}
				}catch(e){
					console.log( "Error: " + e);
				}
			}
			if( f== 0 ){
				alert( "Participate at least in one category ");
				return false;
			}
		}
	</script>
<?php	}
}?>	
</body>
</html>	