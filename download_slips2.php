<?php
session_start();
include('db.php');
include('config.php');
include("actions.php");
//echo "<pre>";
//print_r($_SESSION);
if( $_GET['action'] == 'download_slips' ){

		$counts = array();
		foreach( $config_categories as $key => $value ){
			//$counts[ $key ] = array();
		}

		$sc = array();
		$query = "select id, school_id, school_name, village_name, mandal_name, contact_person, phone from kriya_schools order by id";
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
								$sc[ $school_id ][ "cnt" ]+=$j[$type];
							}
						}
					}
				}
			}
		}

		//echo "<pre>";
		//print_r( $sc );
		//exit;

		$pdf_cnt = 0;
		include('mpdf60/mpdf.php');

		foreach( $sc as $school_id=>$school ){
		if( $school['cnt'] ){

			//echo $html;exit;
			ob_start();

			$slipcnt = 0;

			$headv = "<p style='font-size:16px;' >".str_pad($school_id,3,"0",STR_PAD_LEFT)." - " . $school['school_name'] ." - ". $school['contact_person'] . ", ". $school['phone'] . "</p>";
			$s = ["sub_jrs" =>"Sub Junior", "jrs"=>"Junior", "srs"=>"Senior"];
			foreach( $s as $type=>$type_name ){
				foreach( $school[ $type ] as $item_id=>$slip ){
					if( $slipcnt == 0 ){
						echo $headv;
					}
					echo "<div style='border:1px solid #cdcdcd;border-radius:5px;text-align:center; margin-bottom:10px; font-family:arial; '>
					<div style='float:left; width:70%;' >
					<p style='font-size:16px; font-weight:bold; font-family:arial;'>" . $config_categories[ $item_id ]["english"] . " - " . $type_name . "</p>
					<div>".$school['school_id'] . "</div>
					<div>".$school['school_name'] . "</div>
					<div style='margin-bottom:15px;'>".$school['village_name'] . " - " .$school['mandal_name'] . "</div>
					</div>
					<div style='float:left; width:30%;' >";
					if( $config_categories[ $item_id ]["group"] ){
						echo "<p style='font-size:18px; margin:0px; padding:0px; margin-bottom:10px; margin-top:20px;'>Group of " . $slip['students'] . "</p>";
					}else{
						echo "<p style='font-size:18px; margin:0px; padding:0px; margin-bottom:10px; margin-top:20px;'>Single</p>";
					}
					echo "
					<p style='font-size:40px; margin:0px; padding:0px;' >" . str_pad($slip['series']+1,3,"0",STR_PAD_LEFT) . "</p>
					</div>
					</div>";
					$slipcnt++;
					if( $slipcnt == 6 ){
						$slipcnt=0;
						echo "<p>continues...</p>";
						//echo "<pagebreak/>";
						echo "<pagebreak sheet-size=\"A4\" />";
					}
				}
			}

			$pdf_cnt++;

			$d = ob_get_clean();

			$mpdf=new mPDF('C','A4');
			//['mode'=>'utf-8','format'=>'A4']
			$mpdf->WriteHTML($d);
			$mpdf->autoScriptToLang = true;
			$mpdf->autoLangToFont = true;
			//$mpdf->SetDisplayMode('fullpage');
			$mpdf->Output( 'slips/School_'.$school_id .".pdf", 'F' );
			echo "<div>" .$school_id . " Saved</div>";

			if( $pdf_cnt > 20 ){
				exit;
			}

		}
		}

		//echo "<pre align='left'>";
		//print_r( $sc );
		exit;

}