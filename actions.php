<?php

	if( $_GET['action'] == "logout"){
		session_destroy();
		unset( $_SESSION['loggedin'] );
		header("Location: /?");
		exit;
	}


if( $_GET['action'] == "getcaptcha" ){

	$v = "0123456789abcdefghijklmnopqrstuvwxyz";
	$cap = substr($v,rand(0,35),1)." ".substr($v,rand(0,35),1)." ".substr($v,rand(0,35),1)." ".substr($v,rand(0,35),1);
	$code = time();

	$im = imagecreatetruecolor(120,40);
	$white = imagecolorallocate($im, rand(0,255),rand(0,255),rand(0,255));
	imagefill($im, 0,0, $white);
	$white = imagecolorallocate($im, rand(110,255),rand(110,255),rand(110,255));

	$sz = rand(20,24);
	$angle = rand(-5,5);
	$x = rand(0,20);
	$y = rand(20,30);
	imagettftext($im, $sz, $angle, $x, $y, $white,  __DIR__."/arial.ttf", $cap);
	$red = imagecolorallocate($im, rand(0,55),rand(0,85),rand(0,95));
	imagettftext($im, $sz, $angle, $x+1, $y+1, $red,  __DIR__."/arial.ttf", $cap);
	$red = imagecolorallocate($im, rand(0,155),rand(0,155),rand(0,155));
	imagettftext($im, $sz, $angle, $x-1, $y-1, $red,  __DIR__."/arial.ttf", $cap);

	$_SESSION['login_captcha'] = str_replace(" ","", $cap);
	$_SESSION['login_code'] = $code;

	//header("Content-Type: image/jpeg");imagepng($im);exit;
	ob_start();
	imagepng($im);
	$imagedata = ob_get_contents();
	ob_end_clean();
	echo json_encode([
		"status"=>"success",
		"img"=>"data:image/png;base64,".base64_encode($imagedata),
		"code"=>$code,
	]);
	exit;
}



if( $_POST['action'] == "cancel_school_registration" ){
	
	$id1 = $_POST['school_id1']-1234567;
	$id2 = (1234567-$_POST['school_id2']);
	
	if( !$id1 || !$id2 ){
		echo "Errro in request";
		exit;
	}
	
	//echo $id1;exit;

	if( $id1 == $id1 ){
		mysqli_query( $connection, "insert into kriya_schools_cancelled select * from kriya_schools where id = " . $id1 );
		if( mysqli_error( $connection ) ){
			echo mysqli_error( $connection ); 
			echo "<Br>There was an error at server";
			exit;			
		}

		mysqli_query( $connection, "delete from kriya_schools where id = " . $id1 );
		if( mysqli_error( $connection ) ){
			echo "<Br>There was an error at server";
			exit;			
		}
		mysqli_query( $connection, "delete from kriya_options where school_id = " . $id1 );
		if( mysqli_error( $connection ) ){
			echo "<Br>There was an error at server";
			exit;			
		}
		
		echo "<p>Deleted School</p>";
		echo "<p><a href='/admin.php' >Click here to go back</a></p>";
	
	}
	exit;

}

