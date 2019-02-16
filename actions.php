<?php
	if( $_GET['action'] == "logout"){
		session_destroy();
		unset( $_SESSION['loggedin'] );
		header("Location: /?");
		exit;
	}
	if( $_POST["action"] == "check_school_code" ){
	
		//echo "<pre>";print_r($_POST);exit;
		$query = "select * from kriya_school_list where school_id='" . mysqli_escape_string( $connection, $_POST['school_code'] ) . "'";
		$res = mysqli_query( $connection, $query );
		if(mysqli_error($connection)){
			echo json_encode(array("status" => "failed","reason" => mysqli_error($connection)));
		}
		$row = mysqli_fetch_assoc( $res );
		if($row["school_id"] != ""){
			$_SESSION['school_id'] = $row['school_id'];
			header("Location:/".$row["school_id"]);	
		}else{
			header("Location:/?event=failed");
		}
		exit;	
	}

if( $_GET['action'] == "download_report"){

	include_once("PHP_XLSXWriter/xlsxwriter.class.php");

	$filename = "kriya_schools.xlsx";
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->setAuthor('Satish Kalepu');
	//$writer->writeSheet( $rows, "utf8");

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
	for($i=1;$i<=24;$i++){
		$header[ "i-" . $i . "" ] = "integer";
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
					utf8_encode($school['school_id']),
					utf8_encode($school['school_name']),
					utf8_encode($school['city']),
					utf8_encode($school['contact_person']),
					$school['phone'] . utf8_encode(($school['phone2']?", ".$school['phone2']:"")),
					$school['email'],
					'.'
				);
				foreach( $options as $item_id=>$ii ){
					$row[] = ($school["items"][ $item_id ][ $item_type ]?$school["items"][ $item_id ][ $item_type ]:" ");
				}
				$rows[] = $row;
			}
		}
		$col_options = array('font-style'=>'bold','border'=>'left,right,top,bottom','widths'=>array(10,30,10,10,10,10,1,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5));
		$row_options = array('border'=>'left,right,top,bottom');
		$writer->writeSheetHeader($item_type_name, $header, $col_options );
		foreach($rows as $row){
			$writer->writeSheetRow($item_type_name, $row, $row_options );
		}
	}
	$writer->writeToStdOut();
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
	

	include_once("PHP_XLSXWriter/xlsxwriter.class.php");

	$header = array(
	  'Reg No',
	  'School Code',
	  'School Details',
	  'City',
	  'Contact Person',
	  'Phone',
	  'Email',
	  'Students',
	  'Teachers',
	  'Accommodation'
	);

	$header = array(
	  'Reg No'=>'string',
	  'School'=>'string',
	  'School Details'=>'string',
	  'City'=>'string',
	  'Contact Person'=>'string',
	  'Phone'=>'string',
	  'Email'=>'string',
	  'Students'=>'integer',
	  'Teachers'=>'integer',
	  'Accommodation'=>'string'
	);

	$rows = array();
	//$rows[] = $header;
	while($row = mysqli_fetch_assoc($res))  
	{
		$rows[] = array(
			str_pad($row['id'],3,"0",STR_PAD_LEFT),
			utf8_encode($row['school_id']),
			utf8_encode($row['school_name']),
			utf8_encode($row['village_name']. " - ".$row["mandal_name"]." - ".$row['district_name']),
			utf8_encode($row['contact_person'] ),
			$row['phone'] . ($row['phone2']?" - ".$row['phone2']:""),
			utf8_encode( $row['email'] ),
			$row['total_students'],
			$row['total_teachers'],
			($row['accommodation']?"Yes":" - ")
		);
	}
	if( 1==2 ){
		$filename = "kriya_schools.csv";
		header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
		header("Content-Type: application/vnd.ms-excel");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');

		foreach( $rows as $row){
			echo implode(",\t", $row) . "\r\n";
		}
		exit;
	}

	$filename = "kriya_schools.xlsx";
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->setAuthor('Satish Kalepu');
	//$writer->writeSheet( $rows, "utf8");
	$writer->writeSheetHeader('Sheet1', $header, array('font-style'=>'bold'));
	foreach($rows as $row){
		$writer->writeSheetRow('Sheet1', $row);
	}
		
	$writer->writeToStdOut();
	//$writer->writeToFile('example.xlsx');
	//echo $writer->writeToString();
	exit;
}

if( $_POST['action'] == "downloadexcel" ){

}

