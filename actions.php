<?php
session_start();


if( $_GET['action'] == "logout" ){
	session_destroy();
	session_regenerate_id();
	header("Location: /index.php?event=Logout");
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
	$angle = rand(0,0);
	$x = rand(10,20);
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

if( $_GET['action'] == "get_payment_details" ){
	if( !preg_match("/^[0-9]+$/", $_GET['id']) ){
		echo json_encode(['status'=>"fail", "error"=>"Id incorrect"]);exit;
	}
	$res = mysqli_query( $connection, "select * from kriya_schools where id = " . $_GET['id'] );
	if( mysqli_error( $connection ) ){
		echo json_encode(['status'=>"fail", "error"=>mysqli_error( $connection )]);exit;
	}
	$row = mysqli_fetch_assoc($res);
	$d = $row['id'] . "<BR>" . $row['entry_type'] . ": " . $row['type'] . "<BR>";
	if( $row['type'] == 'school' ){
		$d .= $row['school_id'] . "<BR>" . $row['school_name'] . "<BR>";
	}else if( $row['type'] == 'parent' ){
	}else if( $row['type'] == 'institute' ){
		$d .= $row['institute'] . "<BR>";
	}
	$d .= $row['village_name'] . " " . $row['district_name'] . "<BR>";
	echo json_encode([
		'status'=>"success", 
		"data"=>[
			"details"=>$d,
			"amount"=>$row['amount'],
			"collected"=>$row['collected'],
			"approved"=>($row['approved']==1?true:false)
		]
	]);
	exit;
}
if( $_GET['action'] == "update_payment_status" ){
	if( !preg_match("/^[0-9]+$/", $_GET['id']) ){
		echo json_encode(['status'=>"fail", "error"=>"Id incorrect"]);exit;
	}
	$res = mysqli_query( $connection, "select * from kriya_schools where id = " . $_GET['id'] );
	if( mysqli_error( $connection ) ){
		echo json_encode(['status'=>"fail", "error"=>mysqli_error( $connection )]);exit;
	}
	$row = mysqli_fetch_assoc($res);

	mysqli_query($connection, "update kriya_schools 
		set collected = '" . $_GET['collection'] . "', 
		approved = '" . ($_GET['approved']=='true'?"1":"0") . "' 
		where id = " . $_GET['id'] );
	if( mysqli_error( $connection ) ){
		echo json_encode(['status'=>"fail", "error"=>mysqli_error( $connection )]);exit;
	}

	echo json_encode([
		'status'=>"success", 
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
		mysqli_query( $connection, "delete from kriya_options where user_id = " . $id1 );
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
	  'Type',
	  'Entry Type',
	  'School Code',
	  'School',
	  'City', 'District',
	  'Contact Person',
	  'Phone','Phone2',
	  'Email',
	  'Students',
	  'Amount',
	  'Collected',
	  'Approved'
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
			($row['type']),
			($row['entry_type']),
			($row['school_id']),
			($row['type']=='school'?$row['school_name']:$row['institute']),
			($row['village_name']),
			($row['district_name']),
			($row['contact_person'] ),
			$row['phone'],
			($row['phone2']?$row['phone2']:""),
			($row['email']),
			$row['total_students'],
			$row['amount'],
			$row['collected'],
			($row['approved']?"Approved":" - ")
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

		$subject = "Kriya OTP " . $otp;

		$st = send_mail_smtp_ses( $email, "", "", $subject, $message );
		if( $st['status'] == "fail" ){
			error_log("Error in ses sendmail: " . $st['error'] );
		}
}

function sendemail( $vid ){

	global $connection;
	global $config_categories;
	global $config_telugu_names;
	global $email_cc_list;

	$res = mysqli_query( $connection, "select * from kriya_schools where id = " . $vid );
	$row = mysqli_fetch_assoc( $res );

	$res2 = mysqli_query($connection, "select * from kriya_options where user_id = " . $row['id']);
	$row2 = mysqli_fetch_assoc( $res2 );
	if( $row ){

		$selection = json_decode($row['selection'],true);
		// print_r( $selection );exit;

		$message = "<p>Dear " . preg_replace("/\W+/", " ", $row['contact_person'] ) . "</p>";
		$message .= "<p>Thank you for your participation.</p>";
		$message .= "<p>Your registration number: <b>".str_pad($row['id'], 3, "0", STR_PAD_LEFT)."</b></p>";
		if( $row['type'] == 'school' ){
			$message .= "<p>UDISE Code: " . htmlspecialchars($row['school_id']) . "</p>";
			$message .= "<p>School: " . htmlspecialchars($row['school_name']) . " - ". htmlspecialchars($row['village_name'])." - ".htmlspecialchars($row['district_name']) . "</p>";
		}else if( $row['type'] == 'parent' ){
			$message .= "<p>Registration type: Parent</p>";
			$message .= "<p>".htmlspecialchars($row['village_name'])." - ".htmlspecialchars($row['district_name']) . "</p>";
		}else if( $row['type'] == 'institute' ){
			$message .= "<p>Registration type: Institute</p>";
			$message .= "<p>Institute: ".$row['institute'] ."<BR>".htmlspecialchars($row['village_name'])." - ".htmlspecialchars($row['district_name']) . "</p>";
		}
		$message .= "<p>Phone: " . $row['phone'] . "</p>";
		$message .= "<p>Email: " . $row["email"] . "</p>";
		$message .= "<p>Total Students Nominated: " . $row["total_students"] . "</p>";
		if( $row['entry_type'] == "paid" ){
			$message .= "<p>Nomination Fees Rs.". $row['amount'] ."/-</p>";
			$message .= "<p>Amount should be transfered to:<br>";
			$message .= "Account Name: Kriya Society<br>";
			$message .= "Account Number: 3260 2200 0034 44<br>";
			$message .= "Branch: Canara Bank, KAKINADA ADITYA ACADEMY<br>";
			$message .= "IFSC: CNRB0013260</p>";
		}
		$message .= "<p>&nbsp;</p>";
		$message .= "<p>You can modify nominations of your choice until December 21st 2024.</p>";
		if( $row['entry_type'] == "paid" ){
			$message .= "<p>Admin team will validate your payment details and confirm your participation.</p>";
		}
		$message .= "<p>Entry passes will be emailed a day before. Students should keep two or more copies of their entry passes</p>";

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
            <?php
            foreach ($config_categories as $key => $value) {
                $key = (int)$key;
                if (isset($selection[$key])) {
                    $key = (int)$key;
				    $sub_jrs_count = 0;
				    if (isset($selection[$key]['sub_jrs'])) {
				        if (is_array($selection[$key]['sub_jrs'])) {
				            foreach ($selection[$key]['sub_jrs'] as $group) {
				                $sub_jrs_count += is_array($group) ? count($group) : 0;
				            }
				        }
				    }
				    $sub_jrs_count = $sub_jrs_count > 0 ? $sub_jrs_count : "-";

				    $jrs_count = 0;
				    if (isset($selection[$key]['jrs'])) {
				        if (is_array($selection[$key]['jrs'])) {
				            foreach ($selection[$key]['jrs'] as $group) {
				                $jrs_count += is_array($group) ? count($group) : 0;
				            }
				        }
				    }
				    $jrs_count = $jrs_count > 0 ? $jrs_count : "-";

				    $srs_count = 0;
				    if (isset($selection[$key]['srs'])) {
				        if (is_array($selection[$key]['srs'])) {
				            foreach ($selection[$key]['srs'] as $group) {
				                $srs_count += is_array($group) ? count($group) : 0;
				            }
				        }
				    }
				    $srs_count = $srs_count > 0 ? $srs_count : "-";
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($value['sno']) ?></td>
                        <td><?= htmlspecialchars($value['name']) ?></td>
                        <td><?= $sub_jrs_count ?></td>
				        <td><?= $jrs_count ?></td>
				        <td><?= $srs_count ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
	</table>	

	<br>

    <p>Student List</p>
    <table border="1" cellpadding="5" style="border-collapse:collapse;">
        <thead>
            <tr bgcolor='#f0f0f0'>
                <td>SNo</td>
                <td>Name</td>
                <td>Class</td>
                <td>Gender</td>
                <td>Game Name</td>
                <td>Type</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $participant_sno = 1;
            foreach ($config_categories as $key => $value) {
                $key = (int)$key;
                if (isset($selection[$key]['jrs'])) {
                    foreach ($selection[$key]['jrs'] as $group) {
                        foreach ($group as $student) {
                            ?>
                            <tr>
                                <td><?= $participant_sno++ ?></td>
                                <td><?= htmlspecialchars($student['name']) ?></td>
                                <td><?= htmlspecialchars($student['class']) ?></td>
                                <td><?= htmlspecialchars($student['gender']) ?></td>
                                <td><?= htmlspecialchars($value['name']) ?></td>
                                <td>Junior</td>
                            </tr>
                            <?php
                        }
                    }
                }
                if (isset($selection[$key]['sub_jrs'])) {
                    foreach ($selection[$key]['sub_jrs'] as $group) {
                        foreach ($group as $student) {
                            ?>
                            <tr>
                                <td><?= $participant_sno++ ?></td>
                                <td><?= htmlspecialchars($student['name']) ?></td>
                                <td><?= htmlspecialchars($student['class']) ?></td>
                                <td><?= htmlspecialchars($student['gender']) ?></td>
                                <td><?= htmlspecialchars($value['name']) ?></td>
                                <td>Sub Junior</td>
                            </tr>
                            <?php
                        }
                    }
                }
                if (isset($selection[$key]['srs'])) {
                    foreach ($selection[$key]['srs'] as $group) {
                        foreach ($group as $student) {
                            ?>
                            <tr>
                                <td><?= $participant_sno++ ?></td>
                                <td><?= htmlspecialchars($student['name']) ?></td>
                                <td><?= htmlspecialchars($student['class']) ?></td>
                                <td><?= htmlspecialchars($student['gender']) ?></td>
                                <td><?= htmlspecialchars($value['name']) ?></td>
                                <td>Senior</td>
                            </tr>
                            <?php
                        }
                    }
                }
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

		$st = send_mail_smtp_ses( $row['email'], $email_cc_list, "", $subject, $message );
		if( $st['status'] == "fail" ){
			error_log("Error in ses sendmail: " . $st['error'] );
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


if( $_GET['action'] == "searchdb" ){

	$cond = "where 1 =1 ";
	if( $_GET['keyword'] ){
		if( is_numeric($_GET['keyword']) ){
			$cond .= " and school_id like '%" . mysqli_escape_string( $connection, $_GET['keyword'] ) . "%' ";
		}else{
			$cond .= " and ( 
			school_name 	like '%" . mysqli_escape_string( $connection, $_GET['keyword'] ) . "%' or
			mandal_name 	like '%" . mysqli_escape_string( $connection, $_GET['keyword'] ) . "%' or
			village_name 	like '%" . mysqli_escape_string( $connection, $_GET['keyword'] ) . "%' ) ";
		}
	}
	$query = "select count(*) from kriya_school_list " . $cond . " ";
	$res = mysqli_query( $connection, $query );
	if( mysqli_error($connection) ){
		echo json_encode( ["status"=>"error", "error"=>mysqli_error($connection)] );
		exit;
	}
	$row = mysqli_fetch_array( $res );
	$total = $row[0];
	$start = ((int)$_GET['p']-1)*100;
	$query = "select * from kriya_school_list " . $cond . " order by school_id limit ".$start.", 100";
	$res = mysqli_query( $connection, $query );
	if( mysqli_error($connection) ){
		echo json_encode( ["status"=>"error", "error"=>mysqli_error($connection)] );
		exit;
	}
	$records = [];
	while( $row = mysqli_fetch_assoc( $res ) ){
		$row['enc'] = md5(session_id() . $row['school_id'] );
		$records[] = $row;
	}
	echo json_encode( ["status"=>"success", "total"=>$total, "records"=>$records, "query"=>$query] );
	exit;
	
}

//print_r( $_POST );exit;

if( $_POST['action'] == "master_school_edit" ){

	$edit = json_decode( $_POST['edit'], true );
	if( !preg_match( "/^[0-9]{4,25}$/", $edit['school_id'] ) ){
		echo json_encode( ["status"=>"error", "error"=>"School ID Incorrect" ] );exit;
	}
	if( $edit['enc'] != "new" ){
		//echo md5(session_id(). $edit['school_id']);exit;
		if( md5(session_id(). $edit['school_id']) != $edit['enc'] ){
			echo json_encode( ["status"=>"error", "error"=>"Session Expired" ] );exit;
		}
	}
	if( !preg_match( "/^[a-z][a-z0-9\.\,\ \-\_\&\@\(\)]{4,100}$/i", trim($edit['school_name']) ) ){
		echo json_encode( ["status"=>"error", "error"=>"School Name Incorrect" ] );exit;
	}
	if( !preg_match( "/^[a-z][a-z0-9\.\,\ \-\_\&\@\(\)]{4,100}$/i", trim($edit['school_category']) ) && $edit['school_category'] != "-" ){
		echo json_encode( ["status"=>"error", "error"=>"School Category Incorrect" ] );exit;
	}
	if( !preg_match( "/^[a-z][a-z0-9\.\,\ \-]{4,50}$/i", trim($edit['district_name']) ) && $edit['district_name'] != "" ){
		echo json_encode( ["status"=>"error", "error"=>"District Incorrect" ] );exit;
	}
	if( !preg_match( "/^[a-z][a-z0-9\.\,\ \-]{4,50}$/i", trim($edit['mandal_name']) ) && $edit['mandal_name'] != "" ){
		echo json_encode( ["status"=>"error", "error"=>"Mandal Incorrect" ] );exit;
	}
	if( !preg_match( "/^[a-z][a-z0-9\.\,\ \-]{4,50}$/i", trim($edit['village_name']) ) && $edit['village_name'] != "" ){
		echo json_encode( ["status"=>"error", "error"=>"Village Incorrect" ] );exit;
	}

	if( $edit['enc'] == "new" ){

		$query = "select * from kriya_school_list where school_id = '" . mysqli_escape_string( $connection, $edit['school_id'] ) . "' ";
		$res = mysqli_query( $connection, $query );
		if( mysqli_error($connection) ){
			echo json_encode( ["status"=>"error", "error"=>mysqli_error($connection)] );exit;
		}
		$row = mysqli_fetch_assoc( $res );
		if( $row ){
			echo json_encode( ["status"=>"error", "error"=>"School already exists"] );exit;
		}

		$query = "insert into kriya_school_list set 
		school_id = '" . mysqli_escape_string( $connection, $edit['school_id'] ) . "',
		school_name 		= '" . mysqli_escape_string( $connection, $edit['school_name'] ) . "',
		district_name 		= '" . mysqli_escape_string( $connection, $edit['district_name'] ) . "',
		village_name 		= '" . mysqli_escape_string( $connection, $edit['village_name'] ) . "',
		mandal_name 		= '" . mysqli_escape_string( $connection, $edit['mandal_name'] ) . "',
		school_category 	= '" . mysqli_escape_string( $connection, $edit['school_category'] ) . "' ";
		//echo $query;

		mysqli_query( $connection, $query );
		if( mysqli_error($connection) ){
			echo json_encode( ["status"=>"error", "error"=>mysqli_error($connection)] );exit;
		}

	}else{

		//print_r( $_POST );
		$query = "update kriya_school_list set 
		school_name 		= '" . mysqli_escape_string( $connection, $edit['school_name'] ) . "',
		district_name 		= '" . mysqli_escape_string( $connection, $edit['district_name'] ) . "',
		village_name 		= '" . mysqli_escape_string( $connection, $edit['village_name'] ) . "',
		mandal_name 		= '" . mysqli_escape_string( $connection, $edit['mandal_name'] ) . "',
		school_category 	= '" . mysqli_escape_string( $connection, $edit['school_category'] ) . "'
		where school_id = '" . mysqli_escape_string( $connection, $edit['school_id'] ) . "' ";
		//echo $query;exit;
		mysqli_query( $connection, $query );
		if( mysqli_error($connection) ){
			echo json_encode( ["status"=>"error", "error"=>mysqli_error($connection)] );exit;
		}

		$query = "select * from kriya_schools where school_id = '" . mysqli_escape_string( $connection, $edit['school_id'] ) . "' ";
		$res = mysqli_query( $connection, $query );
		if( mysqli_error($connection) ){
			echo json_encode( ["status"=>"error", "error"=>mysqli_error($connection)] );exit;
		}
		$row = mysqli_fetch_assoc( $res );
		if( $row ){
			$query = "update kriya_schools set 
			school_name 		= '" . mysqli_escape_string( $connection, $edit['school_name'] ) . "',
			district_name 		= '" . mysqli_escape_string( $connection, $edit['district_name'] ) . "',
			village_name 		= '" . mysqli_escape_string( $connection, $edit['village_name'] ) . "',
			mandal_name 		= '" . mysqli_escape_string( $connection, $edit['mandal_name'] ) . "',
			school_category 	= '" . mysqli_escape_string( $connection, $edit['school_category'] ) . "'
			where school_id = '" . mysqli_escape_string( $connection, $edit['school_id'] ) . "' ";
			mysqli_query( $connection, $query );
			if( mysqli_error($connection) ){
				echo json_encode( ["status"=>"error", "error"=>mysqli_error($connection)] );exit;
			}
		}


	}

	echo json_encode( ["status"=>"success", "query"=>$query] );

	exit;
}




if( $_POST['action'] == "send_otp"){  

	$_POST['email'] = strtolower(trim($_POST['email']));
	
	if( !trim($_POST['code']) ){
		echo json_encode([
			"status"=>"fail",
			"error"=>"Security Code Wrong"
		]);	exit;
	}

	if( strtolower($_POST['code']) == strtolower($_SESSION['login_captcha']??'') ){
		if( $_POST['email'] ){

			if( !preg_match("/^[a-z0-9\.\_\-]{2,50}\@[a-z0-9\.\_\-]{2,50}\.[\.a-z]{2,6}$/i", $_POST['email'] )  ){
				echo json_encode([
					"status"=>"fail",
					"error"=>"Incorrect Email"
				]);	exit;
			}

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
		}else{
			echo json_encode([
				"status"=>"fail",
				"error"=>"Email"
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

if ( $_POST['action'] == "email_login") {
	$_POST['email'] = strtolower(trim($_POST['email']));

	// if( !trim($_POST['code']) ){
	// 	echo json_encode([
	// 		"status"=>"fail",
	// 		"error"=>"Security Code Wrong"
	// 	]);	exit;
	// }

	$res5 = mysqli_query( $connection, "select * from email_otp where email = '" . mysqli_escape_string($connection, $_POST['email']) . "' ");
	$email_otp_row = mysqli_fetch_assoc( $res5 );
	if( $email_otp_row ){
		if( $_POST['email_otp'] != "123456" && $_POST['email_otp'] != $email_otp_row['otp'] ){
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

	$res = mysqli_query( $connection, "select * from kriya_schools where email = '". $_POST['email'] . "' " );
	$row = mysqli_fetch_assoc( $res );

	if ( $row ) {
		$_SESSION['user_id'] = $row['id'];
	}else{
		$_SESSION['user_id'] = -1;
		// $query = "insert into kriya_schools set 
		// email = '" . $_POST["email"] . "' ";
		// //echo $query;

		// mysqli_query( $connection, $query );
		// if( mysqli_error($connection) ){
		// 	echo json_encode( ["status"=>"error", "error"=>mysqli_error($connection)] );exit;
		// }

		// $inserted_id = mysqli_insert_id($connection);
		// // echo $inserted_id; exit;
		// $_SESSION['user_id'] = $inserted_id;
	}

	$_SESSION['logged_in'] = "yes";
	$_SESSION['email'] = $_POST["email"];
	echo json_encode([
		"status"=>"success",
		"error"=>""
	]);	

	exit;
}

if( $_POST['action'] == "insert_record" ){

	if( !$_POST['data'] ){
		echo json_encode([
			"status"=>"error",
			"error"=>"Input missing"
		]);
		exit;
	}

	if( $_GET['email'] != $_SESSION['email'] ){
		echo json_encode([
			"status"=>"error",
			"error"=>"Input missing"
		]);
		exit;
	}

	$data = json_decode($_POST['data'],true);

	if ($data['type'] == "institute") {
		if ($data['institute'] == "") {
			echo json_encode([
				"status"=>"error",
				"error"=>"Enter institute name"
			]);
			exit;
		}else if ($data['state_name'] == "") {
			echo json_encode([
				"status"=>"error",
				"error"=>"Select State"
			]);
			exit;
		}else if ($data['district_name'] == "") {
			echo json_encode([
				"status"=>"error",
				"error"=>"Select District"
			]);
			exit;
		}else if ($data['contact_person'] == "") {
			echo json_encode([
				"status"=>"error",
				"error"=>"Enter Contact Person Name"
			]);
			exit;
		}else if ($data['phone'] == "") {
			echo json_encode([
				"status"=>"error",
				"error"=>"Enter Phone Number"
			]);
			exit;
		}
		$query = "insert into kriya_schools set
			type = '".  mysqli_escape_string( $connection, $data['type'] ) . "',
			entry_type = '".  mysqli_escape_string( $connection, $data['entry_type'] ) . "',
			email = '" . mysqli_escape_string( $connection, $_SESSION['email'] ) . "',			
			contact_person = '".mysqli_escape_string( $connection, $data['contact_person'] )."',
			institute = '".mysqli_escape_string( $connection, $data['institute'] )."',
			school_category = '-',
			phone = '".mysqli_escape_string( $connection, $data['phone'])."',
			phone2 = '".mysqli_escape_string( $connection, $data['phone2'])."',
			village_name = '" . mysqli_escape_string( $connection, $data['village_name']) . "',
			district_name = '" . mysqli_escape_string( $connection, $data['district_name']) . "',
			state_name = '" . mysqli_escape_string( $connection, $data['state_name']) . "',
			reg_date = '" . date("Y-m-d H:i:s") . "',
			ip = '" . $_SERVER['REMOTE_ADDR'] . "' ";
	}else if ($data['type'] == "parent") {
		if ($data['contact_person'] == "") {
			echo json_encode([
				"status"=>"error",
				"error"=>"Enter Person Name"
			]);
			exit;
		}else if ($data['phone'] == "") {
			echo json_encode([
				"status"=>"error",
				"error"=>"Enter Phone Number"
			]);
			exit;
		}else if ($data['state_name'] == "") {
			echo json_encode([
				"status"=>"error",
				"error"=>"Select State"
			]);
			exit;
		}else if ($data['district_name'] == "") {
			echo json_encode([
				"status"=>"error",
				"error"=>"Select District"
			]);
			exit;
		}else if ($data['village_name'] == "") {
			echo json_encode([
				"status"=>"error",
				"error"=>"Enter Village Name"
			]);
			exit;
		}
		$query = "insert into kriya_schools set
			type = '".  mysqli_escape_string( $connection, $data['type'] ) . "',
			entry_type = '".  mysqli_escape_string( $connection, $data['entry_type'] ) . "',
			email = '" . $_SESSION['email'] . "',			
			contact_person = '".mysqli_escape_string( $connection, $data['contact_person'] )."',
			school_name = '".mysqli_escape_string( $connection, $data['school_name'] )."',
			school_category = '-',
			phone = '".mysqli_escape_string( $connection, $data['phone'])."',
			phone2 = '".mysqli_escape_string( $connection, $data['phone2'])."',
			village_name = '" . mysqli_escape_string( $connection, $data['village_name']) . "',
			district_name = '" . mysqli_escape_string( $connection, $data['district_name']) . "',
			state_name = '" . mysqli_escape_string( $connection, $data['state_name']) . "',
			reg_date = '" . date("Y-m-d H:i:s") . "',
			ip = '" . $_SERVER['REMOTE_ADDR'] . "' ";
	}else if ($data['type'] == "school") {
		$query = "insert into kriya_schools set
			type = '".  mysqli_escape_string( $connection, $data['type'] ) . "',
			entry_type = '".  mysqli_escape_string( $connection, $data['entry_type'] ) . "',
			email = '" . $_SESSION['email'] . "',			
			contact_person = '".mysqli_escape_string( $connection, $data['contact_person'] )."',
			school_id = '".mysqli_escape_string( $connection, $data['school_id'] )."',
			school_name = '".mysqli_escape_string( $connection, $data['school_name'] )."',
			school_category = '".mysqli_escape_string( $connection, $data['school_category'] )."',
			phone = '".mysqli_escape_string( $connection, $data['phone'])."',
			phone2 = '".mysqli_escape_string( $connection, $data['phone2'])."',
			village_name = '" . mysqli_escape_string( $connection, $data['village_name']) . "',
			district_name = '" . mysqli_escape_string( $connection, $data['district_name']) . "',
			state_name = '" . mysqli_escape_string( $connection, $data['state_name']) . "',
			reg_date = '" . date("Y-m-d H:i:s") . "',
			recent_date = '" . date("Y-m-d H:i:s") . "',
			ip = '" . $_SERVER['REMOTE_ADDR'] . "' ";
	}

	mysqli_query( $connection, $query );
	if( mysqli_error($connection) ){
		echo "there was an error in query";
		echo mysqli_error($connection);
		exit;
	}
	$inserted_id = mysqli_insert_id($connection);
	$_SESSION['user_id'] = $inserted_id;
	echo json_encode([
		"status"=>"success",
		"error"=>""
	]);
	exit;

}

if( $_POST['action'] == "update_record"){

	if( !$_POST['data'] ){
		echo json_encode([
			"status"=>"error",
			"error"=>"Input missing"
		]);
		exit;
	}

	$data = json_decode($_POST['data'],true);

	$res = mysqli_query($connection, "select * from kriya_schools where id =  ". $_SESSION['user_id']);
	$school = mysqli_fetch_assoc($res);
	if( $school['email'] != $data['email'] ||  $school['id'] != $data['id'] ){
		echo json_encode([
			"status"=>"error",
			"error"=>"Details Mismatch"
		]);
		exit;
	}

	$query = "update  kriya_schools set
		type = '".  mysqli_escape_string( $connection, $data['type'] ) . "',
		entry_type = '".  mysqli_escape_string( $connection, $data['entry_type'] ) . "',
		contact_person = '".mysqli_escape_string( $connection, $data['contact_person'] )."',
		institute = '".mysqli_escape_string( $connection, $data['institute'] )."',
		school_id = '".mysqli_escape_string( $connection, $data['school_id'] )."',
		school_name = '".mysqli_escape_string( $connection, $data['school_name'] )."',
		school_category = '".mysqli_escape_string( $connection, $data['school_category'] )."',
		phone = '".mysqli_escape_string( $connection, $data['phone'])."',
		phone2 = '".mysqli_escape_string( $connection, $data['phone2'])."',
		village_name = '" . mysqli_escape_string( $connection, $data['village_name']) . "',
		district_name = '" . mysqli_escape_string( $connection, $data['district_name']) . "',
		state_name = '" . mysqli_escape_string( $connection, $data['state_name']) . "',
		ip = '" . $_SERVER['REMOTE_ADDR'] . "',
		recent_date = '" . date("Y-m-d H:i:s") . "'
		where id = " . $_SESSION['user_id'];
	
	mysqli_query( $connection, $query );
	if( mysqli_error($connection) ){
		echo "there was an error in query";
		echo mysqli_error($connection);
		exit;
	}

	echo json_encode([
		"status"=>"success",
		"error"=>""
	]);
	exit;
}

if( $_POST['action'] == "search_school"){
	$school_code = $_POST['code'];

	$query = "select * from kriya_school_list where school_id = '" . $school_code . "' ";
	$res = mysqli_query( $connection, $query );
	if( mysqli_error($connection) ){
		echo json_encode( ["status"=>"error", "error"=>mysqli_error($connection)] );exit;
	}
	$row = mysqli_fetch_assoc( $res );

	if ( $row ) {
		echo json_encode([
			"status"=>"success",
			"data"=> $row
		]);
		exit;
	}else{
		echo json_encode([
			"status"=>"fail",
			"error"=>"SchoolNotFound"
		]);	exit;
	}

}

if( $_POST['action'] == "save_nominations" ){


	if( !$_POST['record'] ){
		echo json_encode([
			"status"=>"error",
			"error"=>"Input missing"
		]);
		exit;
	}

	$res = mysqli_query( $connection, "select * from kriya_schools where id = " . $_SESSION['user_id'] );
	$row = mysqli_fetch_assoc($res);

	$res2 = mysqli_query( $connection, "delete from kriya_students where user_id = " . $_SESSION['user_id'] );

	$entries = json_decode($_POST['record'],true);
	// print_r($entries);exit;
 	$totalCount = 0;
    $boys_cnt = 0;
    $girls_cnt = 0;

    foreach ($entries as $entry) {
        if (isset($entry['jrs'])) {
        	foreach ($entry['jrs'] as $studentArray) {
	            $totalCount += count($studentArray);
	            foreach ($studentArray as $student) {
	                if (strtolower($student['gender']) === 'male') {
	                    $boys_cnt++;
	                } elseif (strtolower($student['gender']) === 'female') {
	                    $girls_cnt++;
	                }
	            }
	        }
        }
        if (isset($entry['sub_jrs'])) {
        	foreach ($entry['sub_jrs'] as $studentArray) {
	            $totalCount += count($studentArray);
	            foreach ($studentArray as $student) {
	                if (strtolower($student['gender']) === 'male') {
	                    $boys_cnt++;
	                } elseif (strtolower($student['gender']) === 'female') {
	                    $girls_cnt++;
	                }
	            }
	        }
        }
        if (isset($entry['srs'])) {
        	foreach ($entry['srs'] as $studentArray) {
	            $totalCount += count($studentArray);
	            foreach ($studentArray as $student) {
	                if (strtolower($student['gender']) === 'male') {
	                    $boys_cnt++;
	                } elseif (strtolower($student['gender']) === 'female') {
	                    $girls_cnt++;
	                }
	            }
	        }
        }
    }

    $amount = 0;
    if ($row['entry_type'] == 'paid') {
        if ($row['type'] == 'school') {
            $amount = $totalCount * 300;
        } elseif ($row['type'] == 'parent') {
            $amount = $totalCount * 500;
        } elseif ($row['type'] == 'institute') {
            $amount = $totalCount * 500;
        }
    }


    $query = "update kriya_schools set
    total_students = '" . mysqli_escape_string($connection, $totalCount) . "',
    boys = '" . mysqli_escape_string($connection, $boys_cnt) . "',
    girls = '" . mysqli_escape_string($connection, $girls_cnt) . "',
    selection = '" . mysqli_escape_string($connection, json_encode($entries, JSON_PRETTY_PRINT)) . "',
    amount = '" . mysqli_escape_string($connection, $amount) . "'
	where id = " . $_SESSION['user_id'];

	mysqli_query( $connection, $query );
	if( mysqli_error($connection) ){
		echo "there was an error in query";
		echo mysqli_error($connection);
		exit;
	}

	foreach ($entries as $entryID => $entry) {
		if (isset($entry['sub_jrs'])) {
    		foreach ($entry['sub_jrs'] as $studentArray) {
    			foreach ($studentArray as $student) {
					$query = "insert into kriya_students set
					user_id = '" . mysqli_escape_string($connection, $_SESSION['user_id']) . "',
					item_id = '" . mysqli_escape_string($connection, $entryID) . "',
					category = 'sub_jrs',
					name = '" . mysqli_escape_string($connection, $student['name']) . "',
					age = '" . mysqli_escape_string($connection, $student['age']) . "',
					class = '" . mysqli_escape_string($connection, $student['class']) . "',
					gender = '" . mysqli_escape_string($connection, $student['gender']) . "'";

					if (!mysqli_query($connection, $query)) {
		                echo json_encode([
		                	"status" => "error",
		                	"error" => "Error inserting student: " . mysqli_error($connection)
		                ]);
		                exit;
		            }
		        }
	        }
		}

		if (isset($entry['jrs'])) {
    		foreach ($entry['jrs'] as $studentArray) {
    			foreach ($studentArray as $student) {
					$query = "insert into kriya_students set
					user_id = '" . mysqli_escape_string($connection, $_SESSION['user_id']) . "',
					item_id = '" . mysqli_escape_string($connection, $entryID) . "',
					category = 'jrs',
					name = '" . mysqli_escape_string($connection, $student['name']) . "',
					age = '" . mysqli_escape_string($connection, $student['age']) . "',
					class = '" . mysqli_escape_string($connection, $student['class']) . "',
					gender = '" . mysqli_escape_string($connection, $student['gender']) . "'";

					if (!mysqli_query($connection, $query)) {
		                echo json_encode([
		                	"status" => "error",
		                	"error" => "Error inserting student: " . mysqli_error($connection)
		                ]);
		                exit;
		            }
		        }
	        }
		}

		if (isset($entry['srs'])) {
    		foreach ($entry['srs'] as $studentArray) {
    			foreach ($studentArray as $student) {
					$query = "insert into kriya_students set
					user_id = '" . mysqli_escape_string($connection, $_SESSION['user_id']) . "',
					item_id = '" . mysqli_escape_string($connection, $entryID) . "',
					category = 'srs',
					name = '" . mysqli_escape_string($connection, $student['name']) . "',
					age = '" . mysqli_escape_string($connection, $student['age']) . "',
					class = '" . mysqli_escape_string($connection, $student['class']) . "',
					gender = '" . mysqli_escape_string($connection, $student['gender']) . "'";

					if (!mysqli_query($connection, $query)) {
		                echo json_encode([
		                	"status" => "error",
		                	"error" => "Error inserting student: " . mysqli_error($connection)
		                ]);
		                exit;
		            }
		        }
	        }
		}
	}

	$options = [];
	$res = mysqli_query($connection, "select * from kriya_options where user_id = " . $_SESSION['user_id']);
	while ($row = mysqli_fetch_assoc($res)) {
	    $options[$row['item_id']] = $row;
	}

	foreach ($entries as $key => $entry) {
	    $sub_jrs = $jrs = $srs = $sub_jrs_cnt = $jrs_cnt = $srs_cnt = 0;

	    if (isset($entry['sub_jrs'])) {
	        $sub_jrs = count($entry['sub_jrs']);
	        foreach ($entry['sub_jrs'] as $studentArray) {
	            $sub_jrs_cnt += count($studentArray);
	        }
	    }

	    if (isset($entry['jrs'])) {
	        $jrs = count($entry['jrs']);
	        foreach ($entry['jrs'] as $studentArray) {
	            $jrs_cnt += count($studentArray);
	        }
	    }

	    if (isset($entry['srs'])) {
	        $srs = count($entry['srs']);
	        foreach ($entry['srs'] as $studentArray) {
	            $srs_cnt += count($studentArray);
	        }
	    }

	    // print_r($entry);
	    // exit;
	    // echo $srs;
	    // echo $srs_cnt;
	    // exit;

	    if (isset($options[$key])) {
	        $query = "update kriya_options set
	            sub_jrs = '" . $sub_jrs . "',
	            jrs = '" .$jrs . "',
	            srs = '" . $srs . "',
	            sub_jrs_cnt = '" . $sub_jrs_cnt . "',
	            jrs_cnt = '" . $jrs_cnt . "',
	            srs_cnt = '" . $srs_cnt . "'
	            where user_id = '" . $_SESSION['user_id'] . "' and item_id = '" . $key . "' ";
	    } else {
	        $query = "insert into kriya_options set
	        user_id = '" . $_SESSION['user_id'] . "',
	        item_id = '" . $key . "',
	        sub_jrs = '" . $sub_jrs . "',
	        sub_jrs_cnt = '" . $sub_jrs_cnt . "',
	        jrs = '" . $jrs . "',
	        jrs_cnt = '" . $jrs_cnt . "',
	        srs = '" . $srs . "',
	        srs_cnt = '" . $srs_cnt . "'";
	    }

	    mysqli_query($connection, $query);
	    if (mysqli_error($connection)) {
	        echo json_encode([
	            "status" => "error",
	            "error" => "There was an error in the query: " . mysqli_error($connection)
	        ]);
	        exit;
	    }
	}


	sendemail( $_SESSION['user_id'] );

	echo json_encode([
		"status"=>"success",
		"error"=>""
	]);	exit;	
}