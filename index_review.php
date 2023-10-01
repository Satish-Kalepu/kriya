<div id="app" >
	<form method='post' onsubmit="return validate_form()" >
		<div id='registration_form_div' align='left' style='width:100%;max-width:100%;border:2px solid white; background-color:#ffef96; border-radius:5px; padding:10px; margin:auto;' >
			<div align="right" style="padding-right:50px;"><a href='?action=logout' >Logout</a><BR><BR></div>
			<table width='100%' cellpadding='5' cellspacing='1' border='0' style='border-collapse:collapse;' align='center'>
				<tr bgcolor='#ffef96'>
					<td align="right">School:</td>
					<td>
						<div style="margin-left:10px"><?=htmlspecialchars($data1['school_name']) ?></div>
						<div style="margin-left:10px"><?=htmlspecialchars($data1['school_category']) ?></div>
						<div style="margin-left:10px"><?=htmlspecialchars($data['email']) ?>, <?=htmlspecialchars($data['phone']) ?></div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td align="right">Teacher Name:</td>
					<td>
						<div style="margin-left:10px"><input type='text'  class="input_div"  value="<?=(htmlspecialchars($data['contact_person']) )?>" name='teacher_name' id='teacher_id' required autocomplete="off" style="width:200px"></div>
					</td>
				</tr>
				<tr>
					<td align="right">Phone 2:</td>
					<td><div style="margin-left:10px"><input type='number' class="input_div"  value="<?=($data['phone2']) ?>" name='phone2' id='pho2' style="width:200px" autocomplete="off"></div></td>
				</tr>
				<tr>
					<td align="right">Count of teachers accompanied</td>
					<td>
						<div style="margin-left:10px">
							<input type='number' name='total_teachers'  id='total_teachers_id' class="input_div" style='width:60px;' value="<?=$data['total_teachers']?>"  autocomplete="off">
						</div>
					</td>
				</tr>
			<?php if( $data1["district_code"]!=2814 ){ ?>
				<tr  bgcolor='#ffef96' >
					<td align="right">Need accommodation?:</td>
					<td>
						<div style="margin-left:10px"><input type='checkbox' name='accommodation'  id='accommodation_id' style='width:15px; height:15px;' value="y" <?=$data['accommodation']?"checked":""?> ></div>
					</td>
				</tr>
				<tr bgcolor='#ffef96'>
					<td colspan='2' align='center' style='font-size:10px;color:gray;'>above details required for food preparation.</td>
				</tr>
			<?php	} ?>
			</table>
			<input type='hidden' name='total_students'  id='total_students_id' value="<?=$data['total_students'] ?>" >
	</div>	
	<center>
		<p style='color:white; font-weight:bold;'>Please check all the conditions and age groups before submitting.</p>
		<p style='color:white; font-weight:bold;'>Last date for submission and corrections 22nd November 2023. Max 60 members are allowed from a school.</p>
	</center>
	<table class="ddd satfixed" border=0 style="border-collapse:collapse;" width="100%" cellpadding='5' cellspacing='1'>
		<thead class="sticky1">
			<tr valign='middle' bgcolor='#3B5998' style='color:white;font-weight:bold;'>
				<td class="col_"  align='center' width="40" rowspan="2" >ID</td>
				<td class='hcol3' align='center' rowspan="2"  >Category</td>
				<td colspan='3' align='center' >Persons/Groups</td>
				<td class='hcol6' align='center' rowspan="2" >Rules</td>
			</tr>
			<tr valign='middle' bgcolor='#3B5998' style='color:white;font-weight:bold;'>
				<td width='60'align='center'>Sub Junior</td>
				<td width='60' align='center'>Junior</td>
				<td width='60' align='center'>Senior</td>
			</tr>
		</thead>
		<thead class="sticky2">
			<tr bgcolor='#3B5998' style='color:white;font-weight:bold;'>
				<td width='30' align='center'>Sub Junior</td>
				<td width='30' align='center'>Junior</td>
				<td width='30' align='center'>Senior</td>
			</tr>
		</thead>
		<tbody>
	<?php	foreach ($config_categories as $key => $value) {
			$key = (int)$key;
			$vshow = false;			
			if( ($value["enabled"][0] && $config_school_types[ 'sub_jrs' ][ $data1['school_category'] ]) || ($value["enabled"][1] && $config_school_types[ 'jrs' ][ $data1['school_category'] ])  ||  ($value["enabled"][2] && $config_school_types[ 'srs' ][ $data1['school_category'] ] ) ){
				$vshow = true;
			}
			if( $vshow || true){
			?>
			<template v-for="cd,ci in categories" >
			<tr bgcolor='#ffef96' v-if="cd['enabled'][0]||cd['enabled'][1]||cd['enabled'][2]" >
				<td class="col_" align='right'>{{ cd['sno'] }}</td>
				<td class="col2" width='250'>{{ cd['name'] }}</td>
				<td class="col3" align='center' nowrap >
					<template v-if="data1['school_category'] in config_school_types[ 'sub_jrs' ]" >
						<template v-if="'group' in cd" >
							<div v-for="gd,gi in cd['enabled'][0]" >
								<span v-if="cd['enabled'][0]>1" >Group {{ gi+1 }}</span><span v-else>Group</span>
								<select title="Select Number of Students in the Group" class="sel" data-max="cd['max'][0]" >
									<option value="0">-</option>
								</select>
							</div>
						</template>
						<template v-else>
							<select title="Select Number of Students in the Group" class="sel" data-max="cd['max'][0]" >
								<option value="0">-</option>
							</select>
						</template>
					</template>
				</td>
				<td class="col4" align='center' nowrap >
				<?php	if( $value["enabled"][1] && $config_school_types[ 'jrs' ][ $data1['school_category'] ] ){   ?>
						<?php if($value['group']){ for($k=1;$k<=$value['enabled'][1];$k++){ ?>
							<div><?=$value['enabled'][1]>1?"Group ". $k .": ":"Group: " ?> 
						<select title="Select Number of Students in the Group"  class="sel"  name='stu[<?=$key?>][jrs][<?=$k-1 ?>]' id='jrs_<?=$key ?>_<?=$k-1 ?>' data-max='<?=$value['max'][1]?>'  onchange="validate_this(this)">
							<option value='0'>-</option>";
							<?php 
							for($kk=$value['max'][1][0];$kk<=$value['max'][1][1];$kk++){
								echo "<option ".($selection[$key]['jrs'][$k-1]==$kk?"selected":""). " value='".$kk."'>".$kk."</option>";
							}
							?>
						</select></div>
						<?php }}else{ ?>
						<select class="sel"  name='stu[<?=$key?>][jrs][0]' id='jrs_<?=$key ?>_0' data-max='<?=$value['max'][1]?>'  onchange="validate_this(this)">
							<option value='0'>-</option>";
							<?php 
							for($kk=$value['max'][1][0];$kk<=$value['max'][1][1];$kk++){
								echo "<option ".($selection[$key]['jrs'][0]==$kk?"selected":""). " value='".$kk."'>".$kk."</option>";
							}
							?>
						</select>
						<?php } ?>					
				<?php	}else{?>
						<div title="not available for <?=$data1['school_category'] ?> and in juniors ">-</div>
				<?php	}?>
				</td>
				<td class="col5" align='center' nowrap >
				<?php	if( $value["enabled"][2] && $config_school_types[ 'srs' ][ $data1['school_category'] ] ){   ?>
						<?php if($value['group']){ for($k=1;$k<=$value['enabled'][2];$k++){ ?>
							<div><?=$value['enabled'][2]>1?"Group ". $k .": ":"Group: " ?> 
						<select title="Select Number of Students in the Group"  class="sel"  name='stu[<?=$key?>][srs][<?=$k-1 ?>]' id='srs_<?=$key ?>_<?=$k-1 ?>' data-max='<?=$value['max'][2]?>'  onchange="validate_this(this)">
							<option value='0'>-</option>";
							<?php 
							for($kk=$value['max'][2][0];$kk<=$value['max'][2][1];$kk++){
								echo "<option ".($selection[$key]['srs'][$k-1]==$kk?"selected":""). " value='".$kk."'>".$kk."</option>";
							}
							?>
						</select></div>
						<?php }}else{ ?>
						<select class="sel"  name='stu[<?=$key?>][srs][0]' id='srs_<?=$key ?>_0' data-max='<?=$value['max'][2]?>'  onchange="validate_this(this)">
							<option value='0'>-</option>";
							<?php 
							for($kk=$value['max'][2][0];$kk<=$value['max'][2][1];$kk++){
								echo "<option ".($selection[$key]['srs'][0]==$kk?"selected":""). " value='".$kk."'>".$kk."</option>";
							}
							?>
						</select>
						<?php } ?>
				<?php	}else{?>
						<div title="not available for <?=$data1['school_category'] ?> and for seniors ">-</div>
				<?php	}?>
				</td>
				<td class="col6" width='450'><?=$value['details'] ?></td>
			</tr>
			<?php } ?>
	<?php } ?>	
		</tbody>
	</table>
	<div align='center' id="footer_msg" style='padding:10px;position:fixed; left:0px; bottom:0px; width:100%; background-color:rgba(0,0,0,0.8);' align=center >
		<div style="display:inline-block; font-size:18px; color:white; margin-right:50px;" >Total Students:  
		<span style='font-weight:bold; font-size:18px; ' id="total_students_div" ><?=$data['total_students'] ?></span>
		</div> 
		<input type='submit' value='Save Details &amp; Confirm Participation' name='Register' id="register_btn" class="input_div" style=' padding:5px; cursor:pointer; margin:10px;width:300px !important; font-weight:bold;'>
	</div>
	<input type='hidden' name='action' value='register'>
	<input type='hidden' name='school_id' value='<?=$module ?>'>
	</form>
</div>	
	<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
<script>
var app = Vue.createApp({
	data(){
		return {
			categories: <?=json_encode($config_categories) ?>,
			config_school_types: <?=json_encode($config_school_types) ?>,
			data1: <?=json_encode($data1) ?>,
			data: <?=json_encode($data) ?>,
		};
	},
	mounted(){
		
	},
	methods: {
	}
}).mount("#app");
</script>