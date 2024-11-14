<?php

include('../config_global.php');
include('config.php');
include('db.php');

// echo "<pre>";
// print_r( $config_categories );
// echo "</pre>";

$school_res = mysqli_query( $connection, "select * from kriya_schools where id = 2");
$data = mysqli_fetch_assoc( $school_res );

$school_res1 = mysqli_query( $connection, "select * from kriya_school_list where school_id = 28145000727");
$data1 = mysqli_fetch_assoc( $school_res1 );

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


?>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="../bootstrap/bootstrap.min.css">
<body style="background-color: #2c95da;">
<div id="app">
	<div class="logo" align="center">
		<img src="/kriya-head1.jpg" style=" max-width:100%;" >
	</div>
	<form method="post" @submit.prevent="validate_form()">
		<div style="padding: 10px;">
		    <div id="registration_form_div" class="border border-white rounded p-3" style="max-width: 100%; background-color: #ffef96;">
		        <div class="text-end" style="padding-right: 50px;">
		            <a href='?action=logout'>Logout</a><br><br>
		        </div>
		        <div class="row mb-3">
		            <div class="col-md-5 text-end">School:</div>
		            <div class="col-md-7">
		                <div class="ms-2" id="school_name">{{ data1['school_name'] }}</div>
		                <div class="ms-2" id="school_category">{{ data1['school_category'] }}</div>
		                <div class="ms-2" id="school_contact">{{ data['email'] }}, {{ data['phone'] }}</div>
		            </div>
		        </div>
		        
		        <div class="row mb-3">
		            <div class="col-md-5 text-end">Teacher Name:</div>
		            <div class="col-md-7">
		                <input type="text" name="teacher_name" v-model="data.contact_person" value="{{ data['contact_person'] }}" id='teacher_id' required autocomplete="off" class="form-control" style="width: 200px;">
		            </div>
		        </div>

		        <div class="row mb-3">
		            <div class="col-md-5 text-end">Phone 2:</div>
		            <div class="col-md-7">
		                <input type='number' class="form-control" v-model="data.phone2" name='phone2' id='pho2' style="width: 200px;" autocomplete="off">
		            </div>
		        </div>

		        <div class="row mb-3" v-if="data1.district_code != 2814">
		            <div class="col-md-4 text-end">Need Accommodation?</div>
		            <div class="col-md-8">
		                <input type='checkbox' name='accommodation' id='accommodation_id' style='width: 15px; height: 15px;' value="y">
		            </div>
		        </div>

		        <div class="row mb-3" v-if="data1.district_code != 2814">
		            <div class="col-md-12 text-center" style='font-size: 10px; color: gray;'>
		                Above details required for food preparation.
		            </div>
		        </div>
		        
		        <input type="hidden" name="total_students" id="total_students_id" v-model="data.total_students">
		    </div>
		</div>
		<div class="text-center mb-4">
		    <p class="text-white fw-bold">Please check all the conditions and age groups before submitting</p>
		    <p class="text-white fw-bold">Last date for submission and corrections is 18th November 2023</p>
		    <p class="text-white fw-bold">Maximum 60 members are allowed from a school</p>
		</div>
		<table class="table table-bordered table-hover w-100" style="border-collapse: collapse; margin: 0 10px;  max-width: calc(100% - 20px);">
		    <thead>
		        <tr class="text-white fw-bold" style="background-color: #3B5998;">
		            <th class="text-center" rowspan="2">ID</th>
		            <th class="text-center" rowspan="2">Category</th>
		            <th colspan="3" class="text-center">No of Students</th>
		            <th class="text-center" rowspan="2">Rules</th>
		        </tr>
		        <tr class="text-white fw-bold" style="background-color: #3B5998;">
		            <th class="text-center">Sub Junior</th>
		            <th class="text-center">Junior</th>
		            <th class="text-center">Senior</th>
		        </tr>
		    </thead>
		    <tbody>
		        <tr v-for="(value, key) in categories" :key="key" style="background-color: #ffef96;">
		            <td class="text-center">{{ value.sno }}</td>
		            <td class="text-left">{{ value.name }}</td>
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
		<div class="fixed-bottom bg-dark text-white text-center p-3">
		    <div class="d-inline-block me-5" style="font-size: 18px;">
		        Total Students:  
		        <span class="fw-bold" id="total_students_div">{{ data.total_students }}</span>
		    </div>
		    <button type="submit" id="register_btn" class="btn btn-light fw-bold" style="width: 300px;">
		        Save Details &amp; Confirm Participation
		    </button>
		</div>
	</form>
	<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
	<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="popup_category"></h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
	        <div class="row">
	          <div class="col-6">
	            <h4 id="popup_category"></h4>
	          </div>
	          <div class="col-6 text-end">
	            <h6 id="max_students"></h6>
	          </div>
	        </div>
	        <p id="student_category" style="display: none;"></p>
	        <p id="student_sno" style="display: none;"></p>
	        
	        <table class="table table-bordered mt-3">
	          <thead class="table-light">
	            <tr>
	              <th>#</th>
	              <th>Name</th>
	              <th>Age</th>
	              <th>Gender</th>
	              <th>Class</th>
	              <th></th>
	            </tr>
	          </thead>
	          <tbody>
	            <tr v-for="student, key in current_students" :key="key">
	              <td>{{ key + 1 }}</td>
	              <td class="p-0"><input type="text" v-model="student.name" class="form-control" /></td>
	              <td class="p-0" style="width: 60px;"><input type="number" v-model="student.age" class="form-control"/></td>
	              <td class="p-0" style="width: 110px;">
	                <select v-model="student.gender" class="form-select">
	                  <option value="" disabled>Gender</option>
	                  <option value="Male">Male</option>
	                  <option value="Female">Female</option>
	                </select>
	              </td>
	              <td class="p-0" style="width: 110px;">
	                <select v-model="student.class" class="form-select">
	                  <option value="" disabled>Class</option>
	                  <option v-for="classNum in [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]" :key="classNum" :value="classNum">{{ classNum }}</option>
	                </select>
	              </td>
	              <td class="p-0 text-center">
	                <button @click="removeRow(key)" type="button" :disabled="current_students.length <= getMinStudents()" class="btn btn-danger btn-sm">X</button>
	              </td>
	            </tr>
	          </tbody>
	        </table>
	        <div class="text-end">
			    <button type="button" @click="addNewStudentRow" class="btn btn-primary btn-sm" :disabled="current_students.length >= getMaxStudents()" style="border-radius: 5px;">
			        +
			    </button>
			</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
	        <button type="button" @click="saveStudents" class="btn btn-primary">Save</button>
	      </div>
	    </div>
	  </div>
	</div>
