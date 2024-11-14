<?php
	session_start();

	include('../config_global.php');
	
	if( !isset($login_enable)  || !isset($email_cc_list) || !isset($user)  ){
		http_response_code(500);
		echo "configuration pending..";exit;
	}

	include('db.php');
	include('config.php');
	
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
			header("Location: /?event=UserNotFound");
			exit;
		}
		$student_res = mysqli_query( $connection, "select * from kriya_students where user_id =" . $_SESSION["user_id"]);
		$data1 = [];
		while ($row = mysqli_fetch_assoc($student_res)) {
		    $data1[] = $row; 
		}
		$selection = json_decode($data['selection'],true);
	}
	// echo "<pre>";
	// print_r(json_encode($config_categories));
	// echo "</pre>";
	// exit;
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
	<style>
		body {
			background-color: #2c95da;
		}
	</style>
</head>
<body>
	<div id="app">
		<div class="text-center">
		    <img src="/kriya.jpg" class="img-fluid" alt="Logo">
		</div>
		<!-- home landing div -->
		<div class="mb-1">
		    <div class="container" style="max-width: 600px;">
			    <div style="text-align: right;">
				    <div>
				    	<button class="btn btn-light text-dark" v-on:click="logout">Logout</button>
				        <!-- <a href="?action=logout" class="btn btn-link text-white">Logout</a> -->
				    </div>
				</div>
		        <div style="border: 2px solid white; padding: 20px; border-radius: 10px; background-color: #fff4d9; margin: 10px auto;">
					<h3 class="text-center" style="margin-top: 0;">Online Registration</h3>
					<hr>
		           	<div class="mb-3">
		           		<?php if ($data['contact_person'] == "") { ?>
		           		<!-- <div v-if="!record['contact_person']"> -->
							  <div class="d-inline me-3">
							    <label><input type="radio" name="registration_type" v-model="record['type']" value="school" > School</label>
							  </div>

							  <div class="d-inline me-3">
							    <label><input type="radio" name="registration_type" v-model="record['type']" value="parent" > Parent</label>
							  </div>

							  <div class="d-inline"> 
							    <label><input type="radio" name="registration_type" v-model="record['type']" value="institute" > Institute</label>
							  </div>
						<!-- </div> -->
		           		<?php } ?>
					</div>

					<div v-if="record['type'] != ''">
						<div v-if="record['type'] == 'school'">
							<div v-if="edit_div_show">

								<div>Search School: <span class="text-danger">*</span></div>
						        <input type="number" class="form-control form-control-sm mb-3" placeholder="Enter UIDC Code" v-model="record['school_id']">
						        <div class="text-danger">{{ err }}</div>

						        <button type="button" class="btn btn-primary" v-on:click="search_school">Search School</button>

						        <div v-if="school_found || (record['school_name'] !== '')">
							        <div class="d-flex align-items-center mb-2">
							            <div>School Name:</div>
							            <div class="ms-1 fw-bold">{{ record.school_name }}</div>
							        </div>
							        <div class="d-flex align-items-center mb-2">
							            <div>School Address:</div>
							            <div class="ms-1 fw-bold">
							                {{ record.village_name }},{{ record.district_name }}
							            </div>
							        </div>
							        <div>Teacher Name: <span class="text-danger">*</span></div>
							        <input type="text" class="form-control form-control-sm mb-3" placeholder="Enter Name" v-model="record['contact_person']">
							        <div class="text-danger">{{ teacher_name_err }}</div>

							        <div>Mobile: <span class="text-danger">*</span></div>
							        <input type="number" class="form-control form-control-sm mb-3" placeholder="Enter Name" v-model="record['phone']">
							        <div class="text-danger">{{ mobile_err }}</div>

							        <div>Mobile 2:</div>
							        <input type="number" class="form-control form-control-sm mb-3" placeholder="Enter Name" v-model="record['phone2']">

						        	<button type="button" class="btn btn-primary" v-on:click="submit_data">Save Changes</button>
						        </div>
						    </div>
						    <div v-else>
						    	<!-- <table class="table table-bordered table-striped">
						    		<tr>
						    			<td>School Name:</td>
						    			<td class="fw-bold">{{ record.school_name }}</td>
						    		</tr>
						    		<tr>
						    			<td>School Address:</td>
						    			<td class="fw-bold">{{ record.village_name }},{{ record.district_name }}</td>
						    		</tr>
						    		<tr>
						    			<td>Contact Person:</td>
						    			<td class="fw-bold">{{ record.contact_person }}</td>
						    		</tr>
						    		<tr>
						    			<td>Mobile:</td>
						    			<td class="fw-bold">{{ record.phone }}</td>
						    		</tr>
						    		<tr>
						    			<td>Mobile 2:</td>
						    			<td class="fw-bold">{{ record.phone2 }}</td>
						    		</tr>
						    	</table> -->
						    	<div>
						    		<div class="mb-3">
									    <div>School Name:</div>
									    <span class="fw-bold">{{ record.school_name }}</span>
									</div>
									<div class="mb-3">
									    <div>School Address:</div>
									    <span class="fw-bold">{{ record.village_name }},{{ record.district_name }}</span>
									</div>
									<div class="mb-3 d-flex align-items-center">
							            <div class="me-2">Teacher Name:</div>
							            <span class="fw-bold">{{ record.contact_person }}</span>
							        </div>
							        <div class="mb-3">
							            <div class="me-2">Mobile Number:</div>
							            <span class="fw-bold">{{ record.phone }} , {{ record.phone2 }}</span>
							        </div>
						    	</div>
						        <div class="d-flex justify-content-between mb-3">
						            <button type="button" class="btn" v-on:click="edit_div" style="background-color: #2c95da; color: white;">Edit</button>
						            <button type="button" class="btn btn-light" onclick="window.location.href='/nominate.php';" style="background-color: #2c95da; color: white;">Select Students</button>
						        </div>
						    </div>
						</div>
						<div v-if="record['type'] == 'parent'">
						    <div v-if="edit_div_show">
						        <div>Parent Name: <span class="text-danger">*</span></div>
						        <input type="text" class="form-control form-control-sm mb-3" v-model="record['contact_person']" placeholder="Enter Parent Name">

						        <div>Mobile: <span class="text-danger">*</span></div>
						        <input type="number" class="form-control form-control-sm mb-3" v-model="record['phone']" placeholder="Enter Mobile Number">

						        <div>Mobile 2:</div>
						        <input type="number" class="form-control form-control-sm mb-3" v-model="record['phone2']">

						        <div>School Name: <span class="text-danger">*</span></div>
						        <input type="text" class="form-control form-control-sm mb-3" v-model="record['school_name']" placeholder="Enter School Name">

						        <div>State: <span class="text-danger">*</span></div>
							    <select v-model="record['state_name']"  v-on:change="updateDistricts($event.target.selectedIndex - 1)" class="form-select mb-3">
							        <option value="">Select State</option>
							        <option v-for="(state, s_index) in states" :key="s_index" :value="state.name">{{ state.name }}</option>
							    </select>

							    <div>District: <span class="text-danger">*</span></div>
							    <select v-model="record.district_name" class="form-select mb-3" :disabled="!districts">
							        <option value="">Select District</option>
							        <option v-for="(district, index) in districts" :key="index" :value="district">{{ district }}</option>
							    </select>

						        <div>Village/City: <span class="text-danger">*</span></div>
						        <input type="text" class="form-control mb-3" v-model="record['village_name']" placeholder="Enter Village/City">

						        <button type="button" class="btn btn-primary" v-on:click="submit_data">Save Changes</button>
						    </div>
						    <div v-else>
						        <div class="mb-3 d-flex align-items-center">
						            <div class="me-2">Parent Name:</div>
						            <span class="fw-bold">{{ record.contact_person }}</span>
						        </div>
						        <div class="mb-3 d-flex align-items-center">
						            <div class="me-2">Mobile Number:</div>
						            <span class="fw-bold">{{ record.phone }} , {{ record.phone2 }}</span>
						        </div>
						        <div class="mb-3 d-flex align-items-center">
						            <div class="me-2">School Name:</div>
						            <span class="fw-bold">{{ record.school_name }}</span>
						        </div>
						        <div class="mb-3 d-flex align-items-center">
						            <div class="me-2">Village/City Name:</div>
						            <span class="fw-bold">{{ record.village_name }}</span>
						        </div>
						        <div class="mb-3 d-flex align-items-center">
						            <div class="me-2">District Name:</div>
						            <span class="fw-bold">{{ record.district_name }}</span>
						        </div>
						        <div class="mb-3 d-flex align-items-center">
						            <div class="me-2">State Name:</div>
						            <span class="fw-bold">{{ record.state_name }}</span>
						        </div>
						        <div class="d-flex justify-content-between mb-3">
						            <button type="button" class="btn btn-light" v-on:click="edit_div" style="background-color: #2c95da; color: white;">Edit</button>
						            <button type="button" class="btn btn-light" onclick="window.location.href='/nominate.php';" style="background-color: #2c95da; color: white;">Select Students</button>
						        </div>
						    </div>

						    <div class="text-danger mb-3">{{ parent_err }}</div>
						</div>
					</div>
					<div v-if="record['type'] == 'institute'">
					    <div v-if="edit_div_show">
					        <div>Institute Name: <span class="text-danger">*</span></div>
					        <input type="text" class="form-control mb-3" v-model="record['institute']" placeholder="Enter Institute Name">

					        <div>State: <span class="text-danger">*</span></div>
						    <select v-model="record['state_name']"  v-on:change="updateDistricts($event.target.selectedIndex - 1)" class="form-select mb-3">
						        <option value="">Select State</option>
						        <option v-for="(state, s_index) in states" :key="s_index" :value="state.name">{{ state.name }}</option>
						    </select>

						    <div>District: <span class="text-danger">*</span></div>
						    <select v-model="record.district_name" class="form-select mb-3" :disabled="!districts">
						        <option value="">Select District</option>
						        <option v-for="(district, index) in districts" :key="index" :value="district">{{ district }}</option>
						    </select>

					        <div>Village/City: <span class="text-danger">*</span></div>
					        <input type="text" class="form-control mb-3" v-model="record['village_name']" placeholder="Enter Village/City">

					        <div>Contact Person: <span class="text-danger">*</span></div>
					        <input type="text" class="form-control mb-3" v-model="record['contact_person']" placeholder="Contact Person Name">

					        <div>Mobile: <span class="text-danger">*</span></div>
					        <input type="number" class="form-control mb-3" v-model="record['phone']" placeholder="Enter Mobile Number">
					        
					        <div>Mobile 2: <span class="text-danger">*</span></div>
					        <input type="number" class="form-control mb-3" v-model="record['phone2']" placeholder="Enter Mobile Number 2">

					        <div class="text-danger mb-3">{{ institute_err }}</div>

					        <button type="button" class="btn btn-primary" v-on:click="submit_data">Save Changes</button>
					    </div>
					    <div v-else>
					        <div class="mb-3">
							    <div>Institute Name:</div>
							    <span class="fw-bold">{{ record.institute }}</span>
							</div>
							<div class="mb-3">
							    <div>Village/City Name:</div>
							    <span class="fw-bold">{{ record.village_name }}</span>
							</div>
							<div class="mb-3">
							    <div>District Name:</div>
							    <span class="fw-bold">{{ record.district_name }}</span>
							</div>
							<div class="mb-3">
							    <div>State Name:</div>
							    <span class="fw-bold">{{ record.state_name }}</span>
							</div>
							<div class="mb-3">
							    <div>Contact Person:</div>
							    <span class="fw-bold">{{ record.contact_person }}</span>
							</div>
							<div class="mb-3">
							    <div>Mobile Number:</div>
							    <span class="fw-bold">{{ record.phone }} , {{ record.phone2 }}</span>
							</div>
					        <div class="d-flex justify-content-between mb-3">
					            <button type="button" class="btn btn-light" v-on:click="edit_div" style="background-color: #2c95da; color: white;">Edit</button>
					            <button type="button" class="btn btn-light" onclick="window.location.href='/nominate.php';" style="background-color: #2c95da; color: white;">Select Students</button>
					        </div>
					    </div>
					</div>
		        </div>
		    </div>
		</div>
	<?php if ($data1) { ?>
		<div class="fs-1 text-center text-white">Selected Students</div>
		<div class="mb-4 p-2 w-100 overflow-auto">
		    <table class="table table-bordered table-hover">
				<thead style="background-color: #3B5998; color: white;">
					<tr>
						<th>#</th>
						<th>Category</th>
						<th>Name</th>
						<th>Gender</th>
						<th>Class</th>
					</tr>
				</thead>
				<tbody style="background-color: #fff4d9;">
					<tr v-for="student, s in selected_students" :key="s">
						<td nowrap>{{ s + 1 }}</td>
						<td nowrap>
							<span v-if="student.item_id && config_categories[student.item_id]">
	                            {{ config_categories[student.item_id].name }} [ {{student.category }} ]
	                        </span>
						</td>
						<td nowrap>{{ student.name }}</td>
						<td nowrap>{{ student.gender }}</td>
						<td nowrap>{{ student.class }}</td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php } ?>
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
					school_found: false,
					teacher_name_err:'',
					mobile_err:'',
					parent_err:'',
					institute_err:'',
					record: <?=json_encode($data) ?>,
					selected_students: <?=json_encode($data1) ?>,
					config_categories: <?= json_encode($config_categories) ?>,
					current_type: "",
					edit_div_show: true,
					states: <?= json_encode($state_data) ?>,
				    districts: [],
				};
			},
			mounted(){
		        this.current_type = this.record['type']+'';
		        <?php if ($data['type'] != ""){  ?>
		        	this.edit_div_show = false;
		    	<?php } ?>
			},
			methods:{
				updateDistricts(s_index) {
					let selected_state = this.record['state_name'];
					this.districts = [];
			      	this.districts = this.states[s_index]['districts'];
			      	this.record.district_name = '';
			    },
				submit_data(){
					if (this.record['type'] === "school") {
						if (this.record['school_id'] === "") {
							this.school_found = false;
							this.err = "Select School!";return;
						}else if (this.record['contact_person'] === "") {
							this.teacher_name_err = "Enter Teacher Name";
							return;
						}else if (this.record['phone'] === "") {
							this.mobile_err = "Please Enter Mobile";
							return;
						}
					}else if (this.record['type'] === "parent") {
						this.parent_err = "";
						if (this.record['contact_person'] === "") {
							this.parent_err = "Please Enter Parent Name";
							return;
						}else if (this.record['phone'] === "") {
							this.parent_err = "Enter Mobile Number";
							return;
						}else if (this.record['school_name'] === "") {
							this.parent_err = "Enter School Name";
							return;
						}
					}else if (this.record['type'] === "institute") {
						this.institute_err = "";
						if (this.record['institute'] === "") {
							this.institute_err = "Please Enter Institute Name";
							return;
						}else if (this.record['phone'] === "") {
							this.institute_err = "Enter Mobile Number";
							return;
						}else if (this.record['contact_person'] === "") {
							this.institute_err = "Enter Contact Person Name";
							return;
						}
					}
					this.edit_div_show = !this.edit_div_show;
					this.err = "";
					this.teacher_name_err = "";
					this.mobile_err = "";
					this.insert_data();
				},
				edit_div(){
					this.edit_div_show = !this.edit_div_show;
				},
				logout() {
			    	window.location.href = '?action=logout';
			    },
				search_school(){
					this.msg = "Searching...";
					this.school_details = "";
					this.school_found = false;
					this.err = "";
					vdata = "code="+encodeURIComponent(this.record['school_id']);
					vdata = vdata + "&action=search_school";
					var con = new XMLHttpRequest();
					con.open("POST", "?", true );
					con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
					con.onload = () => {
						this.msg = "";
						const response = JSON.parse(con.responseText);
				        if (response.status === "fail") {
				        	this.school_details = "";
				        	this.record['school_name'] = '';
				            this.record['village_name'] = '';
				            this.record['district_name'] = '';
				            this.record['state_name'] = '';
				            this.err = response.error;
				        } else if (response.status === "success") {
				            this.school_details = response.data;
				            this.record['school_name'] = response.data['school_name'];
				            this.record['village_name'] = response.data['village_name'];
				            this.record['district_name'] = response.data['district_name'];
				            this.record['state_name'] = response.data['state_name'];
				            this.school_found = true;
				            this.err = "";
				        }
					};
					con.send( vdata );
				},
				insert_data(){
					// console.log(data)

					var con = new XMLHttpRequest();
					con.open("POST", "?", true );
					con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
					con.onload = () => {
						this.msg = "";
						const response = JSON.parse(con.responseText);
				        if (response.status === "error") {
				            this.err = response.error;
				        } else if (response.status === "success") {
				        	this.school_found = true;
				            // location.reload();
				        }
					};
					var vpost = "action=update_record&record="+encodeURIComponent(JSON.stringify(this.record));
					con.send(vpost);
				},
			},
		}).mount("#app");
	</script>
</body>
</html>