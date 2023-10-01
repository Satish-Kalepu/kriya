<?php

$_SERVER['HTTP_HOST'] = "register.kriyaonline.org";

error_reporting("E_ALL & ~E_NOTICE");
include('db.php');
include('config.php');
include("actions.php");
include('mpdf60/mpdf.php');

$day1 = "19 November 2022";
$day2 = "20 November 2022";

$s = ["sub_jrs" =>"Sub Junior", "jrs"=>"Junior", "srs"=>"Senior"];

/*
foreach( $config_categories as $i=>$j ){

	//$s = utf8_decode($j['name']);
	
	
	$dos = mb_convert_encoding($j['name'], "CP850", mb_detect_encoding($j['name'], "UTF-8, CP850, ISO-8859-15", true));
	echo "<div>". $dos . "</div>"; 
	$s = $j['name'];
	echo "<div>". $s . "</div>";
	echo  mb_detect_encoding($s, mb_detect_order(), true);
	for($i=0;$i<strlen($s);$i++){
		$k = $s[$i];
		$u = "&#x".dechex(ord($k)).";";
		echo "<div>" . $k . ": " . ord($k) . ": " . dechex(ord($k)) . " : " . $u . "</div>";
		//echo 
	}
	
	exit;

}

exit;
*/

//print_r( $argv );exit;


if( 1 == 51 ){
	$query = "select * from kriya_schools 
	where total_students > 0 order by id";
	$res2 = mysqli_query( $connection, $query );
	//echo mysqli_num_rows($res2);exit;
	while( $school = mysqli_fetch_assoc($res2) ){
		//echo "echo \"php download_slip.php ".$school['id'] . "\"\r\n";
		echo "php download_slip.php ".$school['id'] . " one \n";
		echo "php download_slip.php ".$school['id'] . " two \n";
	}                                                          
	exit;
}

if( !$argv[1] ){echo "arg one school id";exit;}
if( !$argv[2] ){echo "arg two should be one or two";exit;}
$day = $argv[2];
if( $day != "one" && $day != "two" ){
	echo "arg two should be one or two";
	exit;
}


