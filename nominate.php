<?php
session_start();

include('../config_global.php');

if( !isset($login_enable)  || !isset($email_cc_list) || !isset($user)  ){
	http_response_code(500);
	echo "configuration pending..";exit;
}

// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
// exit;

include('db.php');
include('config.php');

//include('config_telugu_names.php');
include("smtp_ses.php");
include("actions.php");

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

	if ($data['type'] == "school") {
		$items_data = array();
		$item_res = mysqli_query( $connection,  "select * from kriya_options where user_id = " . $_SESSION['user_id'] );
		echo mysqli_error( $connection );
		while( $r = mysqli_fetch_assoc( $item_res ) ){
			$items_data[ $r['item_id'] ] = $r;
		}
		$selection = json_decode($data['selection'],true);
		// print_r($data);
	}
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
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
	<style>
		body {
			background-color: #2c95da;
		}

		@media only screen and (max-width: 600px) {

			.bigtable{ display:none; }
			.smalltable{ display:block; }
			.details_div {
	            padding: 5px;
	            margin-right: 10px;
	            margin-left: 10px;
	            border: 2px solid white;
	            background-color: #fff4d9;
	        }
		}
		@media only screen and (min-width: 601px) {

			.bigtable{ display:block; }
			.smalltable{ display:none; }
			.details_div {
	            margin: 10px auto;
	            max-width: 600px;
	            padding: 5px;
	            border: 2px solid white;
	            background-color: #fff4d9;
	        }
		}

	</style>
