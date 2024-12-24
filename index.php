<?php
	session_start();

	include('../config_global.php');

	if( !isset($login_enable)  || !isset($email_cc_list) || !isset($user)  ){
		http_response_code(500);
		echo "configuration pending..";exit;
	}


	if( $_GET['enable'] == "special4" ){
		unset($_SESSION['email']);
		unset($_SESSION['logged_in']);
		//echo "OK";exit;
		$_SESSION['special4'] = "yes";
		header("Location: /?special_login_enabled");
		exit;
	}

	if( $_SESSION['logged_in'] == "yes" ){
		if( !$_SESSION['email'] ){
			session_destroy();
			session_regenerate_id();
			header("Location: index.php");exit;
		}
		header("Location: home.php?email=". urlencode($_SESSION['email']) );exit;
	}

	include('db.php');
	include('config.php');
	
	if( $_SESSION['special4'] == "yes" ){
		//echo "Okk";exit;
		$login_enable = true;
	}

	//echo $login_enable
	
	//print_r( $_GET );exit;


	//print_r( $_SESSION );exit;

	// var_dump($login_enable);
	// if( $login_enable ){
	// 	echo "OKk";exit;
	// }else{
	// 	echo "not ok";exit;
	// }

	include("smtp_ses.php");
	include("actions.php");

	// if( $_SESSION['loggedin'] == "y" ){
	// 	$school_res = mysqli_query( $connection, "select * from kriya_schools where id =" . $_SESSION["user_id"]);
	// 	$data = mysqli_fetch_assoc( $school_res );
	// 	if( !$data ){
	// 		session_destroy();
	// 		header("Location: /?event=SchoolNotFound");
	// 		exit;
	// 	}

	// 	if( !$module ){
	// 		header("Location: /" . $data['school_id'] . "?event=LoginFound");exit;
	// 	}

	// 	$school_res1 = mysqli_query( $connection, "select * from kriya_school_list where school_id =" . $data["school_id"]);
	// 	$data1 = mysqli_fetch_assoc( $school_res1 );
	// 	$data1['school_category'] = $data1['school_category']?$data1['school_category']:"-";
	// 	$items_data = array();
	// 	$item_res = mysqli_query( $connection,  "select * from kriya_options where school_id = " . $_SESSION['user_id'] );
	// 	echo mysqli_error( $connection );
	// 	while( $r = mysqli_fetch_assoc( $item_res ) ){
	// 		$items_data[ $r['item_id'] ] = $r;
	// 	}
	// 	$selection = json_decode($data['selection'],true);
	// }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Kriya Registration Form</title>
	<script src="/js/axios.min.js"></script>
	<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<style>
		body {
			background-color: #2c95da;
		}
	</style>
