<?php
	session_start();

	include('../config_global.php');
	
	if( !isset($login_enable)  || !isset($email_cc_list) || !isset($user)  ){
		http_response_code(500);
		echo "configuration pending..";exit;
	}

	include('db.php');
	include('config.php');
	
	if( $_SESSION['special3'] == "yes" ){
		$login_enable = true;
	}
        //$_SESSION['special2'] = "yes";  //   remove this to disable login.
	if( $_GET['enable'] == "special3" ){
		$_SESSION['special3'] = "yes";
		header("Location: /?special_login_enabled");
		exit;
	}

	//include('config_telugu_names.php');
	include("smtp_ses.php");
	include("router.php");
	include("actions.php");

	/*echo "<pre>";
	print_r( $config_categories );
	echo "</pre>";*/

	$config_school_types = array(
		'sub_jrs'=>[
			"Pr. Up Pr. and Secondary Only"=>1,
			"Pr. with Up.Pr. sec. and H.Sec."=>1,
			"primary"=>1,
			"Primary"=>1,
			"Primary with Upper Primary"=>1,
			"-"=>1,
		],
		'jrs'=>[
			"Pr. Up Pr. and Secondary Only"=>1,
			"Pr. with Up.Pr. sec. and H.Sec."=>1,
			"Primary with Upper Primary"=>1,
			"Secondary"=>1,
			"secondary"=>1,
			"Secondary Only"=>1,
			"Secondary School"=>1,
			"Secondary with Higher Secondary"=>1,
			"Senior Secondary"=>1,
			"Up. Pr. Secondary and Higher Sec"=>1,
			"Upper Pr. and Secondary"=>1,
			"Upper Primary only"=>1,
			"-"=>1,		
		],
		'srs'=>[
			"Pr. Up Pr. and Secondary Only"=>1,
			"Pr. with Up.Pr. sec. and H.Sec."=>1,
			"Primary with Upper Primary"=>1,
			"Secondary"=>1,
			"secondary"=>1,
			"Secondary Only"=>1,
			"Secondary School"=>1,
			"Secondary with Higher Secondary"=>1,
			"Senior Secondary"=>1,
			"Up. Pr. Secondary and Higher Sec"=>1,
			"Upper Pr. and Secondary"=>1,
			"Upper Primary only"=>1,
			"-"=>1,
		],		
	);

	if( !is_numeric($module) ){
		unset($module);
	}

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
	if( !$_SESSION['logged_in'] ){
		header("Location: index.php");exit;
	}else{
		$school_res = mysqli_query( $connection, "select * from kriya_schools where id =" . $_SESSION["user_id"]);
		$data = mysqli_fetch_assoc( $school_res );
		if( !$data ){
			session_destroy();
			session_regenerate_id();
			header("Location: /?event=SchoolNotFound");
			exit;
		}

		$school_res1 = mysqli_query( $connection, "select * from kriya_school_list where school_id =" . $data["school_id"]);
		$data1 = mysqli_fetch_assoc( $school_res1 );
		$data1['school_category'] = $data1['school_category']?$data1['school_category']:"-";
		$items_data = array();
		$item_res = mysqli_query( $connection,  "select * from kriya_options where school_id = " . $_SESSION['user_id'] );
		echo mysqli_error( $connection );
		while( $r = mysqli_fetch_assoc( $item_res ) ){
			$items_data[ $r['item_id'] ] = $r;
		}
		$selection = json_decode($data['selection'],true);
		// print_r($data);
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Kriya Registration Form</title>
	<script src="js/axios.min.js"></script>
	<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<link rel="stylesheet" href="../bootstrap/bootstrap.min.css">
	<style>
		body {
			background-color: #2c95da;
		}
		div {
			padding: 4px;
		}
	</style>
</head>
<body>
	<div id="app">
		<div class="text-center">
		    <img src="/kriya-head1.jpg" class="img-fluid" alt="Logo">
		</div>
		<table class="table table-bordered table-hover">
		    <thead style="background-color: #3B5998; color: white;">
		        <tr>
		            <th rowspan="2" class="text-center" width="40">ID</th>
		            <th rowspan="2" class="text-center">Category</th>
		            <th colspan="3" class="text-center">No of Students</th>
		            <th rowspan="2" class="text-center">Rules</th>
		        </tr>
		        <tr>
		            <th class="text-center" width="60">Sub Junior</th>
		            <th class="text-center" width="60">Junior</th>
		            <th class="text-center" width="60">Senior</th>
		        </tr>
		    </thead>
		    <tbody style="background-color: #ffef96;">
		        <tr v-for="value, key in config_categories" :key="key">
		        	<td>{{ value.sno }}</td>
		        	<td>{{ value.name }}</td>
		        	<td class="text-center">
		                <div v-if="value.enabled[0] && config_school_types['sub_jrs'][data1.school_category]">
		                    <div v-if="value.group && value.enabled[0] > 1" v-for="k in value.enabled[0]">
		                        <button type="button" @click="openPopup(value, 'sub_jrs')" :class="studentCount(value.sno, 'sub_jrs') > 0 ? 'btn btn-secondary btn-sm mb-2' : 'btn btn-light btn-sm mb-2'" v-html="'Group ' + k + ': <b>' + studentCount(value.sno, 'sub_rs') + '</b>'">
		                        </button>
		                    </div>
		                    <div v-else>
		                        <span @click="openPopup(value, 'sub_jrs')" :class="studentCount(value.sno, 'sub_jrs') > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
		                            {{ studentCount(value.sno, 'sub_jrs') || 0 }}
		                        </b></span>
		                    </div>
		                </div>
		                <div v-else>-</div>
		            </td>
		            <td class="text-center">
		                <div v-if="value.enabled[1] && config_school_types['jrs'][data1.school_category]">
		                    <div v-if="value.group && value.enabled[1] > 1" v-for="k in value.enabled[1]">
		                        <button type="button" @click="openPopup(value, 'jrs')" :class="studentCount(value.sno, 'jrs') > 0 ? 'btn btn-secondary btn-sm mb-2' : 'btn btn-light btn-sm mb-2'" v-html="'Group ' + k + ': <b>' + studentCount(value.sno, 'jrs') + '</b>'">
		                        </button>
		                    </div>
		                    <div v-else>
		                        <span @click="openPopup(value, 'jrs')" :class="studentCount(value.sno, 'jrs') > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
		                            {{ studentCount(value.sno, 'jrs') || 0 }}
		                        </b></span>
		                    </div>
		                </div>
		                <div v-else>-</div>
		            </td>
		            <td class="text-center">
		                <div v-if="value.enabled[2] && config_school_types['srs'][data1.school_category]">
		                    <div v-if="value.group && value.enabled[2] > 1" v-for="k in value.enabled[2]">
		                        <button type="button" :class="studentCount(value.sno, 'srs') > 0 ? 'btn btn-secondary btn-sm mb-2' : 'btn btn-light btn-sm mb-2'" @click="openPopup(value, 'srs')" v-html="'Group ' + k + ': <b>' + studentCount(value.sno, 'srs') + '</b>'">
		                        </button>
		                    </div>
		                    <div v-else>
		                        <span @click="openPopup(value, 'srs')" :class="studentCount(value.sno, 'srs') > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
		                            {{ studentCount(value.sno, 'srs') || 0 }}
		                        </b></span>
		                    </div>
		                </div>
		                <div v-else>-</div>
		            </td>
		            <td class="text-left">{{ value.details }}</td>
		        </tr>
		    </tbody>
		</table>
	</div>
	<script>
		var app=Vue.createApp({
			data(){
				return{
					home_page: 'test_index.php',
					config_categories : <?=json_encode( $config_categories ) ?>,
					config_school_types: <?=json_encode($config_school_types) ?>,
					data1: <?=json_encode($data1) ?>,
					data: <?=json_encode($data) ?>,
				};
			},
			mounted(){
				// console.log("Data:", this.data);
				// console.log("Data1:", this.data1);
				// console.log("Value:", this.config_categories);
				// console.log("school_types:", this.config_school_types);
			},
			methods:{
				validate_school_code(){
					school_code = document.getElementById("school_code").value;
					return true;
				},
				studentCount(){

				},
			},
		}).mount("#app");
	</script>
</body>
</html>