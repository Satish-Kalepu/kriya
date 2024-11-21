<?php
	session_start();
	// echo $_SESSION['email'];
	// exit;
	include('../config_global.php');
	
	if( !isset($login_enable)  || !isset($email_cc_list) || !isset($user)  ){
		http_response_code(500);
		echo "configuration pending..";exit;
	}

	include('db.php');
	include('config.php');
	
	include("smtp_ses.php");
	include("actions.php");

	if( !isset($_GET['email']) ){
		header("Location: index.php");exit;
	}

	$is_registered = false;
	$data1 = [];
	$data = [];
	if( !$_SESSION['logged_in'] ){
		header("Location: index.php");exit;
	}else if( $_SESSION['email'] != $_GET['email'] ){
		header("Location: index.php");exit;
	}else{
		$email = mysqli_real_escape_string($connection, $_GET["email"]);
		$school_res = mysqli_query( $connection, "select * from kriya_schools where email = '".$email."'" );
		$data = mysqli_fetch_assoc($school_res);
		if( $data ){
			$student_res = mysqli_query( $connection, "select * from kriya_students where user_id = " . $data["id"]);
			$data1 = [];
			while ($row2 = mysqli_fetch_assoc($student_res)) {
			    $data1[] = $row2; 
			}
			$selection = json_decode($data['selection'],true);
			$is_registered = true;
		}else{
			$data = [];
		}
	}
	// echo "<pre>";
	// print_r($data);
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
				    </div>
				</div>
		        <div style="border: 2px solid white; padding: 20px; border-radius: 10px; background-color: #fff4d9; margin: 10px auto;">
					<div v-if="is_registered==false">
						<h3 class="text-center" style="margin-top: 0;">REGISTRATION</h3>
					</div>
					<div v-else>
						<h3 class="text-center" style="margin-top: 0;">NOMINATIONS</h3>	
					</div>
					<hr>
           			<div v-if="is_registered==false&&registration_type==''">
           				<table class="table table-hover ">
           					<tbody>
           					<tr valign="middle">
           						<td width="50" align="center"><input type="radio" id="free_school" v-model="registration_type" value="free,school" ></td>
           						<td>
           							<label for="free_school" style="cursor:pointer;">
           								<div class="mb-2"><strong>Government School</strong></div>
           								<div class="mb-2">Free Entry. Maximum 60 students per school</div>
           							</label>
           						</td>
           					</tr>
           					<tr valign="middle">
           						<td align="center"><input type="radio" id="free_school2" v-model="registration_type" value="free,school" ></td>
           						<td>
           							<label for="free_school2" style="cursor:pointer;">
           								<div class="mb-2"><strong>Budget Private School</strong></div>
           								<div class="mb-2">Private School's collecting annual tuition fee for 5th class below 25,000/-, for 10th class below 40,000/- and Not more than that.</div>
           								<div>Free Entry. Maximum 60 students per school</div>
           							</label>
           						</td>
           					</tr>
           					<tr valign="middle">
           						<td align="center"><input type="radio" id="paid_school" v-model="registration_type" value="paid,school" ></td>
           						<td>
           							<label for="paid_school" style="cursor:pointer;">
           								<div class="mb-2"><strong>Premium Private School</strong></div>
           								<div class="mb-2">Entry Fee 300/- per participant per competition for private schools collecting annual tuition fee more than 25,000/- for 5th class and more that 40,000/- for 10th Class</div>
           								<div>Paid Entry. Maximum 60 students per school</div>
           							</label>
           						</td>
           					</tr>
           					<tr valign="middle">
           						<td align="center"><input type="radio" id="paid_parent" v-model="registration_type" value="paid,parent" ></td>
           						<td>
									<label for="paid_parent" style="cursor:pointer;">
										<div class="mb-2"><strong>Parent</strong></div>
										<div class="mb-2">Entry Fee 500/- per participant per competition for parents</div>
										<div>Paid Entry.</div>
									</label>
           						</td>
           					</tr>
           					<tr valign="middle">
           						<td align="center"><input type="radio" id="paid_institute" v-model="registration_type" value="paid,institute" ></td>
           						<td>
           							<label for="paid_institute" style="cursor:pointer;">
           								<div class="mb-2"><strong>Institute</strong></div>
           								<div class="mb-2">Entry Fee 500/- per participant per competition for  for Music, Dance, Art Schools and other institutes etc.,</div>
           								<div>Paid Entry.</div>
           							</label>
           						</td>
           					</tr>
           				</tbody>
           				</table>
           			</div>

					<div v-if="registration_type!=''&&'type' in record">
						<div v-if="is_registered==false" ><div class="btn btn-primary btn-sm" v-on:click="registration_type=''" >Change Registration Type</div></div>
						<div v-if="record['type'] == 'school'">
							<div v-if="edit_details">
								<div v-if="is_registered==false">
							 		<div class="mb-3">
									    <div>Entry Type: <span class="fw-bold">{{ record.type }}</span></div>
									</div>
									<div>
										<div>UDISE Code: <span class="text-danger">*</span></div>
								        <input type="number" class="form-control form-control-sm mb-3" placeholder="Enter UDISE Code" v-model="record['school_id']">
								        <div class="text-danger">{{ err }}</div>
								        <button class="btn" v-on:click="search_school" style="background-color: #2c95da; color: white;">Search School</button>
									</div>
								</div>
						        <div v-if="school_found">
							        <div class="d-flex align-items-center mb-2">
							            <div>School Name:</div>
							            <div class="ms-1 fw-bold">{{ record.school_name }}</div>
							            <div class="ms-1 fw-bold">{{ record.village_name }},{{ record.district_name }}</div>
							        </div>
							        <div>Teacher Name: <span class="text-danger">*</span></div>
							        <input type="text" class="form-control form-control-sm mb-3" placeholder="Enter Name" v-model="record['contact_person']">
							        <div class="text-danger">{{ teacher_name_err }}</div>

							        <div>Mobile: <span class="text-danger">*</span></div>
							        <input type="number" class="form-control form-control-sm mb-3" placeholder="Enter Name" v-model="record['phone']">
							        <div class="text-danger">{{ mobile_err }}</div>

							        <div>Mobile 2:</div>
							        <input type="number" class="form-control form-control-sm mb-3" placeholder="Enter Name" v-model="record['phone2']">

							        <div class="text-danger mb-3" v-if="form_err">{{ form_err }}</div> 
						        	<div><button type="button" class="btn" style="background-color: #2c95da; color: white;" v-on:click="submit_data()"><span v-if="is_registered">Update</span><span v-else>Register</span></button></div>
						        </div>
						    </div>
						    <div v-else>
						    	<div>
						    		<div class="mb-3 d-flex" style="column-gap:10px;">
									    <div>Entry Type:</div>
								        <span class="fw-bold">{{ record.entry_type }}</span>
										<button type="button" class="btn btn-link btn-sm" style="float:right;" v-on:click="openchangepopup()" >
										    Change Type
										</button>
									</div>
						    		<div class="mb-3">
									    <div>School:</div>
									    <div class="fw-bold">
									    	<div>{{ record.school_name }}</div>
									    	<div class="fw-bold">{{ record.village_name }}, {{ record.district_name }}</div>
										</div>
									</div>
									<div class="mb-3 d-flex align-items-center">
							            <div class="me-2">Teacher Name:</div>
							            <span class="fw-bold">{{ record.contact_person }}</span>
							        </div>
							        <div class="mb-3">
							            <span class="me-2">Mobile Number: </span>
							            <span class="fw-bold">{{ record.phone }}, {{ record.phone2 }}</span>
							        </div>
							        <div class="mb-3 d-flex align-items-center" v-if="record.entry_type == 'paid'">
									    <div class="me-2">Amount To Be Paid: <span class="fw-bold">
									        {{ (record.amount) }} /- 
									    </span>
										</div>
									</div>
									<div class="mb-3" v-if="record.entry_type == 'paid'">
							            <div class="me-2">Amount should transfer to:</div>
							            <div>Account Name: <strong>Kriya Society</strong></div>
									    <div>Account Number: <strong>3260 2200 0034 44</strong></div>
									    <div>Branch: <strong>Canara Bank, KAKINADA ADITYA ACADEMY</strong></div>
									    <div>IFSC: <strong>CNRB0013260</strong></div>
										<div>Kindly email your transaction details to kriyasociety@gmail.com</div>
										<div>Admin team will validate your payment details and confirm your participation.</div>
							        </div>
							        <div class="mb-3">Note: Entry passes will be emailed a day before. Participants are required to keep at least two copies of entry passes with them</div>
						    	</div>
						        <div class="d-flex justify-content-between mb-3">
						            <button type="button" class="btn" v-on:click="edit_details=true" style="background-color: #2c95da; color: white;">Edit</button>
						            <button type="button" class="btn btn-light" onclick="window.location.href='/nominate.php';" style="background-color: #2c95da; color: white;">Select Students</button>
						        </div>
						    </div>
						</div>
						<div v-if="record['type'] == 'institute'">
							<div v-if="edit_details">
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
						        <div class="text-danger mb-3" v-if="form_err">{{ form_err }}</div> 
						        <div><button type="button" class="btn" style="background-color: #2c95da; color: white;" v-on:click="submit_data()"><span v-if="is_registered">Update</span><span v-else>Register</span></button></div>
						    </div>
						    <div v-else>
						    	<div class="mb-3">
								    <div>Entry Type:</div>
								    <span class="fw-bold">{{ record.entry_type }}</span>
								</div>
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
								<div class="mb-3" v-if="record.entry_type == 'paid'">
								    <div class="me-2">Amount To Be Paid: <span class="fw-bold">
								        {{ record.amount }} /-
								    </span></div>
								    
								</div>
								<div class="mb-3">
						            <div class="me-2">Amount should transfer to:</div>
						            <div>Account Name: <strong>Kriya Society</strong></div>
								    <div>Account Number: <strong>3260 2200 0034 44</strong></div>
								    <div>Branch: <strong>Canara Bank, KAKINADA ADITYA ACADEMY</strong></div>
								    <div>IFSC: <strong>CNRB0013260</strong></div>
						        </div>
						        <div class="d-flex justify-content-between mb-3">
						            <button type="button" class="btn btn-light" v-on:click="edit_details=true" style="background-color: #2c95da; color: white;">Edit</button>
						            <button type="button" class="btn btn-light" onclick="window.location.href='/nominate.php';" style="background-color: #2c95da; color: white;">Select Students</button>
						        </div>
						    </div>
						</div>
						<div v-if="record['type'] == 'parent'">
							<div v-if="edit_details">
						        <div>Parent Name: <span class="text-danger">*</span></div>
						        <input type="text" class="form-control form-control-sm mb-3" v-model="record['contact_person']" placeholder="Enter Parent Name">

						        <div>Mobile: <span class="text-danger">*</span></div>
						        <input type="number" class="form-control form-control-sm mb-3" v-model="record['phone']" placeholder="Enter Mobile Number">

						        <div>Mobile 2:</div>
						        <input type="number" class="form-control form-control-sm mb-3" v-model="record['phone2']">

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

						        <div class="text-danger mb-3" v-if="form_err">{{ form_err }}</div> 
						        <div><button type="button" class="btn" style="background-color: #2c95da; color: white;" v-on:click="submit_data()"><span v-if="is_registered">Update</span><span v-else>Register</span></button></div>
						    </div>
						    <div v-else>
						    	<div class="mb-3 d-flex align-items-center">
						            <div class="me-2">Entry Type:</div>
						            <span class="fw-bold">{{ record.entry_type }}</span>
						        </div>
						        <div class="mb-3 d-flex align-items-center">
						            <div class="me-2">Parent Name:</div>
						            <span class="fw-bold">{{ record.contact_person }}</span>
						        </div>
						        <div class="mb-3 d-flex align-items-center">
						            <div class="me-2">Mobile Number:</div>
						            <span class="fw-bold">{{ record.phone }} , {{ record.phone2 }}</span>
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
						        <div class="mb-3" v-if="record.entry_type == 'paid' && selected_students.length">
								    <div class="me-2">Amount To Be Paid: <span class="fw-bold">
								        {{ record.amount }} /-
								    </span></div>
								    
								</div>
								<div class="mb-3" v-if="selected_students.length">
						            <div class="me-2">Amount should transfer to:</div>
						            <div>Account Name: <strong>Kriya Society</strong></div>
								    <div>Account Number: <strong>3260 2200 0034 44</strong></div>
								    <div>Branch: <strong>Canara Bank, KAKINADA ADITYA ACADEMY</strong></div>
								    <div>IFSC: <strong>CNRB0013260</strong></div>
						        </div>
						        <div class="d-flex justify-content-between mb-3">
						            <button type="button" class="btn btn-light" v-on:click="edit_details=true" style="background-color: #2c95da; color: white;">Edit</button>
						            <button type="button" class="btn btn-light" onclick="window.location.href='/nominate.php';" style="background-color: #2c95da; color: white;">Select Students</button>
						        </div>
						    </div>
						</div>

					</div>
					
		        </div>
		    </div>
		</div>
		<template v-if="is_registered" >
		<div class="fs-1 text-center text-white">Selected Students</div>
		<div v-if="selected_students.length" class="mb-4 p-2 w-100 overflow-auto">
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
		<div v-else class="fs-4 text-center text-dark">You have not selected any students yet</div>
		</template>

	<div class="modal fade" id="changeTypeModal" tabindex="-1" aria-labelledby="changeTypeModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="changeTypeModalLabel">Change Entry Type</h5>
	                <button type="button" id="close_modal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	            </div>
	            <div class="modal-body">
	                <div>
	                    <div class="mb-3">
	                        <label class="fs-5"><input type="radio" name="change_type" v-on:change="selectType('govt_school')" value="govt_school"> Govt School</label>
	                        <p class="text-muted mt-2">
	                            Entry Free for Govt. Schools.
	                        </p>
	                    </div>
	                    <div class="mb-3">
	                        <label class="fs-5"><input type="radio" name="change_type" v-on:change="selectType('free_private_school')" value="free_private_school"> Private School (No Entry Fee)</label>
	                        <p class="text-muted mt-2">
	                            Entry Free for Budget Private Schools collecting annual tuition fee for 5th class below 25,000/-, for 10th class below 40,000/- and Not more than that.
	                        </p>
	                    </div>
	                    <div class="mb-3">
	                        <label class="fs-5"><input type="radio" name="change_type" v-on:change="selectType('private_school_paid')" value="private_school_paid"> Private School (With Entry Fee)</label>
	                        <p class="text-muted mt-2">
	                            Entry Fee 300/- per participant per competition for premium private schools collecting annual tuition fee more than 25,000/- for 5th class and more that 40,000/- for 10th Class
	                        </p>
	                    </div>
	                </div>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
	                <button type="button" class="btn btn-primary" v-on:click="saveSelectedType">Save</button>
	            </div>
	        </div>
	    </div>
	</div>

	</div>
	<script>
		var app=Vue.createApp({
			data(){
				return {
					is_registered: <?=$is_registered?"true":"false" ?>,
					email:'',
					code:'',
					captcha_code:'',
					otp_sent: false,
					registration_type: "",
					msg:'',
					email_otp:'',
					err:'',
					show_captcha: false,
					school_found: <?=$is_registered?"true":"false" ?>,
					teacher_name_err:'',
					mobile_err:'',
					form_err:'',
					institute_err:'',
            		selectedType: '<?=$data['entry_type'] ?>',
					record: <?=json_encode($data) ?>,
					selected_students: <?=json_encode($data1) ?>,
					config_categories: <?= json_encode($config_categories) ?>,
					edit_details: <?=$is_registered?"false":"true" ?>,
					states: <?= json_encode($state_data) ?>,
				    districts: [],
				    radio_btn: false,
				    entry_type:'',
				    proceedClicked: false,
				    details_inserted: true,
				    vpop: false,
				};
			},
			watch: {
				registration_type: function(){
					this.change_type();
				}
			},
			mounted(){
		        if( 'type' in this.record == false ){
		        	this.record = {
						"type": "",
						"entry_type": "",
						"school_id": "",
						"school_name": "",
						"school_category": "",
						"village_name": "",
						"mandal_name": "",
						"district_name": "",
						"state_name": "",
						"institute": "",
						"contact_person": "",
						"phone": "",
						"phone2": "",
						"email": "",
						"total_students": "",
						"total_teachers": "",
						"boys": "",
						"girls": "",
						"accommodation": "",
						"reg_date": "",
						"recent_date": "",
						"ip": "",
						"selection": "",
		        	};
		        }else{
		        	this.registration_type = this.record['entry_type']+','+this.record['type'];
		        }
			},
			methods:{
				openchangepopup: function(){
					this.vpop =  new bootstrap.Modal('#changeTypeModal');
					this.vpop.show();
				},
				change_type: function(){
					var x = this.registration_type.split(',');
					this.record['type'] = x[1];
					this.record['entry_type'] = x[0];
				},
				updateDistricts(s_index) {
					let selected_state = this.record['state_name'];
					this.districts = [];
			      	this.districts = this.states[s_index]['districts'];
			      	this.record.district_name = '';
			    },

		        selectType(type) {
		            this.selectedType = type;
		            if (type === 'govt_school') {
			            this.record['entry_type'] = 'free';
			        } else if (type === 'free_private_school') {
			            this.record['entry_type'] = 'free';
			        } else if (type === 'private_school_paid') {
			            this.record['entry_type'] = 'paid';
			        }
		        },

		        saveSelectedType() {
		            if (this.selectedType) {
						this.vpop.hide();	
		                this.insert_data();
		            }
		        },
				submit_data(){
					if (this.record['type'] === "school") {
						this.teacher_name_err = "";
						this.form_err = "";
						if (this.record['school_id'] == "") {
							this.school_found = false;
							this.form_err = "Select School!";return;
						}else if (this.record['contact_person'].match(/^[a-z0-9\ \.]{3,100}$/i) == null ) {
							this.form_err = "Enter Teacher Name";
							return;
						}else if (this.record['phone'].toString().match(/^[0-9]{10}$/i) == null ) {
							this.form_err = "Please enter Phone. should be 10 digits";
							return;
						}else if (this.record['phone2'].toString()!="" ) {
							if (this.record['phone2'].toString().match(/^[0-9]{10}$/i) == null ) {
								this.form_err = "Phone 2 incorrect. should be 10 digits";
								return;
							}
						}
					}else if (this.record['type'] === "parent") {
						this.form_err = "";
						if (this.record['contact_person'].match(/^[a-z0-9\ \.]{3,100}$/i) == null ) {
							this.form_err = "Please Enter Parent Name";
							return;
						}
						if (this.record['phone'].toString().match(/^[0-9]{10}$/i) == null ) {
							this.form_err = "Please enter Phone. should be 10 digits";
							return;
						}
						console.log( this.record['phone2'].toString() );
						if (this.record['phone2'].toString()!="" ) {
							if (this.record['phone2'].toString().match(/^[0-9]{10}$/i) == null ) {
								this.form_err = "Phone 2 incorrect. should be 10 digits";
								return;
							}
						}
						if (this.record['state_name'] === "") {
							this.form_err = "Select State";
							return;
						}
						if (this.record['district_name'] === "") {
							this.form_err = "Select District";
							return;
						}
						if (this.record['village_name'].match(/^[a-z0-9\ \.]{3,100}$/i) == null ) {
							this.form_err = "Enter Village Name";
							return;
						}
						
					}else if (this.record['type'] === "institute") {
						this.form_err = "";
						if (this.record['institute'].match(/^[a-z0-9\-\ \.]{3,100}$/i) == null ) {
							this.form_err = "Please Enter Institute Name";
							return;
						}
						if (this.record['phone'].toString().match(/^[0-9]{10}$/i) == null ) {
							this.mobile_err = "Please enter Phone. should be 10 digits";
							return;
						}
						if (this.record['phone2'].toString()!="" ) {
							if (this.record['phone2'].match(/^[0-9]{10}$/i) == null ) {
								this.form_err = "Phone 2 incorrect. should be 10 digits";
								return;
							}
						}
						if (this.record['contact_person'].match(/^[a-z0-9\ \.]{3,100}$/i) == null ) {
							this.form_err = "Enter Contact Person Name";
							return;
						}
						if (this.record['state_name'] === "") {
							this.form_err = "Select State";
							return;
						}
						if (this.record['district_name'] === "") {
							this.form_err = "Select District";
							return;
						}
						if (this.record['village_name'].match(/^[a-z0-9\ \.]{3,100}$/i) == null) {
							this.form_err = "Enter Village Name";
							return;
						}
					}
					this.proceedClicked = true;
					this.edit_div_show = !this.edit_div_show;
					this.err = "";
					this.teacher_name_err = "";
					this.mobile_err = "";
					this.radio_btn = true;
					this.insert_data();
				},
				logout() {
			    	window.location.href = '?action=logout';
			    },
				search_school(){
					if (this.record['school_id'] == "") {
						this.school_found = false;
						this.err = "Enter UDISE Code!";return;
					}
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
				            this.record['school_category'] = '';
				            this.err = response.error;
				        } else if (response.status === "success") {
				            this.school_details = response.data;
				            this.record['school_name'] = response.data['school_name'];
				            this.record['village_name'] = response.data['village_name'];
				            this.record['district_name'] = response.data['district_name'];
				            this.record['state_name'] = response.data['state_name'];
				            this.record['school_category'] = response.data['school_category'];
				            this.school_found = true;
				            this.err = "";
				        }
					};
					con.send( vdata );
				},
				insert_data(){

					var con = new XMLHttpRequest();
					con.open("POST", "", true );
					con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
					con.onload = () => {
						this.msg = "";
						const response = JSON.parse(con.responseText);
				        if (response.status === "success") {
				        	document.location.reload();
				        }else{
				            this.form_err = response.error;
				        }
					};
					if( this.is_registered ){
						var vpost = "action=update_record&data="+encodeURIComponent(JSON.stringify(this.record));
					}else{
						var vpost = "action=insert_record&data="+encodeURIComponent(JSON.stringify(this.record));
					}
					con.send(vpost);
				},
			},
		}).mount("#app");
	</script>
</body>
</html>