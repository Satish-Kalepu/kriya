<?php
	session_start();
	include('../config_global.php');
	include('db.php');
	include('config.php');
	include('config_telugu_names.php');
	include("actions.php");	
	if( $_GET['action'] == "logout" ){
		session_destroy();
		header("Location: admin.php?event=logout");
		exit;
	}
	
?>
<html>
<head>
<title>Kriya Registration Form</title>
<style>
body { margin:0px; padding: 0px; }
* {
	font-size:11px;
	font-family:arial;
}
.menu a{ margin-left:20px; margin-right:20px; display:inline-block; padding:10px 5px; background-color:#f0f0f0; text-decoration: none; cursor: pointer; }
.menu{  margin-bottom: 20px; text-align: center; }
thead td{ font-weight: bold; }
</style>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
</head>
<body>
<div style="margin-bottom:20px; padding:10px; font-size:20px; border-bottom: 2px solid #ff790e; color: black; background-color: #ffef96; " >
Kriya Admin
</div>
<?php if(  $_SESSION['admin_login'] == 'yes' ){ ?>
<div style='position:absolute; top:10px; right:20px;' ><a style="font-size:18px;" href="?action=logout" >Logout</a></div>
<?php } ?>

<?php
//print_r( $_SESSION );
if(  $_SESSION['admin_login'] == 'yes' ){

	echo "<div class='menu' >";
	echo "<a href='?r=".time()."' >Schools</a>";
	echo "<a href='?view=report1&r=".time()."' >Summary</a>";
	echo "<a href='?view=download&r=".time()."' >Report</a>";
	//echo "<a href='?view=slips&r=".time()."' >Slips</a>";
	echo "</div>";

	if( $_GET['view'] == "" ){
		$stu_query = "select sum(total_students) as stu, sum(total_teachers) as tea from kriya_schools";
		$res = mysqli_query($connection,$stu_query);
		$stu_row = mysqli_fetch_assoc( $res );
		$condition = "";
		$condition = " where 1=1 ";
		if( $_GET['keyword'] ){
			$condition .= " and ( 
				school_id like '%" . $_GET['keyword'] . "%' 
				or  school_name like '%" . $_GET['keyword'] . "%' 
				or  village_name like '%" . $_GET['keyword'] . "%' 
				or  mandal_name like '%" . $_GET['keyword'] . "%' 
				or  district_name like '%" . $_GET['keyword'] . "%' 
				or  contact_person like '%".$_GET['keyword']."%'
				or  phone like '%" . $_GET['keyword'] . "%' 
				or  phone2 like '%".$_GET['keyword']."%'
				or  email like '%".$_GET['keyword']."%'
				
			) ";
		}
		if( $_GET['show'] == "submitted" ){
			$condition .= " and total_students > 0 ";
		}
		$query_count = 'select count(*) as cnt from kriya_schools ' . $condition;
		$res_count = mysqli_query( $connection, $query_count );
		$row_count = mysqli_fetch_assoc( $res_count );
		$total_records = $row_count['cnt'];
		$perpage = 50;
		$total_pages = ceil($total_records/$perpage);
		$current_page = $_GET['page']?$_GET['page']:1;
		$start = $perpage*($current_page-1);              
		$query_display = "select * from kriya_schools " . $condition ." order by id limit ".$start.",".$perpage;
		$res_display = mysqli_query( $connection, $query_display);
		mysqli_error($connection);
		$records = array();
		while($row = mysqli_fetch_assoc( $res_display) ){
			$records[ $row["id"] ] = $row;
		}
		$t1 = $start+$perpage; ?>

		<center>
		<div style='width:1300px; text-align:left;' align='left' >
			<div align='right' ><a href='?action=download_schools' >Download Excel</a></div>
			<div style='font-size:14px; line-height:30px;'>Total Students:<?=$stu_row['stu']?>, Total Teachers: <?= $stu_row['tea']?></div>
			<div align="center">
			<form method="get">
				<input type="text" name="keyword" id="keyword" placeholder="Search Keyword">
				<button type="submit">Search</button>
				</form>
			</div>	
			<div style="padding:5px;" align=center>
				<div style="float:left;padding:5px">Displaying: <?=$start+1 ?> to <?=$t1 > $total_records ?$total_records : $t1 ?> of <?=$total_records ?></div>
				<div style="float:right;padding:5px">
					<a href="?keyword=<?=urlencode($_GET["keyword"]) ?>&orderby=<?=$_GET['orderby'] ?>&page=1&show=<?=$_GET['show'] ?>" >First</a> |
				<?php	if( $current_page > 1 ){ ?>
						<a href="?keyword=<?=urlencode($_GET["keyword"]) ?>&orderby=<?=$_GET['orderby'] ?>&page=<?=($current_page-1) ?>&show=<?=$_GET['show'] ?>" >Previous</a> |
				<?php	}
				if( $current_page < $total_pages ){ ?>
					<a href="?keyword=<?=urlencode($_GET["keyword"]) ?>&orderby=<?=$_GET['orderby'] ?>&page=<?=($current_page+1) ?>&show=<?=$_GET['show'] ?>" >Next</a> |
				<?php 	} ?>
				<a href="?keyword=<?=urlencode($_GET["keyword"]) ?>&orderby=<?=$_GET['orderby'] ?>&page=<?=$total_pages ?>&show=<?=$_GET['show'] ?>" >Last</a>
				</div> 
		<?php if( $_GET['show'] == "submitted" ){ ?>
		<a href="?show=all">VIEW ALL</a>
		<?php }else{ ?>
		<a href="?show=submitted">VIEW SUBMITTED</a>
		<?php } ?>
				
			</div>    
			<table class='ddd' border='1' style='border-collapse:collapse;'  width='100%' cellpadding='5' cellspacing='1' >
			<thead>
			<tr valign='middle' >
			<td>Reg No</td>
			<td>School Id</td>
			<td>School Name</td>
			<td>Village</td>
			<td>District</td>
			<td>Contact</td>
			<td>Students</td>
			<td>Teachers</td>
			<td>Acm</td>
			<td>View</td>
			<td>Delete</td>
			</tr>
			</thead><tbody>
		<?php	foreach($records as $key =>$row){?>
				<tr>
				<td align='right'><?=$row['id']?></td>
				<td align='right'><?=$row['school_id']?></td>
				<td><?=$row['school_name']?></td>
				<td><?=$row['village_name']?></td>
				<td><?=$row['district_name']?></td>
				<td>
					<?=$row['contact_person']."<BR>". $row['phone'] . ($row['phone2']?",".$row['phone2']:"") .  ($row['email']?"<BR>".$row['email']:"")?>
				</td>
				<td align='right'><?=$row['total_students']?$row['total_students']:"-"?></td>
				<td align='right'><?=($row['total_teachers']?$row['total_teachers']:"-")?></td>
				<td><?=$row['accommodation']?"Yes":"-"?></td>
				<td>
					<a href='?view=school_details&school_id=<?=$row['id'] ?>'>VIEW</a>
				</td>
				<td>
					<a href='?view=cancel_registration&school_id=<?=$row['id'] ?>'>CANCEL REGISTRATION</a>
				</td>
				</tr>
		<?php	} ?>
			</tbody></table>
			</div>
		</div>
		</center>

<?php }else if( $_GET['view'] == "cancel_registration" ){

		$query = "select * from kriya_schools where id = '".$_GET['school_id']."' ";
		$res = mysqli_query($connection,$query);
		$school = mysqli_fetch_assoc($res);  //print_r($school);
		//print_r($options);
		$school_id = $_GET['school_id'];
		?>
		<div style='height:10px;'></div>
		<div align='right' style='padding:10px;'>
		<a style='margin:20px; padding:10px; cursor:pointer;width:200px !important;font-weight:bold;text-decoration:none;color:black;background-color:#ffef96;border-radius:5px;' target="_blank" href='download_pdf.php?event=download_pdf&record_id=<?= $school_id ?>'>Download Pdf</a></div>
		<center>
		<p>
		<form method="post" >
			<input type="submit" name="btn" value="CONFIRM TO CANCEL REGISTRATION">
			<input type="hidden" name="action" value="cancel_school_registration">
			<input type="hidden" name="school_id1" value="<?=1234567+$school_id ?>" >
			<input type="hidden" name="school_id2" value="<?=1234567-$school_id ?>" >
		</form>
		</p>
			<table width='400' cellpadding='5' cellspacing='0' border='1' style='border-collapse:collapse;' align='center'>
				<thead>
					<tr>
						<td>Reg No</td>
						<td><?=$school['id']?></td>
					</tr>
					<tr>
						<td>School Code</td>
						<td><?=$school['school_id']?></td>
					</tr>
					<tr>
						<td>School Name</td>
						<td><?=$school['school_name']?></td>
					</tr>
					<tr>
						<td>City</td>
						<td><?=$school['village_name'].",".$school['mandal_name'].",".$school['district_name']?></td>
					</tr>
					<tr>
						<td>Contact</td>
						<td><?=$school['contact_person']?$school['contact_person']:"-"?></td>
					</tr>
					<tr>
						<td>Email</td>
						<td><?=$school['email']?></td>
					</tr>
					<tr>
						<td>Phone</td>
						<td><?=$school['phone']?></td>
					</tr>
					<tr>
						<td>Phone2</td>
						<td><?=$school['phone2']?$school['phone2']:"-"?></td>
					</tr>
					<tr>
						<td>Students</td>
						<td><?=$school['total_students'] ?></td>
					</tr>
					<tr>
						<td>Teachers</td>
						<td><?=$school['total_teachers']?></td>
					</tr>
					<tr>
						<td>Accommodation</td>
						<td><?=($school['accommodation']?"Yes":"No")?></td>
					</tr>
					<tr>
						<td>Registered Date: </td>
						<td><?=($school['reg_date']!="0000-00-00 00:00:00"?date("d M, Y", strtotime($school['reg_date'])):"")?></td>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</center>
			
		
<?php	}else if( $_GET['view'] == "school_details"){
		$query = "select * from kriya_schools where id = '".$_GET['school_id']."' ";
		$res = mysqli_query($connection,$query);
		$school = mysqli_fetch_assoc($res);  //print_r($school);
		$options = array();
		$query = "select * from kriya_options where school_id = '" . $_GET['school_id'] . "' ";
		$res = mysqli_query($connection,$query);
		while( $row = mysqli_fetch_assoc( $res )){
			$options[ $row['item_id'] ] = $row;
		}
		//print_r($options);
		$school_id = $_GET['school_id'];
		
		if( $_GET['test'] == "test" ){
			echo "<pre>";
			print_r( $config_categories );
			echo "</pre>";
		} 
		
		?>
		<div style='height:10px;'></div>
		<div align='right' style='padding:10px;'>
		<a style='margin:20px; padding:10px; cursor:pointer;width:200px !important;font-weight:bold;text-decoration:none;color:black;background-color:#ffef96;border-radius:5px;' target="_blank" href='download_pdf.php?event=download_pdf&record_id=<?= $school_id ?>'>Download Pdf</a></div>
		<center>
			<table width='400' cellpadding='5' cellspacing='0' border='1' style='border-collapse:collapse;' align='center'>
					<tr>
						<td>Reg No</td>
						<td><?=$school['id']?></td>
					</tr>
					<tr>
						<td>School Code</td>
						<td><?=$school['school_id']?></td>
					</tr>
					<tr>
						<td>School Name</td>
						<td><?=$school['school_name']?></td>
					</tr>
					<tr>
						<td>City</td>
						<td><?=$school['village_name'].",".$school['mandal_name'].",".$school['district_name']?></td>
					</tr>
					<tr>
						<td>Contact</td>
						<td><?=$school['contact_person']?$school['contact_person']:"-"?></td>
					</tr>
					<tr>
						<td>Email</td>
						<td><?=$school['email']?></td>
					</tr>
					<tr>
						<td>Phone</td>
						<td><?=$school['phone']?></td>
					</tr>
					<tr>
						<td>Phone2</td>
						<td><?=$school['phone2']?$school['phone2']:"-"?></td>
					</tr>
					<tr>
						<td>Students</td>
						<td><?=$school['total_students']?></td>
					</tr>
					<tr>
						<td>Teachers</td>
						<td><?=$school['total_teachers']?></td>
					</tr>
					<tr>
						<td>Accommodation</td>
						<td><?=($school['accommodation']?"Yes":"No")?></td>
					</tr>
					<tr>
						<td>Registered Date: </td>
						<td><?=($school['reg_date']!="0000-00-00 00:00:00"?date("d M, Y", strtotime($school['reg_date'])):"")?></td>
					</tr>
				<tbody>
				</tbody>
			</table>
		</center>
		<div style='height:40px;'></div>
		<center>
			<div align='left' style='width:800px;' >
				<table border=1 style="border-collapse:collapse;" width="100%" cellpadding='5' cellspacing='0' >
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
				foreach ($config_categories as $key => $value){
					$key = (int)$key; ?>
					<tr>
					<td class="col_" align='right'><?=$value['sno']?></td>
					<td class="col2" ><?=$value['name']?></td>
				<?php	if( $value["enabled"][0] ){ ?>
					<td class="col3" align='center'><?=$value['group']?($options[ $key ]['sub_jrs']?$options[ $key ]['sub_jrs']:""):"" ?></td>
					<td class="col3" align='center'><?=($options[ $key ]['sub_jrs_cnt']?$options[ $key ]['sub_jrs_cnt']:"") ?></td>
					<?php
					}else{
						echo "<td align=center>--</td>";
						echo "<td align=center>--</td>";
					} ?>

				<?php	if( $value["enabled"][1] ){ ?>
					<td class="col3" align='center'><?=$value['group']?($options[ $key ]['jrs']?$options[ $key ]['jrs']:""):"" ?></td>
					<td class="col3" align='center'><?=($options[ $key ]['jrs_cnt']?$options[ $key ]['jrs_cnt']:"") ?></td>
				<?php
					}else{
						echo "<td align=center>--</td>";
						echo "<td align=center>--</td>";
					} ?>

				<?php	if( $value["enabled"][2] ){ ?>
					<td class="col3" align='center'><?=$value['group']?($options[ $key ]['srs']?$options[ $key ]['srs']:""):"" ?></td>
					<td class="col3" align='center'><?=($options[ $key ]['srs_cnt']?$options[ $key ]['srs_cnt']:"") ?></td>
					<?php
					}else{
						echo "<td align=center>--</td>";
						echo "<td align=center>--</td>";
					} ?>
				</tr>
			<?php	} ?>
					</tbody>
				</table>
			</div>
		</center>
<?php	}else if( $_GET['view'] == "report1" && !$_GET['item_id'] ){

		$counts = array();
		foreach( $config_categories as $key => $value ){
			$counts[ $key ] = array();
		}

		$query = "select item_id, sum(sub_jrs) as groups, sum(sub_jrs_cnt) as cnt, count(school_id) as school_cnt from kriya_options where sub_jrs > 0 group by item_id";
		$res2 = mysqli_query( $connection, $query );
		while( $row = mysqli_fetch_assoc($res2) ){
			$counts[ $row['item_id'] ][ "sub_jrs" ] = array( "groups"=>$row['groups'], "students"=>$row['cnt'], "schools"=> $row['school_cnt'] );
		}

		$query = "select item_id, sum(jrs) as groups, sum(jrs_cnt) as cnt, count(school_id) as school_cnt from kriya_options where jrs > 0 group by item_id";
		$res2 = mysqli_query( $connection, $query );
		while( $row = mysqli_fetch_assoc($res2) ){
			$counts[ $row['item_id'] ][ "jrs" ] = array( "groups"=>$row['groups'], "students"=>$row['cnt'], "schools"=> $row['school_cnt'] );
		}

		$query = "select item_id, sum(srs) as groups, sum(srs_cnt) as cnt, count(school_id) as school_cnt from kriya_options where srs > 0 group by item_id";
		$res2 = mysqli_query( $connection, $query );
		while( $row = mysqli_fetch_assoc($res2) ){
			$counts[ $row['item_id'] ][ "srs" ] = array( "groups"=>$row['groups'], "students"=>$row['cnt'], "schools"=> $row['school_cnt'] );
		}

		/*
		echo "<pre>";
		print_r( $counts );
		echo "</pre>";
		*/

		echo "<center><div style='width:500px; text-align:left;' align='left' >";
		echo "<table class='ddd' border=1 style=\"border-collapse:collapse;\" cellpadding='5' align='left'>";
		echo "<thead>";
		echo "<tr valign='middle'>";
		echo "<td rowspan=\"2\" class=\"col_\" align='center'>Item Number</td>";
		echo "<td rowspan=\"2\" class=\"col_\" align='center'>Item Name</td>";
		echo "<td colspan=\"3\" class=\"col_\" align='center'>Sub Juniors</td>";
		echo "<td colspan=\"3\" class=\"col_\" align='center'>Juniors</td>";
		echo "<td colspan=\"3\" class=\"col_\" align='center'>Seniors</td>";
		echo "</tr>";
		echo "</tr>";
		echo "<td class=\"col_\" align='center'>Groups</td>";
		echo "<td class=\"col_\" align='center'>Students</td>";
		echo "<td class=\"col_\" align='center'>Schools</td>";
		echo "<td class=\"col_\" align='center'>Groups</td>";
		echo "<td class=\"col_\" align='center'>Students</td>";
		echo "<td class=\"col_\" align='center'>Schools</td>";
		echo "<td class=\"col_\" align='center'>Groups</td>";
		echo "<td class=\"col_\" align='center'>Students</td>";
		echo "<td class=\"col_\" align='center'>Schools</td>";
		echo "</tr></thead><tbody>";
		foreach( $config_categories as $key => $value ){
			echo "<tr>";
			echo "<td align='center'>".$key."</td>";
			echo "<td align='left' nowrap>".$value['name']."</td>";
			if( $counts[ $key ]['sub_jrs']['students'] ){
			echo "<td align='center'><a href=\"?view=report1&item_id=".$key."&t=sub_jrs\" >".$counts[ $key ]['sub_jrs']['groups']."</a></td>";
			echo "<td align='center'><a href=\"?view=report1&item_id=".$key."&t=sub_jrs\" >".$counts[ $key ]['sub_jrs']['students']."</a></td>";
			echo "<td align='center'><a href=\"?view=report1&item_id=".$key."&t=sub_jrs\" >".$counts[ $key ]['sub_jrs']['schools']."</a></td>";
			}else{
			echo "<td align='center'>-</td>";
			echo "<td align='center'>-</td>";
			echo "<td align='center'>-</td>";
			}
			if( $counts[ $key ]['jrs']['students'] ){
			echo "<td align='center'><a href=\"?view=report1&item_id=".$key."&t=jrs\" >".$counts[ $key ]['jrs']['groups']."</a></td>";
			echo "<td align='center'><a href=\"?view=report1&item_id=".$key."&t=jrs\" >".$counts[ $key ]['jrs']['students']."</a></td>";
			echo "<td align='center'><a href=\"?view=report1&item_id=".$key."&t=jrs\" >".$counts[ $key ]['jrs']['schools']."</a></td>";
			}else{
			echo "<td align='center'>-</td>";
			echo "<td align='center'>-</td>";
			echo "<td align='center'>-</td>";
			}
			if( $counts[ $key ]['srs']['students'] ){
			echo "<td align='center'><a href=\"?view=report1&item_id=".$key."&t=srs\" >".$counts[ $key ]['srs']['groups']."</a></td>";
			echo "<td align='center'><a href=\"?view=report1&item_id=".$key."&t=srs\" >".$counts[ $key ]['srs']['students']."</a></td>";
			echo "<td align='center'><a href=\"?view=report1&item_id=".$key."&t=srs\" >".$counts[ $key ]['srs']['schools']."</a></td>";
			}else{
			echo "<td align='center'>-</td>";
			echo "<td align='center'>-</td>";
			echo "<td align='center'>-</td>";
			}
			echo "<tr>";
		}
		echo "</tbody></table>";
		echo "</div></center>";

	}else if( $_GET['view'] == "report1" && $_GET['item_id'] ){

		$query = "select a.*, 
		b.school_id,
		b.id as school_regno,
		b.school_name, 
		b.village_name, 
		b.mandal_name,
		b.district_name,
		b.contact_person, 
		b.phone, 
		b.phone2
		from ( 
			select * from kriya_options where `item_id` = " . $_GET['item_id'] . " and `" . $_GET['t'] . "` > 0 order by school_id
		) as a 
		left join kriya_schools as b on ( a.school_id = b.id ) order by b.id";     
		//echo $query;   
		//select * from kriya_options where `item_id` = 4 and `srs` > 0;          
		$q1 = "select * from kriya_options where `item_id` = " . $_GET['item_id'] . " and `" . $_GET['t'] . "` > 0 order by school_id";
		$res = mysqli_query( $connection, $query );
		if(mysqli_error($connection)){
			echo $q1;
			echo mysqli_error($connection);
			exit;
		}
		$schools = array();
		while( $row = mysqli_fetch_assoc($res) ){
			$schools[$row['school_regno']] = $row;
			$schools[$row['school_regno']]["school_name"] = $row["school_name"]. " ,".$row["village_name"]. " , ". $row["mandal_name"]." , ".$row["district_name"];
			$schools[$row['school_regno']]["city"] =  $row["contact_person"] . " ,".$row['phone'] . " "  .($row['phone2']?",".$row['phone2']:"") . " "  . ($row['email']?"<BR>".$row['email']:"");
		}
	//	echo "<pre>";print_r($schools);exit;
		$d = $config_categories[ $_GET['item_id'] ];
		//print_r( $d );

		echo "<center><div style='width:900px; text-align:left;' align='left' >";
		echo "<div style='font-size:18px; font-weight:bold; line-height:30px;'>" . $d["name"] . " - " . ($_GET['t']=="sub_jrs"?"Sub Juniors":($_GET['t']=="jrs"?"Juniors":"Seniors")) . "</div>";
		echo "<table class='ddd' border=\"1\" style=\"border-collapse:collapse;\"  width=\"100%\" cellpadding='5' cellspacing='1' >";
		echo "<thead>";
		echo "<tr valign='middle' >";
		echo "<td>Reg No</td>";
		echo "<td>School Id</td>";
		echo "<td>School Name</td>";
		echo "<td>Contact</td>";
		echo "<td>Groups</td>";
		echo "<td>Students</td>";
		echo "</tr>";
		echo "</thead><tbody>";
		$tot = 0;$totc = 0;
		foreach($schools as $key => $value){
			echo "<tr>";
			echo "<td align='right'>".$key."</td>";
			echo "<td>".$value["school_id"] ."</td>";
			echo "<td>".$value["school_name"] ."</td>";
			echo "<td>".$value["city"] ."</td>
			<td>" . $value[ $_GET['t'] ] . "</td>
			<td>" . $value[ $_GET['t'] . "_cnt" ] . "</td>";
			echo "</tr>";
			$tot += $value[ $_GET['t'] ];
			$totc += $value[ $_GET['t'] . "_cnt" ];
		}
			echo "<tr>";
			echo "<td colspan=\"4\">Total</td>
			<td>".$tot."</td>
			<td>".$totc."</td>";
			echo "</tr>";
		echo "</tbody></table>";
		echo "</div></center>";

	}else if( $_GET['view'] == "download" ){

		echo "<div align='center' style='font-size:12px; line-height:40px;' ><a href='?action=download_report' >Download Excel</a></div>";

		$schools = array();
		$query = "select * from kriya_schools where school_id != '' order by school_id";
		$res = mysqli_query($connection,$query);
		while( $row = mysqli_fetch_assoc( $res)){
			$schools[ $row['id'] ] = $row;
		}

		$options = array();
		$query = "select * from kriya_options order by item_id ";
		$res = mysqli_query($connection,$query);
		while( $row = mysqli_fetch_assoc( $res )){
			if( !$schools[ $row['school_id'] ][ "items" ] ){
				$schools[ $row['school_id'] ][ "items" ] = array();
			}
			$schools[ $row['school_id'] ][ "items" ][ $row['item_id'] ] = $row;
			$options[ $row['item_id'] ] = 1;
		}
		//echo "<pre>";print_r($schools);
		$itemtypes = array("sub_jrs"=>"Sub Juniors", "jrs"=>"Juniors", "srs"=>"Seniors");
		foreach( $itemtypes as $item_type=>$item_type_name ){
			echo "<div style='margin:5px; margin-top:30px; padding:5px; font-weight:bold; font-size:14px;'>Group: " . $item_type_name . "</div>";
			echo "<table border=\"1\" cellpadding='5' style='border-collapse:collapse;' >";
			echo "<thead><tr valign='middle'><td height=40 align='center' >Reg No</td>
			<td>School Code</td><td>School Name</td>";
			//echo "<td>Contact</td>";
			foreach( $options as $item_id=>$ii ){
				echo "<td align='center' width=\"20\">" . $item_id . "</td>";
			}
			echo "</tr></thead>";
			foreach( $schools as $school_id=>$school ){
				$f = 0;
				foreach( $options as $item_id=>$ii ){
					if( $school["items"][ $item_id ][ $item_type ] ){
						$f = 1;
					}
				}
				if( $f ){
					echo "<tr>";
					echo "<td align='center'>" . $school['id'] . "</td>";
					echo "<td align='center'>" . $school['school_id'] . "</td>";
					echo "<td nowrap ><b>".$school['school_name']."</b><BR><span style='color:#999999;'>".($school['city']!=$school['district']?($school['city']. ", ".$school["district"]):$school['city']) ."</span></td>";
					//echo "<td nowrap >".$school['contact_person']."<BR>". $school['phone'] . ($school['phone2']?", ".$school['phone2']:"") ."</td>";
					foreach( $options as $item_id=>$ii ){
						echo "<td align='center'>" . ($school["items"][ $item_id ][ $item_type ]?$school["items"][ $item_id ][ $item_type ] . "/" . $school["items"][ $item_id ][ $item_type . "_cnt" ]:"") . "</td>";
					}
					echo "</tr>";
				}
			}
			echo "</table>";
		}

	}else if( $_GET['view'] == "slips" ){
		//include("admin_slips.php");
	}
	echo "<BR><BR><BR><BR><BR><BR>";

}else{ ?>

	<form method='post' >
		<table cellpadding="10" align="center" style="max-width:400px;" >
		<tr>
			<td>Password</td>
				<td><input type='password' value='<?=$_SESSION['post']['password'] ?>' placeholder="Enter Password" required name='password'></td>
		</tr>
		<tr>
			<td>Security</td>
			<td>
				<div><img src="" id="captcha_img" ></div>
				<div style="padding:10px;"><a href="Javascript:reloadcaptcha()">Refresh</a></div>
			</td>
		</tr>
		<tr>
			<td>Code</td>
			<td>
				<input type='text' value='' placeholder="Enter Above Code" required name='code' id="code">
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type='submit' value='Submit' name='ss' style='width:100px !important;'>
			</td>
		</tr>
		</table>
		<div>
			<input type="hidden" name="action" value="admin_login">
			<input type="hidden" name="captcha_code" id="captcha_code" value="">
		</div>
	</form>

	<script>
		function reloadcaptcha(){
			var con = new XMLHttpRequest();
			con.open("GET", "?action=getcaptcha", true );
			con.onload = function(v){
				var v= JSON.parse(this.responseText);
				document.getElementById("captcha_img").src = v['img'];
				document.getElementById("captcha_code").value = v['code'];
			};
			con.send();
		}
		setTimeout("reloadcaptcha()",2000);
	</script>

<?php } ?>

</body>
</html>