</head>
<body>
	<div id="app">
		<div class="text-center">
		    <img src="/kriya.jpg" class="img-fluid" alt="Logo">
		</div>
		<div>
		    <div class="container" style="max-width: 400px;">
		        <div style="border: 2px solid white; padding: 20px; border-radius: 10px; background-color: #fff4d9;">
		        	<template v-if="login_enable" >
			            <div class="text-center" style="margin-top: 0; font-size:1.2rem; color:#666;">REGISTRATION</div>
			            <div>Email:</div>
			            <div class="mb-3">
						    <input type="email" v-model="email" class="form-control form-control-sm" placeholder="Enter Email ID" autocomplete="off" >
						</div>
						<div v-if="show_captcha==false&&otp_sent==false" class="mb-3">
			            	<button type="button" class="btn btn-sm" v-on:click="show_captcha=true;reloadcaptcha()" style="background-color: #2c95da; color: white;">GO</button>
			            </div>
						<template v-if="show_captcha">
							<div>Security Code</div>
							<div  class="mb-3">
								<img src="" alt="CAPTCHA" id="captchaimg" class="img-fluid me-2">
								<div style="width:30px; height:30px; display: inline-block;cursor: pointer;" v-on:click.prevent="reloadcaptcha" >
						    		<svg viewBox="0 0 64 64" fill="currentcolor"><path d="m54,32c0,12.15-9.85,22-22,22s-22-9.85-22-22,9.85-22,22-22h2.26l-5.76-5.76,4.24-4.24,13,13-13,13-4.24-4.24,5.76-5.76h-2.26c-8.84,0-16,7.16-16,16s7.16,16,16,16,16-7.16,16-16h6Z"></path></svg>
							    </div>
							</div>
							<div  class="mb-3">
							    <input type="text" class="form-control form-control-sm" v-model="code" id="code" name="code" placeholder="Enter Above Code" required autocomplete="off" >
							</div>
							<button type="button" class="btn btn-sm" style="background-color: #2c95da; color: white;" @click="send_otp">Get OTP</button>
				        </template>
				        <template v-if="otp_sent" >
				        	<div class="float-end">
				        		<button type="button" class="btn btn-link btn-sm" @click="otp_sent = false; show_captcha = true; reloadcaptcha()">Resend OTP</button>
				        	</div>
				        	<div>OTP: </div>
							<div class="mb-3" >
							    <input type="number" name="email_otp" v-model="email_otp" class="form-control" placeholder="Enter OTP" style="width: 100%;">
							</div>
							<div class="mb-3 d-flex justify-content-between">
								<button type="button" class="btn btn-sm" style="background-color: #2c95da; color: white;" @click="email_login">Login</button>
							</div>
						</template>
						<div v-if="err" class="alert alert-danger mb-3  py-1" role="alert">
						    {{ err}}
						</div>
						<div v-if="msg" class="alert alert-dark mb-3 py-1" role="alert">
						    {{ msg }}
						</div>
					</template>
					<template v-else >
						<div class="text-center" style="margin-top: 0; font-size:1.2rem; color:#666;">REGISTRATION CLOSED</div>
					</template>
		        </div>
		    </div>
			<div class="text-center">
				<p class="text-white font-weight-bold">Last date for submission and corrections <span class="h5">21st December</span> 2024. Max 60 members are allowed from a school.</p>
			</div>
			<div class="text-center">
			    <img src="/header_final.jpg" class="img-fluid" alt="Description">
			</div>
		</div>
	</div>
	<script>
		var app=Vue.createApp({
			data(){
				return {
					email:'',
					code:'',
					captcha_code:'',
					otp_sent: false,
					msg:'',
					email_otp:'',
					err:'',
					show_captcha: false,
					s: <?=$login_enable?"true":"false" ?>,
					login_enable: <?=$login_enable==true?"true":"false" ?>,
				};
			},
			mounted(){
				
			},
			methods:{
				send_otp(){
					this.err = "";
					this.msg = "";
					var email = this.email.trim().toLowerCase();
					var code = this.code.trim();
					if( email.match(/^[a-z0-9\-\.\_]{2,100}\@[a-z0-9\-\.\_]{2,100}\.[a-z\.]{2,5}$/)  == null ){
						this.err = ("Enter proper email");return false;
					}
					if( code == "" ){
						this.err = ("Enter security code");return false;
					}

					this.msg = "Sending OTP...";
					vdata = "email="+encodeURIComponent(email);
					vdata = vdata + "&code="+encodeURIComponent(code);
					vdata = vdata + "&captcha_code="+encodeURIComponent(this.captcha_code);
					vdata = vdata + "&action=send_otp";
					var con = new XMLHttpRequest();
					con.open("POST", "?", true );
					con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
					con.onload = () => {
						this.msg = "";
						const response = JSON.parse(con.responseText);
				        if (response.status === "fail") {
				            this.err = response.error;
				            this.reloadcaptcha();
				            // this.code = "";
				        } else if (response.status === "OTPSent") {
				            this.msg = "OTP Sent Successfully!";
				            this.otp_sent = true;
				            this.show_captcha = false;
				            this.err = "";
				            // this.code = "";
				        }
					};
					con.send( vdata );
				},
				reloadcaptcha(){
					var con = new XMLHttpRequest();
					con.open("GET", "?action=getcaptcha", true );
					con.onload = function(v){
						var v= JSON.parse(this.responseText);
						document.getElementById("captchaimg").src = v['img'];
						this.captcha_code = v['code'];
					};
					con.send();
				},
				resend_email() {
				    this.err = "";
				    this.msg = "Resending OTP...";

				    this.send_otp();
				},
				email_login(){
					// console.log(this.email)
					var email = this.email.trim().toLowerCase();
					this.email = email;
					var email_otp = String(this.email_otp).trim();
					this.err = "";
					this.msg = "";

					if( email.match(/^[a-z0-9\-\.\_]{2,50}\@[a-z0-9\-\.\_]{2,50}\.[a-z\.]{2,10}$/)  == null ){
						this.err = ("Enter proper email");return false;
					}
					if( email_otp.match(/^[0-9]{5,8}/)  == null ){
						this.err = "Enter proper otp";return false;
					}
					this.msg = "Submitting...";

					vdata = "email=" + encodeURIComponent(email);
					vdata = vdata  + "&email_otp=" + encodeURIComponent(email_otp);
					vdata = vdata + "&action=email_login";

					var con = new XMLHttpRequest();
					con.open("POST", "?", true );
					con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
					con.onload = () => {
						this.msg = "";
						console.log(con.responseText);
						try{
							var v= JSON.parse(con.responseText);
							if( v['status'] == "success" ){
								this.msg = "Success! redirecting..";
								document.location = "/home.php?email="+this.email;
							}else{
								this.err = v['error'];
							}
						}catch(e){
							this.err = "Incorrect Response " + e;
						}
					};
					con.onerror = () => {
					    this.err = "Network error. Please try again.";
					};
					con.send( vdata );
				},
			},
		}).mount("#app");
	</script>
</body>
</html>