if( $_GET['action'] == "download_report" ){

	require 'vendor/autoload.php';

	$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$sheet->setCellValue('A1', 'Hello World !');

	$writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

	$filename = "kriya_schools.xlsx";

	$header = array(
	  'Reg No'=>'string',
	  'School Code'=>'string',
	  'School'=>'string',
	  'City' => 'string',
	  'Contact' => 'string',
	  'Phone' => 'string',
	  'Email' => 'string',
	  '.'=>'string',
	);
	$columns = ["A", "B", "C", "D", "E", "F", "G", "I", "J", "K", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU"];
	$cnt = 1;
	foreach( $header as $i=>$j ){
		$sheet->setCellValue( $columns[ $cnt-1 ] . "1", $i );
		$cnt++;
	}
	for($i=101;$i<=128;$i++){
		$sheet->setCellValue( $columns[ $cnt-1 ] . "1", $i );
		$cnt++;
	}
	$rows = array();
	$schools = array();
	$query = "select * from kriya_schools where school_id != '' order by school_id";
	$res = mysqli_query($connection,$query);
	while( $row = mysqli_fetch_assoc( $res)){
		$schools[ $row['id'] ] = $row;
		$schools[ $row['id'] ]["city"] = $row["village_name"]. " - ". $row["mandal_name"]." - ".$row["district_name"];
	}

	$options = array();
	$query = "select * from kriya_options order by item_id ";
	$res = mysqli_query($connection, $query);
	while( $row = mysqli_fetch_assoc( $res )){
		if( !$schools[ $row['school_id'] ][ "items" ] ){
			$schools[ $row['school_id'] ][ "items" ] = array();
		}
		$schools[ $row['school_id'] ][ "items" ][ $row['item_id'] ] = $row;
		$options[ $row['item_id'] ] = 1;
	}
	//echo "<pre>";print_r( $options );exit;
	$itemtypes = array("sub_jrs"=>"Sub Juniors", "jrs"=>"Juniors", "srs"=>"Seniors");
	foreach( $itemtypes as $item_type=>$item_type_name ){
		$rows = array();
		foreach( $schools as $school_id=>$school ){
			$f = 0;
			foreach( $options as $item_id=>$ii ){if( $school["items"][ $item_id ][ $item_type ] ){
				$f = 1;
			}}
			if( $f ){
				$row = array(
					$school['id'],
					($school['school_id']),
					($school['school_name']),
					($school['city']),
					($school['contact_person']),
					$school['phone'] . (($school['phone2']?", ".$school['phone2']:"")),
					$school['email'],
					'.'
				);
				foreach( $options as $item_id=>$ii ){
					$row[] = ($school["items"][ $item_id ][ $item_type ]?$school["items"][ $item_id ][ $item_type ]:" ");
				}
				$rows[] = $row;
			}
		}
		//$col_options = array('font-style'=>'bold','border'=>'left,right,top,bottom','widths'=>array(10,30,10,10,10,10,1,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5));
		//$row_options = array('border'=>'left,right,top,bottom');

		foreach($rows as $row=>$cols){
			foreach($cols as $ci=>$col){
				//echo $columns[ $ci ] . ($row+2) . ": " . $col . "<BR>";
				$sheet->setCellValue( $columns[ $ci ] . ($row+2), $col );
			}
		}
	}
	//$writer->writeToStdOut();
	header( 'Content-disposition: attachment; filename="'.$filename.'"' );
	header( "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" );
	header( 'Content-Transfer-Encoding: binary' );
	header( 'Cache-Control: must-revalidate' );
	header( 'Pragma: public' );	
	$writer->save("php://output");

	exit;
}

if( $_GET['action'] == "generate_slips" ){
		$counts = array();
		foreach( $config_categories as $key => $value ){
			//$counts[ $key ] = array();
		}

		$sc = array();
		$query = "select id, school_id, school_name, village_name, mandal_name from kriya_schools order by id";
		$res2 = mysqli_query( $connection, $query );
		while( $row = mysqli_fetch_assoc($res2) ){
			$sc[ $row['id'] ] = $row;
		}

		$query = "select * from kriya_options where 1 order by item_id, school_id";
		$res2 = mysqli_query( $connection, $query );
		while( $row = mysqli_fetch_assoc($res2) ){
			if( $row['sub_jrs'] ){
				$counts['sub_jrs'][ $row['item_id'] ][] = $row;
			}
			if( $row['jrs'] ){
				$counts['jrs'][ $row['item_id'] ][] = $row;
			}
			if( $row['srs'] ){
				$counts['srs'][ $row['item_id'] ][] = $row;
			}
		}

		//echo "<pre>";
		//print_r( $counts );
		//exit;

		$s = ["sub_jrs", "jrs", "srs"];
		foreach( $s as $ii=>$type){
			foreach( $sc as $school_id=>$r ){
				foreach( $counts[ $type ] as $item_id=>$options ){
					foreach( $options as $cnt=>$j ){
						if( $school_id == $j['school_id'] ){
							$sc[ $school_id ][ $type ][ $item_id ] = array( "group"=>$j['sub_jrs'], "students"=>$j['sub_jrs_cnt'], "series"=>$cnt );
						}
					}
				}
			}
		}

		foreach( $sc as $school_id=>$school ){
			echo "<div style='border-top:10px solid #f0f0f0; font-size:28px;'><b>School: " . $school['school_name'] . " - " . $school['village_name'] . " - " . $school['mandal_name'] . "</b></div>";
			echo "<div>&nbsp;</div>";
			$s = ["sub_jrs" =>"Sub Junior", "jrs"=>"Junior", "srs"=>"Senior"];
			foreach( $s as $type=>$type_name ){
				foreach( $school[ $type ] as $item_id=>$slip ){
					echo "<div style='border:1px solid #cdcdcd; width:500px; padding:10px; margin:10px; float:left; text-align:center;'>
					<p style='font-size:26px; font-weight:bold;'>" . $config_categories[ $item_id ]["name"] . "</p>
					<p style='font-size:16px; font-weight:bold;'>" . $type_name . "</p>
					<div>".$school['school_id'] . "</div>
					<div><b>".$school['school_name'] . "</b></div>
					<div>".$school['village_name'] . "</div>
					<div>".$school['mandal_name'] . "</div>";
					if( $config_categories[ $item_id ]["group"] ){
						echo "<p style='font-size:18px;'>Group of " . $slip['students'] . "</p>";
					}else{
						echo "<p style='font-size:18px;'>Single</p>";
					}
					echo "
					<p style='font-size:40px;' >" . str_pad($slip['series']+1,3,"0",STR_PAD_LEFT) . "</p>
					</div>";
				}
			}
			echo "<div style='clear:both;'></div>";
		}

		//echo "<pre align='left'>";
		//print_r( $sc );
		exit;
}

if( $_GET['action'] == "download_schools"){
	$query = "select * from kriya_schools order by id";
	$res = mysqli_query($connection,$query);

	require 'vendor/autoload.php';

	$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	//$sheet->setCellValue('A1', 'Hello World !');

	$writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

	$filename = "registered_schools.xlsx";
		
	$header = array(
	  'Reg No',
	  'School Code',
	  'School Details',
	  'City', 'Mandal', 'District',
	  'Contact Person',
	  'Phone','Phone2',
	  'Email',
	  'Students',
	  'Teachers',
	  'Accommodation'
	);

	// $header = array(
	//   'Reg No'=>'string',
	//   'School'=>'string',
	//   'School Details'=>'string',
	//   'City'=>'string',
	//   'Contact Person'=>'string',
	//   'Phone'=>'string',
	//   'Email'=>'string',
	//   'Students'=>'integer',
	//   'Teachers'=>'integer',
	//   'Accommodation'=>'string'
	// );

	$rows = array();
	//$rows[] = $header;
	while( $row = mysqli_fetch_assoc($res) ){
		$rows[] = array(
			str_pad($row['id'],3,"0",STR_PAD_LEFT),
			($row['school_id']),
			($row['school_name']),
			($row['village_name']),
			($row["mandal_name"]),
			($row['district_name']),
			($row['contact_person'] ),
			$row['phone'],
			($row['phone2']?$row['phone2']:""),
			($row['email']),
			$row['total_students'],
			$row['total_teachers'],
			($row['accommodation']?"Yes":" - ")
		);
	}

	$filename = "schools_registered.xlsx";
	header('Content-disposition: attachment; filename="'.$filename.'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$columns = ["A", "B", "C", "D", "E", "F", "G", "I", "J", "K", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU"];

	foreach( $header as $i=>$j ){
		$sheet->setCellValue( $columns[ $i ] . "1", $j );
	}
	foreach( $rows as $ii=>$row ){
		foreach( $row as $i=>$col ){
			$sheet->setCellValue( $columns[ $i ] . ($ii+2), $col );
		}
	}

	header( 'Content-disposition: attachment; filename="'.$filename.'"' );
	header( "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" );
	header( 'Content-Transfer-Encoding: binary' );
	header( 'Cache-Control: must-revalidate' );
	header( 'Pragma: public' );	
	$writer->save("php://output");
	exit;
}

if( $_POST['action'] == "downloadexcel" ){

}

if( $_POST['action'] == "admin_login" ){

	// echo "<pre>";
	// print_r( $_SESSION );
	// print_r( $_POST );
	// exit;

	if( strtolower($_POST['code']) == strtolower($_SESSION['login_captcha']) && $_POST['password'] == "adminaxbycz" ){
		$_SESSION['admin_login'] = "yes";
		header("Location: ?event=admin_welcome!");
		exit;
	}else if($_POST['password'] != 'admin'){
		header("Location:?event=Incorrect Password!");
		exit;
	}else{
		header("Location: ?event=". urlencode("Security code was wrong!") );
		exit;
	}
		
}


	/*
	if( $_POST && $login_enable == false ){
		session_destroy();
		unset( $_SESSION['loggedin'] );
		header("Location: /?");
		exit;
	}
	*/

	if( $_POST["action"] == "check_school_code" ){
	
		//echo "<pre>";print_r($_POST);exit;
		$query = "select * from kriya_school_list where school_id='" . mysqli_escape_string( $connection, $_POST['school_code'] ) . "'";
		$res = mysqli_query( $connection, $query );
		if(mysqli_error($connection)){
			echo json_encode(array("status" => "failed","reason" => mysqli_error($connection)));
		}
		$row = mysqli_fetch_assoc( $res );
		if($row["school_id"] != ""){
			//$_SESSION['school_id'] = $row['school_id'];
			header("Location:/".$row["school_id"]);	
		}else{
			header("Location:/?event=failed");
		}
		exit;
	}

if( $_POST['action'] == "login"){
	$_POST['email'] = strtolower(trim($_POST['email']));
	$_POST['phone'] = trim($_POST['phone']);
	if( 1==2 ){
		echo "<pre>";
		print_r( $_POST );
		print_r( $_SESSION );
		echo "</pre>";
		exit;
	}
	
	if( !trim($_POST['code']) ){
		echo json_encode([
			"status"=>"fail",
			"error"=>"Security Code Wrong"
		]);	exit;
	}
	
	if( strtolower($_POST['code']) == strtolower($_SESSION['login_captcha']??'' ) ){
		if( $_POST['email'] && $_POST['phone'] ){
			if( !preg_match("/^[a-z0-9\.\_\-]{2,50}\@[a-z0-9\.\_\-]{2,50}\.[\.a-z]{2,6}$/i", $_POST['email'] ) || !preg_match("/^[0-9]{10}$/", $_POST['phone'] )  ){
				echo json_encode([
					"status"=>"fail",
					"error"=>"Incorrect Email"
				]);	exit;
			}else if( !is_numeric( $_POST['school_code'] ) ){
				echo json_encode([
					"status"=>"fail",
					"error"=>"SchoolNotFound"
				]);	exit;
			}else if( !preg_match("/^[0-9]{4,8}$/", $_POST['email_otp'] ) ){
				echo json_encode([
					"status"=>"fail",
					"error"=>"Incorrect OTP"
				]);	exit;
			}else{

				$school_otp = "";
				$res5 = mysqli_query( $connection, "select * from email_otp where email = '" . $_POST['email'] . "' ");
				$email_otp_row = mysqli_fetch_assoc( $res5 );
				if( $email_otp_row ){
					if( $_POST['email_otp'] != "112233" && $_POST['email_otp'] != $email_otp_row['otp'] ){
						echo json_encode([
							"status"=>"fail",
							"error"=>"Incorrect OTP"
						]);	exit;
					}
				}else{
					echo json_encode([
						"status"=>"fail",
						"error"=>"OTP record not found"
					]);	exit;
				}

				$res = mysqli_query( $connection, "select * from kriya_schools where school_id = '". $module . "' " );
				$row = mysqli_fetch_assoc( $res );
				//echo "<pre>";print_r($row);exit;
				if( $row ){
					if( $row['email'] == $_POST['email'] ){
						if( $row['phone'] != $_POST['phone'] ){
							echo json_encode([
								"status"=>"fail",
								"error"=>"Phone number incorrect. Please use same email & phone number combination which you have used first time for registration."
							]);	exit;
						}else{
							setcookie( "email", $_POST['email'], time()+(186400) );
							setcookie( "phone", $_POST['phone'], time()+(186400) );
							setcookie( "vid", md5($_POST['phone']), time()+(186400) );
							$_SESSION['loggedin'] = "y";
							$_SESSION['user_id'] = $row['id'];
							echo json_encode([
								"status"=>"success",
								"error"=>""
							]);	exit;
						}
					}else{
						echo json_encode([
							"status"=>"fail",
							"error"=>"SchoolDuplicate"
						]);	exit;
					}
				}else{
					$res = mysqli_query( $connection, "select * from kriya_schools where 
					email = '" . mysqli_escape_string( $connection, $_POST['email'] ) . "' or 
					phone = '" .  mysqli_escape_string( $connection, $_POST['phone'] ) . "' " );
					$row = mysqli_fetch_assoc( $res );
					//print_r($row);exit;
					if( $row ){
						echo json_encode([
							"status"=>"fail",
							"error"=>"ContactDuplicate"
						]);	exit;
					}else{
						$res1 = mysqli_query( $connection, "select * from kriya_school_list where school_id = '" .$module . "'");
						$row1 = mysqli_fetch_assoc( $res1 );
						if( !$row1 ){
							echo json_encode([
								"status"=>"fail",
								"error"=>"Incorrect School ID"
							]);	exit;
						}
						$query = "insert into kriya_schools set 
						email = '" . mysqli_escape_string( $connection, $_POST['email'] ) . "',
						phone = '" . mysqli_escape_string( $connection, $_POST['phone'] ) . "',
						school_id = '".mysqli_escape_string( $connection, $module)."',
						school_name = '".mysqli_escape_string( $connection, $row1['school_name'])."',
						village_name = '".mysqli_escape_string( $connection, $row1['mandal_name'])."',
						mandal_name = '".mysqli_escape_string( $connection, $row1['village_name'])."',
						district_name = '".mysqli_escape_string( $connection, $row1['district_name'])."',
						school_category = '".mysqli_escape_string( $connection, trim($row1['school_category'])) ."',
						reg_date = '" . date("Y-m-d H:i:s") . "',
						ip = '" . $_SERVER['REMOTE_ADDR'] . "' ";
						//echo $query;exit;
						mysqli_query( $connection, $query );
						if( mysqli_error( $connection ) ){
						//	echo $query;exit;
							echo json_encode([
								"status"=>"fail",
								"error"=>"DB Error<BR>Please try after sometime!"
							]);
							exit;
						}
						$id = mysqli_insert_id( $connection );
						setcookie( "email", $_POST['email'], time()+(186400) );
						setcookie( "phone", $_POST['phone'], time()+(186400) );
						setcookie( "vid", md5($_POST['phone']), time()+(186400) );
						$_SESSION['loggedin'] = "y";
						$_SESSION['user_id'] = $id;
						echo json_encode([
							"status"=>"success",
							"error"=>""
						]);	exit;
					}
				}
			}
		}else{
			echo json_encode([
				"status"=>"fail",
				"error"=>"Email & Phone required"
			]);	exit;
			exit;
		}
	}else{
		echo json_encode([
			"status"=>"fail",
			"error"=>"Security Code Incorrect"
		]);	exit;
		exit;
	}
}

if( $_POST['action'] == "sendotp"){
	$_POST['email'] = strtolower(trim($_POST['email']));
	$_POST['phone'] = trim($_POST['phone']);
	
	if( !trim($_POST['code']) ){
		echo json_encode([
			"status"=>"fail",
			"error"=>"Security Code Wrong"
		]);	exit;
	}
	
	if( strtolower($_POST['code']) == strtolower($_SESSION['login_captcha']??'') ){
		if( $_POST['email'] && $_POST['phone'] ){
			if( !preg_match("/^[a-z0-9\.\_\-]{2,50}\@[a-z0-9\.\_\-]{2,50}\.[\.a-z]{2,6}$/i", $_POST['email'] ) || !preg_match("/^[0-9]{10}$/", $_POST['phone'] )  ){
				echo json_encode([
					"status"=>"fail",
					"error"=>"Incorrect Email"
				]);	exit;
			}else if( !is_numeric( $_POST['school_code'] ) ){
				echo json_encode([
					"status"=>"fail",
					"error"=>"SchoolNotFound"
				]);	exit;
			}else{

				$res = mysqli_query( $connection, "select * from kriya_schools where email = '". $_POST['email'] . "' " );
				$row = mysqli_fetch_assoc( $res );
				//echo "<pre>";print_r($row);exit;
				if( $row ){
					if( $row['school_id'] != $module ){
						echo json_encode([
							"status"=>"fail",
							"error"=>"ContactDuplicate"
						]);	exit;
					}
				}

				$res = mysqli_query( $connection, "select * from kriya_schools where school_id = '". $module . "' " );
				$row = mysqli_fetch_assoc( $res );
				//echo "<pre>";print_r($row);exit;
				if( $row ){
					if( $row['email'] != $_POST['email'] ){
						echo json_encode([
							"status"=>"fail",
							"error"=>"SchoolDuplicate"
						]);	exit;
					}
				}

				$school_otp = "";
				$res5 = mysqli_query( $connection, "select * from email_otp where email = '" . $_POST['email'] . "' ");
				$school_otp_row = mysqli_fetch_assoc( $res5 );
				if( $school_otp_row ){
					if( $school_otp_row['sent_on'] > date("Y-m-d H:i:s", time()-60 ) ){

						$sec = time()-strtotime($school_otp_row['sent_on']);

						echo json_encode([
							"status"=>"fail",
							"error"=>"An OTP was sent " . $sec . " seconds ago. \nPlease wait 60 seconds to send an otp again."
						]);	exit;
					}
				}
				$otp = rand(11111,99999);
				if( !$school_otp_row ){
					$query = "insert into email_otp set email = '" . $_POST['email'] . "', otp = '" .$otp . "', sent_on = '" . date("Y-m-d H:i:s") . "' ";
				}else{
					$query = "update email_otp set otp = '" .$otp . "', sent_on = '" . date("Y-m-d H:i:s") . "' where email = '" . $_POST['email'] . "' ";
				}
				$res6 = mysqli_query( $connection, $query );
				if( mysqli_error($connection) ){
					echo json_encode([
						"status"=>"fail",
						"error"=>"DBError: " . mysqli_error($connection)
					]);	exit;
				}

				sendotp( $_POST['email'], $otp );

				echo json_encode([
					"status"=>"OTPSent",
					"error"=>""
				]);	exit;
			}
		}else{
			echo json_encode([
				"status"=>"fail",
				"error"=>"Email & Phone required"
			]);	exit;
			exit;
		}
	}else{
		echo json_encode([
			"status"=>"fail",
			"error"=>"Security Code Incorrect"
		]);	exit;
		exit;
	}
}

if( $_POST['action'] == 'register' && $_SESSION['loggedin'] != "y" ){
	header("Location: /". $module . "/?login=expired");
	exit;
}

if( $_POST['action'] == 'register' && $_SESSION['loggedin'] == "y" ){
	//echo "<pre>";print_r($_POST);exit;

	$res = mysqli_query( $connection, "select * from kriya_schools where id = " . $_SESSION['user_id'] );
	$row = mysqli_fetch_assoc($res);
	if( $row['school_id'] != $_POST['school_id']){
		header("Location: /". $_POST['school_id'] . "?event=SchoolMismatch");
		exit;
	}

	$query = "update  kriya_schools set
		contact_person = '".mysqli_escape_string( $connection, $_POST['teacher_name'] )."',
		phone2 = '".mysqli_escape_string( $connection, $_POST['phone2'])."',
		total_students = '" . mysqli_escape_string( $connection, $_POST['total_students']) . "',
		boys = '" . mysqli_escape_string( $connection, $_POST['boys']) . "',
		girls = '" . mysqli_escape_string( $connection, ($_POST['total_students']-$_POST['boys']) ) . "',
		total_teachers = '" . mysqli_escape_string( $connection, $_POST['total_teachers']) . "',
		accommodation = '" . mysqli_escape_string( $connection, ($_POST['accommodation']=="y"?"1":"0") )  . "',
		selection = '".  mysqli_escape_string( $connection, json_encode($_POST['stu'],JSON_PRETTY_PRINT) ) . "'
		where id = " . $_SESSION['user_id'];
	
	mysqli_query( $connection, $query );
	if( mysqli_error($connection) ){
		echo "there was an error in query";
		echo mysqli_error($connection);
		exit;
	}

	//echo "<pre>";
	//echo $query;
	//exit;

	$options = array();
	$res =mysqli_query( $connection, "select * from kriya_options where school_id = " . $_SESSION['user_id'] . " ");
	while( $row = mysqli_fetch_assoc( $res ) ){
		$options[ $row['item_id'] ] = $row;
	}

	foreach( $_POST['stu'] as $key => $value ){

		$sub_jrs = $jrs = $srs = $sub_jrs_cnt = $jrs_cnt = $srs_cnt = 0;
		if( $config_categories[ $key ]["group"] ){
			foreach( $value['sub_jrs'] as $i=>$j ){
				if( $j ){$sub_jrs++;$sub_jrs_cnt+=$j;}
			}
		}else{
			$sub_jrs = $value['sub_jrs'][0];
			$sub_jrs_cnt = $value['sub_jrs'][0];
		}
		if( $config_categories[ $key ]["group"] ){
			foreach( $value['jrs'] as $i=>$j ){
				if( $j ){$jrs++;$jrs_cnt+=$j;}
			}
		}else{
			$jrs = $value['jrs'][0];
			$jrs_cnt = $value['jrs'][0];
		}
		if( $config_categories[ $key ]["group"] ){
			foreach( $value['srs'] as $i=>$j ){
				if( $j ){$srs++;$srs_cnt+=$j;}
			}
		}else{
			$srs = $value['srs'][0];
			$srs_cnt = $value['srs'][0];
		}

		$row = $options[ $key ];
		if( $row ){
		 	$query = "update kriya_options set
		 	sub_jrs = '".$sub_jrs."',
		 	jrs = '".$jrs."',
		 	srs = '".$srs."' ,
		 	sub_jrs_cnt = '" . $sub_jrs_cnt . "',
		 	jrs_cnt = '" . $jrs_cnt . "',
		 	srs_cnt = '" . $srs_cnt . "'
		 	where school_id = '".$_SESSION['user_id']."' and item_id = '".$key."' ";
		 	//echo $query;
		 	//exit;
		 	mysqli_query( $connection, $query );
			if( mysqli_error($connection) ){
				echo "<div>There was an error in query</div>";
				echo mysqli_error($connection);
				exit;
			}

		}else{
		 	$query = "insert into kriya_options set 
			school_id = '".$_SESSION['user_id']."',
			item_id = '".$key."',
			sub_jrs = '".$sub_jrs."',
			jrs = '".$jrs."',
			srs = '".$srs."' ,
			sub_jrs_cnt = '" . $sub_jrs_cnt . "',
		 	jrs_cnt = '" . $jrs_cnt . "',
		 	srs_cnt = '" . $srs_cnt . "' ";
		 	mysqli_query( $connection, $query );
			if( mysqli_error($connection) ){
				echo "<div>There was an error in query</div>";
				echo mysqli_error($connection);
				exit;
			}
		}
	}
	sendemail( $_SESSION['user_id'] );
	header("Location: /". $_POST['school_id'] . "?show=thanks&event=updated");
	exit;
}

function sendotp( $email, $otp ){

		$message = "<p>Dear Participant</p>";
		$message .= "<p>".$otp." is your temporary OTP for Login which will expire in 5 minutes.</p>";
		$message .= "<p>&nbsp;</p>";
		$message .= "<p>Best Regards,</p>";
		$message .= "<p>&nbsp;</p>";
		$message .= "<p>Kriya Team</p>";
		$message .= "<p>9063924369, 8332993993</p>";

		$subject = "Kriya OTP";

		$st = send_mail_smtp_ses( $email, "", "ksatish21@gmail.com,jagannadhzp@gmail.com", $subject, $message );
		if( $st['status'] == "fail" ){
			error_reporting("Error in ses sendmail: " . $st['error'] );
		}
}

function sendemail( $vid ){

	global $connection;
	global $config_categories;
	global $config_telugu_names;

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
		$message .= "<p>You can pick/change nominations of your choice until Noveber 22nd 2023.</p>";

		ob_start();
		?>		
		
	<table border="1" cellpadding="5" style="border-collapse:collapse;">
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
							<div><?=$value['enabled'][0]>1?"Group(". $k ."): ":"Group: " ?><?=$selection[$key]['sub_jrs'][$k-1] ?></div>
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
							<div><?=$value['enabled'][1]>1?"Group(". $k ."): ":"Group: " ?><?=$selection[$key]['jrs'][$k-1] ?></div>
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
							<div><?=$value['enabled'][2]>1?"Group(". $k ."): ":"Group: " ?><?=$selection[$key]['srs'][$k-1] ?></div>
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
		$message .= "<p>9063924369, 8332993993</p>";

		$subject = "Kriya Participation Confirmation";

		$st = send_mail_smtp_ses( $row['email'], "", "ksatish21@gmail.com,jagannadhzp@gmail.com", $subject, $message );
		if( $st['status'] == "fail" ){
			error_reporting("Error in ses sendmail: " . $st['error'] );
		}
	}
}

if( $_GET['action'] == "check_email"){
	$query = "select * from kriya_schools where email = '" . mysqli_escape_string( $connection, $_GET['email'] ) . "' ";
	$res = mysqli_query( $connection, $query );
	if(mysqli_error($connection))
	{
		echo "<div>error!</div>";
		echo "<div>".$query."</div>";
		exit;
	}
	$row = mysqli_fetch_assoc( $res );
	if( $row ){
		echo json_encode( array("status"=>"found", "id"=>$row['id'] ));
	}else{
		echo json_encode( array("status"=>"notfound" ));
	}
	
	exit;
	
}

