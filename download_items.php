<?php

$_SERVER['HTTP_HOST'] = "register.kriyaonline.org";

//aerror_reporting(E_ALL & ~E_NOTICE);
include('db.php');
include('config.php');
include("actions.php");
//include('mpdf60/mpdf.php');
require("vendor/autoload.php");

$day1 = "25 November 2022";
$day2 = "26 November 2022";

$s = ["sub_jrs" =>"Sub Junior", "jrs"=>"Junior", "srs"=>"Senior"];

//print_r( $config_categories );exit;

$query = "select * from kriya_schools order by id ";
$res = mysqli_query( $connection, $query );
$schools = [];
while( $row  = mysqli_fetch_assoc( $res ) ){
	$schools[ $row['id'] ] = $row;
}

foreach( $config_categories as $i=>$vd ){

		$pdf_cnt = 0;
		$query = "select * from kriya_options where item_id = '" .$vd['sno'] . "' order by school_id ";
		$res = mysqli_query( $connection, $query );

		foreach( $s as $cat=>$catd ){

			mysqli_data_seek($res,0);
			$total = 0;
			$school_ids = [];
			while( $row = mysqli_fetch_assoc($res) ){if( $row[ $cat ] ){
				$school_ids[ $row['school_id'] ] = 1;
				$total+= $row[ $cat . "_cnt" ];
			}}
			
			mysqli_data_seek($res,0);
			ksort($school_ids);
			if( $total ){

			ob_start();
			
			echo "<style>
body, td,tr,th, p, div { font-size: 12px; font-family: Arial;}
.sdfssfs{border-collapse:collapse;}
.sdfssfs td { border:0.5px solid #aaa; padding:5px; margin:0px;}
</style>";

		?>
		<center>
			<div style="font-size:18px;text-align:left; padding:10px;"><?=$vd['sno'] . " - " .htmlspecialchars($vd['name']) . " - " . $catd ?></div>
			<table class="sdfssfs" width='100%' cellspacing="0" cellpadding="5" align='center'>
				<tr>
					<td>Sno</td>
					<td>ID</td>
					<td>Code</td>
					<td>School</td>
					<td>Area</td>
					<td>Person</td>
					<td>Contact</td>
					<td>Count</td>
				</tr>
				<?php $sno=0; while( $row = mysqli_fetch_assoc($res) ){if( $row[ $cat ] ){  $sno++; ?>
				<tr>
					<td><?=$sno ?></td>
					<td><?=$schools[ $row['school_id'] ]['id']?></td>
					<td><?=$schools[ $row['school_id'] ]['school_id']?></td>
					<td><?=htmlspecialchars($schools[ $row['school_id'] ]['school_name']) ?></td>
					<td><?=htmlspecialchars($schools[ $row['school_id'] ]['village_name'].",".$schools[ $row['school_id'] ]['district_name']) ?></td>
					<td><?=htmlspecialchars($schools[ $row['school_id'] ]['contact_person']) ?></td>
					<td><?=htmlspecialchars($schools[ $row['school_id'] ]['phone']). ",".htmlspecialchars($schools[ $row['school_id'] ]['phone2']) ?></td>
					<td align="right"><?=$row[ $cat . "_cnt" ] ?></td>
				</tr>
				<?php }} ?>
			</table>
		</center>
		<?php
			$pdf_cnt++;
			$d = ob_get_clean();
			if( 1==1 ){
				$mpdf = new \Mpdf\Mpdf(['mode'=>'C', 'format'=> 'A4-L']);
				$mpdf->SetHTMLHeader("<p>" .  $vd['sno'] . ': ' . htmlspecialchars($vd['name']) . '_' . $catd . "</p>", 'O' );
				$mpdf->SetHTMLHeader("<p>" .  $vd['sno'] . ': ' . htmlspecialchars($vd['name']) . '_' . $catd . "</p>", 'E' );
				$mpdf->WriteHTML($d);
				$mpdf->autoLangToFont = true;
				$mpdf->Output( 'slips/items2023/'. $vd['sno'] . '_' . preg_replace("/\W/", "-", $vd['name']) . '_' . str_replace(" ", "-", $catd) . ".pdf" , "F" );
				unset($mpdf);
				echo $vd['sno'] . " : " . $catd . "\n";
				//exit;
			}else{
				echo $d;
			}
		}
		}
}