</head>
<body>
	<div id="app">
		<div class="text-center">
		    <img src="/kriya.jpg" class="img-fluid" alt="Logo">
		</div>
		<!-- home landing div -->
		<div>
		    <div class="container" style="max-width: 600px; margin-bottom: 5px;">
				<div style="display: flex; justify-content: space-between; align-items: center;">
				    <div>
				        <!-- <a href="home.php" class="btn btn-link text-white">Back</a> -->
				        <button class="btn btn-light text-dark" v-on:click="goHome">Back</button>
				    </div>
				    <div>
				    	<button class="btn btn-light text-dark" v-on:click="logout">Logout</button>
				        <!-- <a href="?action=logout" class="btn btn-link text-white">Logout</a> -->
				    </div>
				</div>
		    </div>
		    <div class="mb-1 text-center details_div">
	            <div>
	            	<div><b>Selected School:</b> <?php echo $data['school_name']; ?></div>
	            	<div><b>Email:</b> <?php echo $data['email']; ?></div>
	            </div>
	        </div>
		    <div class="bigtable mb-4 p-2 w-100 overflow-auto">
			    <table class="table table-bordered table-hover mb-4">
				    <thead style="background-color: #3B5998; color: white;">
				        <tr>
				            <th rowspan="2" class="text-center">ID</th>
				            <th rowspan="2" class="text-center">Category</th>
				            <th colspan="3" class="text-center">No of Students</th>
				            <th rowspan="2" class="text-center">Rules</th>
				        </tr>
				        <tr>
				            <th class="text-center" width="30">Sub Junior</th>
				            <th class="text-center" width="30">Junior</th>
				            <th class="text-center" width="30">Senior</th>
				        </tr>
				    </thead>
				    <tbody style="background-color: #fff4d9;">
				        <tr v-for="category, category_id in config_categories" :key="category_id">
				        	<td>{{ category.sno }}</td>
				        	<td>{{ category.name }}</td>
				        	<td class="text-center">
				                <div v-if="category.enabled[0]&&data['school_category'] in config_school_types[ 'sub_jrs' ]">
									<div v-if="'group' in category" >
										<div v-if="category.enabled[0]==1" >
											<button @click="openPopup(category, 'sub_jrs', 0)" :class="studentCount(category.sno, 'sub_jrs',0) > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
											{{ studentCount(category.sno, 'sub_jrs',0) }}
											</b></button>
										</div>
										<div v-else>
											<div v-for="group_index in category.enabled[0]" >
												<button style="white-space:nowrap;" type="button" @click="openPopup(category, 'sub_jrs', group_index)" :class="{'btn btn-sm mb-2':true, 'btn-light':(studentCount(category.sno, 'sub_jrs', group_index)==0), 'btn-secondary':(studentCount(category.sno, 'sub_jrs', group_index)>0)}" v-html="'Group ' + group_index + '<BR><b>' + studentCount(category.sno, 'sub_jrs', group_index) + '</b>'"></button>
											</div>
										</div>
									</div>
									<div v-else>
										<button @click="openPopup(category, 'sub_jrs', 0)" :class="studentCount(category.sno, 'sub_jrs',0) > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
										{{ studentCount(category.sno, 'sub_jrs',0) }}
										</b></button>
									</div>
				                </div>
				                <div v-else>-</div>
				            </td>
				            <td class="text-center">
				                <div v-if="category.enabled[1]&&data['school_category'] in config_school_types[ 'jrs' ]">
				                    <div v-if="'group' in category" >
				                    	<div v-if="category.enabled[1]==1" >
											<button @click="openPopup(category, 'jrs', 0)" :class="studentCount(category.sno, 'jrs',0) > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
											{{ studentCount(category.sno, 'jrs',0) }}
											</b></button>
										</div>
										<div v-else>
											<div v-for="group_index in category.enabled[1]" >
												<button style="white-space:nowrap;" type="button" @click="openPopup(category, 'jrs', group_index)" :class="{'btn btn-sm mb-2':true, 'btn-light':(studentCount(category.sno, 'jrs', group_index)==0), 'btn-secondary':(studentCount(category.sno, 'jrs', group_index)>0)}" v-html="'Group ' + group_index + '<BR><b>' + studentCount(category.sno, 'jrs', group_index) + '</b>'"></button>
											</div>
										</div>
									</div>
									<div v-else>
										<button @click="openPopup(category, 'jrs', 0)" :class="studentCount(category.sno, 'jrs',0) > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
										{{ studentCount(category.sno, 'jrs',0) }}
										</b></button>
									</div>
				                </div>
				                <div v-else>-</div>
				            </td>
				            <td class="text-center">
				                <div v-if="category.enabled[2]&&data['school_category'] in config_school_types[ 'srs' ]">
				                    <div v-if="'group' in category" >
				                    	<div v-if="category.enabled[2]==1" >
											<button @click="openPopup(category, 'srs', 0)" :class="studentCount(category.sno, 'srs',0) > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
											{{ studentCount(category.sno, 'srs', 0) }}
											</b></button>
										</div>
										<div v-else>
											<div v-for="group_index in category.enabled[2]" >
												<button style="white-space:nowrap;" type="button" @click="openPopup(category, 'srs', group_index)" :class="{'btn btn-sm mb-2':true, 'btn-light':(studentCount(category.sno, 'srs', group_index)==0), 'btn-secondary':(studentCount(category.sno, 'srs', group_index)>0)}" v-html="'Group ' + group_index + '<BR><b>' + studentCount(category.sno, 'srs', group_index) + '</b>'"></button>
											</div>
										</div>
									</div>
									<div v-else>
										<button @click="openPopup(category, 'srs', 0)" :class="studentCount(category.sno, 'srs',0) > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
										{{ studentCount(category.sno, 'srs',0) }}
										</b></button>
									</div>
				                </div>
				                <div v-else>-</div>
				            </td>
				            <td class="text-left">{{ category.details }}</td>
				        </tr>
				    </tbody>
				</table>
		    </div>
		    <div class="smalltable mb-4 p-2 w-100 overflow-auto" >
		        <div v-for="category, category_id in config_categories" :key="category_id" style="background-color: #fff4d9; border:1px solid white; padding:5px; margin-bottom:10px;">
		        	<div style="display:flex; column-gap:10px; font-size: 1.2rem;">
		        		<div><b>{{ category.sno }}</b></div>
		        		<div><b>{{ category.name }}</b></div>
		        	</div>
					<div class="text-left">{{ category.details }}</div>
		        	<table class="table table-bordered table-hover mb-4">
			        	<thead style="background-color: #3B5998; color: white;">
			        		<tr>
					            <th class="text-center" width="30" style="font-size:0.8rem;">Sub Junior</th>
					            <th class="text-center" width="30" style="font-size:0.8rem;">Junior</th>
					            <th class="text-center" width="30" style="font-size:0.8rem;">Senior</th>
					        </tr>
			        	</thead>
			        	<tbody style="background-color: #fff4d9;">
			        		<td class="text-center">
				                <div v-if="category.enabled[0]&&data['school_category'] in config_school_types[ 'sub_jrs' ]">
									<div v-if="'group' in category" >
										<div v-if="category.enabled[0]==1" >
											<button @click="openPopup(category, 'sub_jrs', 0)" :class="studentCount(category.sno, 'sub_jrs',0) > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
											{{ studentCount(category.sno, 'sub_jrs',0) }}
											</b></button>
										</div>
										<div v-else>
											<div v-for="group_index in category.enabled[0]" >
												<button style="white-space:nowrap;" type="button" @click="openPopup(category, 'sub_jrs', group_index)" :class="{'btn btn-sm mb-2':true, 'btn-light':(studentCount(category.sno, 'sub_jrs', group_index)==0), 'btn-secondary':(studentCount(category.sno, 'sub_jrs', group_index)>0)}" v-html="'Group ' + group_index + '<BR><b>' + studentCount(category.sno, 'sub_jrs', group_index) + '</b>'"></button>
											</div>
										</div>
									</div>
									<div v-else>
										<button @click="openPopup(category, 'sub_jrs', 0)" :class="studentCount(category.sno, 'sub_jrs',0) > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
										{{ studentCount(category.sno, 'sub_jrs',0) }}
										</b></button>
									</div>
				                </div>
				                <div v-else>-</div>
				            </td>
				            <td class="text-center">
				                <div v-if="category.enabled[1]&&data['school_category'] in config_school_types[ 'jrs' ]">
				                    <div v-if="'group' in category" >
				                    	<div v-if="category.enabled[1]==1" >
											<button @click="openPopup(category, 'jrs', 0)" :class="studentCount(category.sno, 'jrs',0) > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
											{{ studentCount(category.sno, 'jrs',0) }}
											</b></button>
										</div>
										<div v-else>
											<div v-for="group_index in category.enabled[1]" >
												<button style="white-space:nowrap;" type="button" @click="openPopup(category, 'jrs', group_index)" :class="{'btn btn-sm mb-2':true, 'btn-light':(studentCount(category.sno, 'jrs', group_index)==0), 'btn-secondary':(studentCount(category.sno, 'jrs', group_index)>0)}" v-html="'Group ' + group_index + '<BR><b>' + studentCount(category.sno, 'jrs', group_index) + '</b>'"></button>
											</div>
										</div>
									</div>
									<div v-else>
										<button @click="openPopup(category, 'jrs', 0)" :class="studentCount(category.sno, 'jrs',0) > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
										{{ studentCount(category.sno, 'jrs',0) }}
										</b></button>
									</div>
				                </div>
				                <div v-else>-</div>
				            </td>
				            <td class="text-center">
				                <div v-if="category.enabled[2]&&data['school_category'] in config_school_types[ 'srs' ]">
				                    <div v-if="'group' in category" >
				                    	<div v-if="category.enabled[2]==1" >
											<button @click="openPopup(category, 'srs', 0)" :class="studentCount(category.sno, 'srs',0) > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
											{{ studentCount(category.sno, 'srs', 0) }}
											</b></button>
										</div>
										<div v-else>
											<div v-for="group_index in category.enabled[2]" >
												<button style="white-space:nowrap;" type="button" @click="openPopup(category, 'srs', group_index)" :class="{'btn btn-sm mb-2':true, 'btn-light':(studentCount(category.sno, 'srs', group_index)==0), 'btn-secondary':(studentCount(category.sno, 'srs', group_index)>0)}" v-html="'Group ' + group_index + '<BR><b>' + studentCount(category.sno, 'srs', group_index) + '</b>'"></button>
											</div>
										</div>
									</div>
									<div v-else>
										<button @click="openPopup(category, 'srs', 0)" :class="studentCount(category.sno, 'srs',0) > 0 ? 'btn btn-secondary btn-sm' : 'btn btn-light btn-sm'"><b>
										{{ studentCount(category.sno, 'srs',0) }}
										</b></button>
									</div>
				                </div>
				                <div v-else>-</div>
				            </td>
			        	</tbody>
		        	</table>
		        </div>
		    </div>
			<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
			<div class="fixed-bottom bg-dark text-white text-center p-3">
			    <div class="d-inline-block me-5" style="font-size: 18px;">
			        Total Students:  
			        <span class="fw-bold" >{{ total_students }}</span>
			    </div>
			    <button type="button" v-on:click="submit_data" id="register_btn" class="btn btn-light fw-bold" style="width: 300px;">
			        Save Details &amp; Confirm Participation
			    </button>
			</div>
		</div>
		<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
		    <div class="modal-dialog">
		        <div class="modal-content">
		            <div class="modal-header">
		                <h5 class="modal-title" id="popup_category"></h5>
		                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		            </div>
		            <div class="modal-body">
			            <h6 class="mb-4 text-end">Min Students: {{ min_students }}, Max Students: {{ max_students }}</h6>

			            <table class="table table-bordered table-sm">
			            	<thead>
			            		<tr>
			            			<th>#</th>
					              	<th>Name</th>
					              	<th>Gender</th>
					              	<th>Class</th>
					              	<th></th>
			            		</tr>
			            	</thead>
			            	<tbody>
			            		<tr v-for="student,key in current_students" :key="key">
			            			<td>{{ key + 1 }}</td>
			            			<td class="p-0"><input type="text" v-model="student.name" class="form-control form-control-sm" /></td>
			            			<td class="p-0" >
					                	<select v-model="student.gender" class="form-select form-select-sm">
					                  		<option value="" disabled>Gender</option>
					                  		<option value="Male">Male</option>
				                  			<option value="Female">Female</option>
					                	</select>
					              	</td>
					              	<td class="p-0" >
						                <select v-model="student.class" class="form-select form-select-sm">
						                  <option value="" disabled>Class</option>
						                  <option v-for="classNum in availableClasses" :key="classNum" :value="classNum">{{ classNum }}</option>
						                </select>
					              	</td>
					              	<td class="p-0 text-center">
						                <button @click="removeRow(key)" type="button" :disabled="current_students.length <= this.min_students" class="btn btn-danger btn-sm">X</button>
						            </td>
			            		</tr>
			            	</tbody>
			            </table>
			            <div class="text-danger">{{ err }}</div>
			            <div class="text-end">
						    <button type="button" @click="addNewStudentRow" class="btn btn-primary btn-sm" :disabled="current_students.length >= this.max_students" style="border-radius: 5px;">
						        +
						    </button>
						</div>
		            </div>
		            <div class="modal-footer">
					    <div class="me-auto">
					        <button type="button" class="btn btn-danger" @click="clear_students">Clear</button>
					    </div>
					    <button type="button" class="btn btn-primary" @click="save_students">Save</button>
					</div>
		        </div>
		    </div>
		</div>
	</div>
	<script>
		var app=Vue.createApp({
			data(){
				return {
					email:'',
					code:'',
					captcha_code:'',
					otp_sent: false,
					msg:'',
					email_otp:'',
					err:'',
					show_captcha: false,
					max_students:0,
					min_students:0,
					selectedType:'<?=$data['type'] ?>',
					config_categories : <?=json_encode( $config_categories ) ?>,
					config_school_types: <?=json_encode($config_school_types) ?>,
					data: <?=json_encode($data) ?>,
					current_students:[],
					newStudent: { name: '', gender: '', class: '' },
					game_level:'',
					game_category:'',
					game_group: 0,
					student_details: [],
					total_students: 0,
					availableClasses: [],
					vmodel: false,
				};
			},
			mounted(){
				this.student_details = <?=$data['selection']?$data['selection']:"{}" ?>;
				console.log(this.student_details)
				this.calculateTotal();
				window.addEventListener('hashchange', this.handleHashChange);

				const modalElement = document.getElementById('studentModal');
			    modalElement.addEventListener('hidden.bs.modal', function () {
			        window.history.pushState("", document.title, window.location.pathname);
			    });
			},
			methods:{
				clear_students() {
				    // Reset the student details for the current game category, level, and group
				    if (this.game_category in this.student_details) {
				        if (this.game_level in this.student_details[this.game_category]) {
				            if (this.game_group in this.student_details[this.game_category][this.game_level]) {
				                this.student_details[this.game_category][this.game_level][this.game_group] = [];
				            }
				        }
				    }

				    // Clear the current students array and reset headings (if applicable)
				    this.current_students = [];

				    // Create empty student rows to match the minimum students requirement
				    for (let i = 0; i < this.min_students; i++) {
				        this.current_students.push({
				            name: '',
				            gender: '',
				            class: ''
				        });
				    }

				    // Calculate the total number of students
				    this.calculateTotal();

        			this.vmodel.hide();
				    
				    // Log the updated student details for debugging
				    console.log(this.student_details);
				},
				goHome() {
			      window.location.href = 'home.php';
			    },
			    logout() {
			    	window.location.href = '?action=logout';
			    },
			    handleHashChange() {
			        if (!window.location.hash) {
			            const modal = bootstrap.Modal.getInstance(document.getElementById('studentModal'));
			            if (modal) {
			                modal.hide();
			            }
			        }
			    },
				openPopup(vkey, vtype, vgroup){
					const hash = `${vtype}-${vkey['sno']}-${vgroup}`;
    				window.location.hash = hash;
					this.current_students = [];
					this.min_students = 0;
					this.max_students = 0;
					this.game_level = vtype;
        			this.game_category = vkey['sno'];
					this.game_group = vgroup;
					this.err = "";
					document.getElementById('popup_category').innerText = vkey['name'];
					this.vmodel = new bootstrap.Modal(document.getElementById('studentModal'));
        			this.vmodel.show();
        			if (vtype == "sub_jrs") {
        				this.min_students = Number(vkey['max'][0][0]);
        				this.max_students = Number(vkey['max'][0][1]);
        				this.availableClasses = [1, 2, 3, 4, 5];
        			}else if (vtype == "jrs") {
        				this.min_students = Number(vkey['max'][1][0]);
        				this.max_students = Number(vkey['max'][1][1]);
        				this.availableClasses = [ 6, 7 ];
        			}else if (vtype == "srs") {
        				this.min_students = Number(vkey['max'][2][0]);
        				this.max_students = Number(vkey['max'][2][1]);
        				this.availableClasses = [8, 9, 10];
        			}
		            this.fetch_students(vkey['sno'],vtype,vgroup);
				},
				fetch_students(vkey, vtype, vgroup){

					if( vkey in this.student_details ){
						if( vtype in this.student_details[ vkey ] ){
							if( vgroup in this.student_details[ vkey ][ vtype ] ){
								this.current_students = this.student_details[vkey][vtype][ vgroup ];
							}
						}
					}
					if (this.current_students.length === 0) {
		                for (let i = 0; i < this.min_students; i++) {
		                    this.current_students.push({
		                        name: '',
		                        gender: '',
		                        class: ''
		                    });
		                }
		            }

				},
				removeRow(key){
				    if (this.current_students.length <= this.min_students) {
				        alert(`At least ${this.min_students} students must remain.`);
				        return;
				    }
				    this.current_students.splice(key, 1);
				    this.calculateTotal();
				},
				save_students(){
					for (const student of this.current_students) {
				        if (!student.name || !student.gender || !student.class) {
				            this.err = "Please fill in all details for each student.";
				            return;
				        }
				    }

					if ( this.game_category in this.student_details == false ) {
				        this.student_details[this.game_category] = {};
				    }

				    if ( this.game_level in this.student_details[this.game_category] == false) {
				        this.student_details[this.game_category][this.game_level] = {};
				    }

				    this.student_details[this.game_category][this.game_level][ this.game_group ] = [];

				    console.log( JSON.stringify( this.student_details[this.game_category], null, 4 ));

				    console.log( this.game_category + ":" + this.game_level + ":" + this.game_group );
					this.student_details[this.game_category][this.game_level][this.game_group].push(...this.current_students);

					//console.log( JSON.stringify( this.student_details[this.game_category], null, 4 ));

					//ssssss

		        	this.calculateTotal();

					if (this.total_students > 60 && this.data['type'] == "school" ){

						alert("Max students should be 60 only!");

						var d = this.total_students - 60;
						this.student_details[this.game_category][this.game_level][this.game_group].splice(0,d);
						this.calculateTotal();
						return;

					}

					this.vmodel.hide();	
				},
				calculateTotal() {
					this.total_students = 0;
				    for (var category_id in this.student_details) {
				        for (var vlevel in this.student_details[category_id]) {
				        	
				        	for (var vgroup in this.student_details[category_id][vlevel]) {
				            	this.total_students += this.student_details[category_id][vlevel][vgroup].length;
				            }

				        }
				    }
				},
				addNewStudentRow() {
				    if (this.current_students.length >= this.max_students) {
				        alert(`Maximum of ${this.max_students} students reached for this category.`);
				        return;
				    }
				    
				    this.current_students.push({
				        name: '',
				        gender: '',
				        class: ''
				    });
				},
				submit_data(){
					if (this.total_students === 0) {
					    alert("Select Atleast One Student");
					    return;
					}

					if( this.data['type'] == 'school' ){
						if (this.total_students > 60 ) {
							alert("You can select maximum 60 Students per school.");
							return;
						}
					}
					var con = new XMLHttpRequest();
					con.open("POST", "?", true );
					con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
					con.onload = () => {
						this.msg = "";
						const response = JSON.parse(con.responseText);
				        if (response.status === "error") {
				            this.err = response.error;
				        } else if (response.status === "success") {
				            document.location = "/home.php";
				        }else{
				        	console.log("error")
				        }
					};
					var vpost = "action=save_nominations&record="+encodeURIComponent(JSON.stringify(this.student_details));
					con.send(vpost);					
				},
				studentCount(category_id, vlevel, vgroup){
					if ( category_id in this.student_details ){
						if( vlevel in this.student_details[ category_id ] ){
							if( vgroup in this.student_details[ category_id ][ vlevel ] ){
								return this.student_details[category_id][ vlevel ][ vgroup ].length;
							}
						}
			        }
			        return 0;
				},
				select_type(type){
					// console.log(type)
					this.selectedType = type;
				},
				dotest: function(){

					// console.log( JSON.stringify(this.student_details,null,4) );
					// return;

					this.student_details = {};

					var cnt = 0;
					for ( var game_category in this.config_categories) {
						for( var vl=0;vl<3;vl++){
				        if ( this.config_categories[ game_category ]['enabled'][vl] ) {
				        	if ( !this.config_categories[ game_category ]['group'] ){
				        		var max = this.config_categories[ game_category ]['max'][vl][1];

				            	if ( game_category in this.student_details == false ) {
							        this.student_details[game_category] = {};
							    }
							    var vlevel = "sub_jrs";
							    if( vl == 1 ){ vlevel = "jrs";}
							    if( vl == 2 ){ vlevel = "srs";}

						        this.student_details[game_category][vlevel] = {};
							    this.student_details[game_category][vlevel][0] = [];

							    this.current_students = [];

			        			for( var i=0;i<max;i++){
			        				this.current_students.push({
								        name: 'something',
								        gender: 'male',
								        class: '1'
								    });
								    cnt++;
			        			}

								this.student_details[game_category][vlevel][0].push(...this.current_students);

								console.log( JSON.stringify(this.student_details,null,4) );

								this.calculateTotal();

								if( cnt > 60 ){ return;}

				            }
				        }
				    	}
				    }
				}
			},
		}).mount("#app");
	</script>
</body>
</html>