{

		$pdf_cnt = 0;

		$cond = " 1= 1";
		if( $argv[1] ){
			$cond .= " and id = " . $argv[1];
		}

		$query = "select * from kriya_schools 
		where " . $cond . " and total_students > 0 order by id";
		//echo $query . "\n";
		$res2 = mysqli_query( $connection, $query );
		$school = mysqli_fetch_assoc($res2);
		if( $school ){
		
		
			if( file_exists('slips/Day_'. $day . '_School_'.$school['id'] .".pdf") ){
				echo $school['id'] ." already exists\n";
				exit;
			}
		
			//print_r( $school );exit;
			$options2 = array();

                	$options = array();
			$query = "select * from kriya_options where school_id = " . $school['id'] . " order by item_id";
			$res3 = mysqli_query( $connection, $query );
			while( $row = mysqli_fetch_assoc($res3) ){
				if( $day == "one" && $row['item_id'] <= 118 ){
					if( $row['item_id'] == 101 ){
						unset($row['sub_jrs_cnt']);unset($row['sub_jrs']);
						unset($row['jrs_cnt']);unset($row['jrs']);
					}
					$options2[ $row['item_id'] ] = $row;
					if( $row['sub_jrs_cnt'] ){
						for($i=0;$i<$row['sub_jrs_cnt'];$i++){
							$row['type'] = "sub_jrs";
							$options[] = $row;
						}
					}
					if( $row['jrs_cnt'] ){
						for($i=0;$i<$row['jrs_cnt'];$i++){
							$row['type'] = "jrs";
							$options[] = $row;
						}
					}
					if( $row['srs_cnt'] ){
						for($i=0;$i<$row['srs_cnt'];$i++){
							$row['type'] = "srs";
							$options[] = $row;
						}
					}
				}
				if( $day == "two" && ( $row['item_id'] == 101 || $row['item_id'] >= 118 ) ){
					if( $row['item_id'] == 101 ){
						unset($row['srs_cnt']);unset($row['srs']);
					}
					$options2[ $row['item_id'] ] = $row;
					if( $row['sub_jrs_cnt'] ){
						for($i=0;$i<$row['sub_jrs_cnt'];$i++){
							$row['type'] = "sub_jrs";
							$options[] = $row;
						}
					}
					if( $row['jrs_cnt'] ){
						for($i=0;$i<$row['jrs_cnt'];$i++){
							$row['type'] = "jrs";
							$options[] = $row;
						}
					}
					if( $row['srs_cnt'] ){
						for($i=0;$i<$row['srs_cnt'];$i++){
							$row['type'] = "srs";
							$options[] = $row;
						}
					}
				}
			}
			
			if( sizeof($options) && sizeof($options2) ){
			
			//echo "<pre>";
			//print_r( $options2 );exit;
			
			//echo "<pre>";
			//print_r( $config_categories );exit;

			//echo $html;exit;
			ob_start();
			
			echo "<style>
body, td,tr,th, p, div { font-size: 12px; font-family: Arial;}
.sdfssfs{border-collapse:collapse;}
.sdfssfs td { border:0.5px solid #aaa; padding:5px; margin:0px;}
</style>";

	
		?>
		
		<center>
			<div style="font-size:14px;text-align:center;">Day: <?=$day=="one"?$day1:$day2 ?></div>
			<table class="sdfssfs" width='100%' cellspacing="0" cellpadding="5" align='center'>
					<tr>
						<td>Reg No</td>
						<td>School Code</td>
						<td>School Name</td>
					</tr>
					<tr>
						<td><?=$school['id']?></td>
						<td><?=$school['school_id']?></td>
						<td><?=$school['school_name'] ?> - <?=$school['village_name'].",".$school['mandal_name'].",".$school['district_name'] ?></td>
					</tr>
			</table>
			<table class="sdfssfs" width='100%' cellspacing="0" cellpadding="5" align='center'>
					<tr>
						<td>Contact Person</td>
						<td>Email</td>
						<td>Phone</td>
					</tr>
					<tr>
						<td><?=$school['contact_person']?$school['contact_person']:"-"?></td>
						<td><?=$school['email'] ?></td>
						<td><?=$school['phone'] ?> <?=$school['phone2']?"<BR>".$school['phone2']:"" ?></td>
					</tr>
			</table>
			<table class="sdfssfs" width='100%' cellspacing="0" cellpadding="5" align='center'>
					<tr>
						<td>Students</td>
						<td>Teachers</td>
						<td>Accommodation</td>
						<td>Registered Date: </td>
						
					</tr>
					<tr>
						<td><?=$school['total_students'] ?></td>
						<td><?=$school['total_teachers']?></td>
						<td><?=($school['accommodation']?"Yes":"No")?></td>
						<td><?=($school['reg_date']!="0000-00-00 00:00:00"?date("d M, Y", strtotime($school['reg_date'])):"")?></td>
					</tr>
			</table>
		</center>
		<div style='height:40px;'></div>
		<center>
				<table class="sdfssfs" border=0 style="border-collapse:collapse;" width="100%"  >
					<thead class='bbc'>
					<tr valign='middle' >
						<td rowspan=2 class="col_"  align='center' width="40" >Item No</td>
						<td rowspan=2 class='hcol3' align='left'  >Competition</td>
						<td colspan=2 align='center'>Sub Juniors</td>
						<td colspan=2 align='center'>Juniors</td>
						<td colspan=2 align='center'>Seniors</td>
					</tr>
					<tr valign='middle' >
						<td align='center'>Groups</td>
						<td align='center'>Students</td>
						<td align='center'>Groups</td>
						<td align='center'>Students</td>
						<td align='center'>Groups</td>
						<td align='center'>Students</td>
					</tr>
					</thead>
					<tbody>
			<?php	
				foreach( $config_categories as $key => $value ){
					if( $day == "one" ){
						if( $key > 118 ){continue;}
					}
					if( $day == "two" ){
						if( $key != 101 && $key < 118 ){continue;}
					}
					$key = (int)$key;
					if( 1==2 ){
					 ?>
					<tr>
					<td colspan=5>
					<?php echo "<pre>"; print_r( $value );print_r( $options2[ $key ] ); echo "</pre>";	echo $options2[ $key ]['sub_jrs_cnt']; ?>
					</td></tr>
					<?php } ?>
					
					<?php
					if( $options2[ $key ]['sub_jrs_cnt'] || $options2[ $key ]['jrs_cnt'] || $options2[ $key ]['srs_cnt'] ){ 
					?>
					
					<tr>
					<td class="col_" align='right'><?=$value['sno']?></td>
					<td class="col2"  style="font-size:16px; " ><?=$value['name'] ?></td>
				<?php	if( $value["enabled"][0] ){ ?>
					<td class="col3" align='center'><?=$value['group']?($options2[ $key ]['sub_jrs']?$options2[ $key ]['sub_jrs']:""):"" ?></td>
					<td class="col3" align='center'><?=($options2[ $key ]['sub_jrs_cnt']?$options2[ $key ]['sub_jrs_cnt']:"") ?></td>
					<?php
					}else{
						echo "<td align=center>--</td>";
						echo "<td align=center>--</td>";
					} ?>

				<?php	if( $value["enabled"][1] ){ ?>
					<td class="col3" align='center'><?=$value['group']?($options2[ $key ]['jrs']?$options2[ $key ]['jrs']:""):"" ?></td>
					<td class="col3" align='center'><?=($options2[ $key ]['jrs_cnt']?$options2[ $key ]['jrs_cnt']:"") ?></td>
				<?php
					}else{
						echo "<td align=center>--</td>";
						echo "<td align=center>--</td>";
					} ?>

				<?php	if( $value["enabled"][2] ){ ?>
					<td class="col3" align='center'><?=$value['group']?($options2[ $key ]['srs']?$options2[ $key ]['srs']:""):"" ?></td>
					<td class="col3" align='center'><?=($options2[ $key ]['srs_cnt']?$options2[ $key ]['srs_cnt']:"") ?></td>
					<?php
					}else{
						echo "<td align=center>--</td>";
						echo "<td align=center>--</td>";
					} ?>
				</tr>
			<?php	}
			 }
			 ?>
					</tbody>
				</table>
			</div>
		</center>	
	
		<?php

		//echo ob_get_clean();exit;

		echo "<pagebreak sheet-size=\"A4\" />";

			$slipcnt = 0;

			$headv = "<p style='font-size:16px; border-bottom:1px solid #cdcdcd;' >".str_pad($school['id'],3,"0",STR_PAD_LEFT)." - " . $school['school_name'] ." - ". ($school['mandal_name']?$school['mandal_name']:$school['village_name']) ." - ". $school['contact_person'] . ", ". $school['phone'] . "</p>";
				foreach( $options as $i=>$j ){

					if( $slipcnt == 0 ){
					//	echo $headv;
					}
					echo "<div style='border:0px solid #f0f0f0;border-radius:5px;text-align:center; font-family:arial; padding:10px; text-align:left; '>
					
					<div style=\"float:right; width:150px; height:50px; padding:10px; padding-bottom:0px; border:1px solid black; font-size:14px;\" >
					<p>Reg.No:_ _ _ _ _ _ _ _ _  <BR><span style='font-size:12px;'>(Filled by Kriya)</span></p>
					</div>

					<div>Day: ".($day=="one"?$day1:$day2)."</div>
					<div><span style=\"font-size:24px; \">" . $config_categories[ $j['item_id'] ]['name'] . "</span>&nbsp;&nbsp;-&nbsp;&nbsp;<span style='font-size:18px; font-family:arial'>" . $s[ $j['type'] ] . "</span></div>
					<div style=\"clear:both;\" ></div>
					<p style='font-size:14px; margin-top:20px;' >Student Name: _ _ _ _ _ _ _ _ _ __ _ _ _ __ _ _ _ __ _ _ _ __ _ _ _ __ _ _ _ __ _ _ _ __ _ _ _ __ _ _ _ _</p>
					<p style='font-size:14px;'>Class: _ _ _ __ __ _ _ _ __ _ _ _ _. Teacher Mobile: _ _ _ _ _  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</p>
					<table width='100%'><tr><td  style='font-size:14px;' >
					". str_pad( $school['id'],3,"0",STR_PAD_LEFT) . " - " . substr( $school['school_name'] . " - ".$school['village_name'] . " - " .$school['mandal_name'], 0, 75 ) . "</td><td align=right>
					".($i+1)."</td></tr></table>
					</div>
					";
					if( $slipcnt!=3 ){
					echo "<div style=' margin-bottom:30px;border-bottom:1px dashed black;' >&nbsp;</div>";
					}
					$slipcnt++;
					
					if( $slipcnt == 4	&& $i!=39 ){
						$slipcnt=0;
						//echo "<p>continues...</p>";
						//echo "<pagebreak/>";
						echo "<pagebreak sheet-size=\"A4\" />";
					}
				}

			$pdf_cnt++;
			
			$d = ob_get_clean();
			if( 1==1 ){

				$mpdf=new mPDF('C','A4');
				//['mode'=>'utf-8','format'=>'A4']
				$mpdf->WriteHTML($d);
				$mpdf->autoLangToFont = true;
				//$mpdf->autoScriptToLang = true;
				//$mpdf->autoLangToFont = true;
				//$mpdf->baseScript = 1;
				//$mpdf->autoVietnamese = true;
				//$mpdf->autoArabic = true;
				//$mpdf->SetDisplayMode('fullpage');

				$mpdf->Output( 'slips/Day_'. $day . '_School_'.$school['id'] .".pdf", "F" );
				//$mpdf->Output();
				echo "" .$school['id'] . " Saved\n";
				exit;			
				unset($mpdf);
			
			}else{
			
				echo $d;
			
			}

			if( $pdf_cnt > 50 ){
				//exit;
			}
		}
		}
		//echo "<pre align='left'>";
		//print_r( $sc );
		exit;
}