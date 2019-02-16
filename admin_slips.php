<?php

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
					if( $j[ $type ] ){
						if( $school_id == $j['school_id'] ){
							if( $j[$type . "_cnt"] == 0  ){
								//echo $type;
								//print_r( $j );
								//exit;
							}
							$sc[ $school_id ][ $type ][ $item_id ] = array( "group"=>$j[$type], "students"=>$j[$type . "_cnt"], "series"=>$cnt );
						}
					}
				}
			}
		}
	}

	//echo "<pre>";
	//print_r( $sc );
	//exit;

	foreach( $sc as $school_id=>$school ){
		echo "<div style='border-top:10px solid #f0f0f0; border-bottom:1px solid #cdcdcd; font-size:28px;'><b>School: " . $school['school_name'] . " - " . $school['village_name'] . " - " . $school['mandal_name'] . "</b></div>";
		echo "<div>&nbsp;</div>";
		$s = ["sub_jrs" =>"Sub Junior", "jrs"=>"Junior", "srs"=>"Senior"];
		foreach( $s as $type=>$type_name ){
			foreach( $school[ $type ] as $item_id=>$slip ){
				echo "<div style='border-bottom:1px dashed #cdcdcd; width:500px; padding:10px; margin-bottom:10px; '>
				<p style='font-size:26px; font-weight:bold;'>" . $config_categories[ $item_id ]["name"] . " - " . $type_name . "</p>
				<p align=right>Reg.No: _ _ _ _ _ _ _ _ _ _ _ _ <BR>(Filled by Kriya)</p>
				<p>Student Name: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ </p>
				<p>Class: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ Teacher Mobile: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ </p>
				<p>Reg.No: ". $school_id . ", Code: ".$school['school_id'] . " - <b>".$school['school_name'] . "</b> - ".$school['village_name'] . " - ".$school['mandal_name'] . "</p>";
				if( $config_categories[ $item_id ]["group"] ){
					echo "<p style='font-size:18px;'>Group of " . $slip['students'] . "</p>";
				}else{
					echo "<p style='font-size:18px;'>Single</p>";
				}
				echo "</div>";
			}
		}
		echo "<div style='clear:both;'></div>";
	}

	//echo "<pre align='left'>";
	//print_r( $sc );
exit;

?>	