</div>
<script>
	var app = Vue.createApp({
		data(){
			return{
				categories: <?=json_encode($config_categories) ?>,
				config_school_types: <?=json_encode($config_school_types) ?>,
				data1: <?=json_encode($data1) ?>,
				data: <?=json_encode($data) ?>,
				student_details: {},
				newStudent: { name: '', age: '', gender: '', class: '', category: '', sno: '' },
				current_sno:'',
				current_category:'',
				current_students:[],
			};
		},
		mounted(){
		},
		methods: {
			openPopup(key, type){
				this.current_students = []; // Reset current students
		        this.current_sno = key['sno'];
		        this.current_category = type;

		        document.getElementById('student_category').innerText = type;
		        document.getElementById('student_sno').innerText = key['sno'];
		        document.getElementById('popup_category').innerText = key['name'];

		        // Show the modal
		        const modal = new bootstrap.Modal(document.getElementById('studentModal'));
		        modal.show();

		        // Fetch students for the selected category (if needed)
		        this.fetch_students(key);
			},
			addNewStudentRow() {
			    const maxStudents = this.getMaxStudents(); // Function to get the max allowed students for the current category
			    if (this.current_students.length >= maxStudents) {
			        alert(`Maximum of ${maxStudents} students reached for this category.`);
			        return; // Exit the function if the limit is reached
			    }
			    
			    this.current_students.push({
			        name: '',
			        age: '',
			        gender: '',
			        class: ''
			    });
			},
			getMaxStudents() {
			    const typeIndexMap = {
			        sub_jrs: 0,
			        jrs: 1,
			        srs: 2
			    };
			    const categoryIndex = typeIndexMap[this.current_category];
			    
			    if (categoryIndex !== undefined && this.categories[this.current_sno]) {
			        return this.categories[this.current_sno]['max'][categoryIndex][1]; // Get the max limit
			    }
			    
			    return Infinity; // If no limit found, allow infinite (default)
			},
		    studentCount(sno, category) {
		        if (this.student_details[sno] && this.student_details[sno][category]) {
		            return this.student_details[sno][category].length;
		        }
		        return 0;
		    },
			fetch_students(key) {
			    this.current_students = [];

			    if (this.student_details[this.current_sno] && this.student_details[this.current_sno][this.current_category]) {
			        this.current_students = this.student_details[this.current_sno][this.current_category];
			        console.log(this.current_students)
			    } else {
			        const typeIndexMap = {
			            sub_jrs: 0,
			            jrs: 1,
			            srs: 2
			        };
			        const index = typeIndexMap[this.current_category];

			        if (index !== undefined) {
			            const minStudents = key['max'][index][0];
			            const maxStudents = key['max'][index][1];

			            document.getElementById('max_students').innerText = `Students:- Min: ${minStudents}, Max: ${maxStudents}`;

			            if (this.current_students.length === 0) {
			                for (let i = 0; i < minStudents; i++) {
			                    this.current_students.push({
			                        name: '',
			                        age: '', 
			                        gender: '',
			                        class: ''
			                    });
			                }
			            }
			        }
			    }
			},
			removeRow(key){
				const minStudents = this.getMinStudents(); // Get the minimum required students
			    if (this.current_students.length <= minStudents) {
			        alert(`At least ${minStudents} students must remain.`);
			        return; // Exit the function if the minimum is reached
			    }
			    this.current_students.splice(key, 1);
			    this.calculateTotal();
			},
			getMinStudents() {
			    const typeIndexMap = {
			        sub_jrs: 0,
			        jrs: 1,
			        srs: 2
			    };
			    const categoryIndex = typeIndexMap[this.current_category];
			    
			    if (categoryIndex !== undefined && this.categories[this.current_sno]) {
			        return this.categories[this.current_sno]['max'][categoryIndex][0]; // Get the min limit
			    }
			    
			    return 1; // Default to 0 if no limit found
			},
			closePopupConfirm(){
				if (this.current_students.length > 0) {
					if (confirm("save the data before cancel")) {
						return;
					}else{
						this.closePopup()
					}					
				}else{
					this.closePopup();
				}
			},
			closePopup(){
				document.getElementById('studentPopup').style.display = 'none';
	    		document.getElementById('popupOverlay').style.display = 'none';
			},
			addStudent(){
				this.newStudent.category = this.current_category;
    			this.newStudent.sno = this.current_sno;
				// console.log(this.newStudent)
				if (this.newStudent.class > 10) {
					alert("Student Class Should Be Less Than 10th");
					return;
				}
			    this.current_students.push({ ...this.newStudent });
			    this.newStudent = { name: '', age: '', gender: '', class: '', category: '', sno: '' };
			},
			calculateTotal() {
			    this.data.total_students = 0;

			    for (const sno in this.student_details) {
			        for (const category in this.student_details[sno]) {
			            this.data.total_students += this.student_details[sno][category].length;
			        }
			    }
			},
			saveStudents() {
				// console.log(this.categories[this.current_sno])
				// console.log(this.current_category)
				let stu_cat = "";
				if (this.current_category == "sub_jrs") {
					stu_cat = 0;
				}else if (this.current_category == "jrs") {
					stu_cat = 1;
				}else if (this.current_category == "srs") {
					stu_cat = 2;
				}
				let max_stu = this.categories[this.current_sno]['max'][stu_cat][0];
				if (this.current_students.length < max_stu) {
					alert("min students require!");
					return;
				}
				// console.log(stu_cat)
				// console.log(this.categories[this.current_sno]['max'][stu_cat][0])
				if (this.current_students.length === 0) {
			        alert("No students to save.");
			        return;
			    }
    			// console.log(document.getElementById('current_student_name').innerText)
				// console.log(this.current_students)
			    for (const student of this.current_students) {
			        if (!student.name || !student.age || !student.gender || !student.class) {
			            alert("Please fill in all details for each student.");
			            return;
			        }
			    }
			    // console.log(this.current_sno)
    			// console.log(this.current_category)
				let student_category = this.current_students[0].category;
				if (!this.student_details[this.current_sno]) {
			        this.student_details[this.current_sno] = {};
			    }

			    if (!this.student_details[this.current_sno][this.current_category]) {
			        this.student_details[this.current_sno][this.current_category] = [];
			    }

			    this.student_details[this.current_sno][this.current_category].push(...this.current_students);

			    this.current_students = [];

			    console.log(this.student_details)

				this.calculateTotal();
				const modal = new bootstrap.Modal(document.getElementById('studentModal'));
		        modal.hide();
				// this.closePopup();
			},
			validate_form(){
				v = document.getElementById("teacher_id").value;
				if( v.match(/^[a-zA-Z0-9\ \.\-\_\,\(\)]{3,50}$/) == null ){
					alert("Enter Teacher/Contact Person Name\n\nspecial characters not accepted!");
					return false;
				}
				if( Number(document.getElementById("total_students_id").value) < 1 ){
					alert("You have not selected any studnets for participation!");
					return false;
				}
				if( Number(document.getElementById("total_students_id").value) > 60 ){
					alert("Number of students per school is limited\n\nPlease keep your participation size not more than 60 students.");
					return false;
				}
				$("#register_btn").val("Saving Information");
				return true;
			},
		},
	}).mount("#app");
</script>
</body>