if( $_POST['action'] == "admin_login" ){
	if( strtolower($_POST['code']) == strtolower($_SESSION['captcha']['code']) && $_POST['password'] == "adminaxbycz" ){
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

if( $_POST['action'] == "login"){
	$_POST['email'] = strtolower(trim($_POST['email']));
	$_POST['phone'] = trim($_POST['phone']);
	$_SESSION['post'] = $_POST;
	if( !$_SESSION['school_id'] ){
		session_regenerate_id();
		session_destroy();
		header("Location: /?retry");
		exit;
	}
	if( 1==2 ){
		echo "<pre>";
		print_r( $_POST );
		print_r( $_SESSION );
		echo "</pre>";
		exit;
	}
	
	if( !trim($_POST['code']) ){
		header("Location: ?event=". urlencode("Security code was wrong!") );
		exit;
	}
	
	if( strtolower($_POST['code']) == strtolower($_SESSION['captcha']['code']) ){
		if( $_POST['email'] && $_POST['phone'] ){
			if( !preg_match("/^[a-z0-9\.\_\-]{3,25}\@[a-z0-9\.\_\-]{3,25}\.[\.a-z]{2,6}$/i", $_POST['email'] ) || !preg_match("/^[0-9\.\+\ \-]{10,15}$/", $_POST['phone'] )  ){
				header("Location: ?event=". urlencode("Email or Phone is not proper!") );
				exit;
			}else if( !is_numeric( $_POST['school_code'] ) ){
				header("Location: ?event=". urlencode("Incorrect Request!") );
				exit;
			}else{
				$res = mysqli_query( $connection, "select * from kriya_schools where school_id = '". $_SESSION['school_id'] . "' " );
				$row = mysqli_fetch_assoc( $res );
				//echo "<pre>";print_r($row);exit;
				if( $row ){
					if( $row['email'] == $_POST['email'] || $row['phone'] == $_POST['phone'] ){
						unset($_SESSION['post']);
						setcookie( "email", $_POST['email'], time()+(186400) );
						setcookie( "phone", $_POST['phone'], time()+(186400) );
						$_SESSION['loggedin'] = "y";
						$_SESSION['user_id'] = $row['id'];
						header("Location: /?event=welcome" );
						exit;						
					}else{
						header("Location: ?event=1");
						exit;
					}
				}else{ 
					$res = mysqli_query( $connection, "select * from kriya_schools where 
					email = '" . mysqli_escape_string( $connection, $_POST['email'] ) . "' or 
					phone = '" .  mysqli_escape_string( $connection, $_POST['phone'] ) . "' " );
					$row = mysqli_fetch_assoc( $res );
					//print_r($row);exit;
					if( $row ){
						header("Location: ?event=2");
						exit;
					}else{
						$res1 = mysqli_query( $connection, "select * from kriya_school_list where school_id = '" .$_SESSION['school_id'] . "'");
					        $row1 = mysqli_fetch_assoc( $res1 );
					        if( !$row1 ){
					        	session_regenerate_id();
					        	session_destroy();
					        	header("Location: /?retry");
					        	exit;
						}
						$query = "insert into kriya_schools set 
						email = '" . mysqli_escape_string( $connection, $_POST['email'] ) . "',
						phone = '" . mysqli_escape_string( $connection, $_POST['phone'] ) . "',
						school_id = '".mysqli_escape_string( $connection, $_SESSION['school_id'])."',
						school_name = '".mysqli_escape_string( $connection, $row1['school_name'])."',
						village_name = '".mysqli_escape_string( $connection, $row1['mandal_name'])."',
						mandal_name = '".mysqli_escape_string( $connection, $row1['village_name'])."',
						district_name = '".mysqli_escape_string( $connection, $row1['district_name'])."',
						reg_date = '" . date("Y-m-d H:i:s") . "',
						ip = '" . $_SERVER['REMOTE_ADDR'] . "' ";
						//echo $query;exit;
						mysqli_query( $connection, $query );
						if( mysqli_error( $connection ) ){
						//	echo $query;exit;
							echo "<p>DB Error<BR>Please try after sometime!</p>";
							exit;
						}
						$id = mysqli_insert_id( $connection );
						unset($_SESSION['post']);
						setcookie( "email", $_POST['email'], time()+(186400) );
						setcookie( "phone", $_POST['phone'], time()+(186400) );
						$_SESSION['loggedin'] = "y";
						$_SESSION['user_id'] = $id;
						header("Location: /?event=welcome" );
						exit;
					}
				}
			}
		}else{
			header("Location: ?event=". urlencode("Email and Phone required!") );
			exit;
		}
	}else{
		header("Location: ?event=". urlencode("Security code was wrong!") );
		exit;
	}
}

if( $_POST['action'] == 'register' && $_SESSION['loggedin'] == "y" ){
	//echo "<pre>";print_r($_POST);exit;

	$_SESSION['post'] = $_POST;
	$query = "update  kriya_schools set
		contact_person = '".mysqli_escape_string( $connection, $_POST['teacher_name'] )."',
		phone2 = '".mysqli_escape_string( $connection, $_POST['phone2'])."',
		total_students = '" . mysqli_escape_string( $connection, $_POST['total_students']) . "',
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
	header("Location: /?show=thanks&event=updated");
	exit;
}

function sendemail( $vid ){

	global $connection;

	$res = mysqli_query( $connection, "select * from kriya_schools where id = " . $vid );
	$row = mysqli_fetch_assoc( $res );
	if( $row ){

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
		$message .= "<p>You can pick/change nominations of your choice until Feb 11th 2019.</p>";
		$message .= "<p>&nbsp;</p>";
		$message .= "<p>Best Regards,</p>";
		$message .= "<p>&nbsp;</p>";
		$message .= "<p>Kriya Team</p>";
		$message .= "<p>7288822599, 8332993993</p>";

		$subject = "Kriya Participation Confirmation";

		require("smtp.php");

		$mail = new SMTPMailer();
		$mail->addTo( $row['email'] );
		$mail->addBcc( "ksatish21@gmail.com" );
		$mail->addReplyTo( "kriyaonline@gmail.com" );
		$mail->Subject( $subject );
		$mail->Body( $message );
		if ( !$mail->Send() ){
			$fp = fopen("smtp_error.log", "a" );
			fwrite( $fp, implode("\r\n", $mail->log) );
			fclose( $fp );
			error_log( "Error sending mail to " . $row['email'] );
		}

		//mail( $row['email'], $subject, $message, "From: Kriya Children Festival <kriyaonline@gmail.com>\r\nBCC: ksatish21@gmail.com \r\nReply-To:kriyaonline@gmail.com");
		//$vurl = "http://kriyaonline.org/register/sendmail.php?to=".urlencode($row['email']) ."&subject=".urlencode($subject)."&message=".urlencode($message);
		//$st = file_get_contents($vurl);
		//header("Mail: " . $st);
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

?>