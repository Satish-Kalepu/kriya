
<script src="vue.min.prod.js"></script>
<script src="axios.min.js"></script>
	<style>
		.mpp td,.mpp th{ border:1px solid #999; }
		.mpp td span{ color:red; font-weight:500; }
		.mpp .mp { font-size:1.2rem; }
		.mpp tbody tr { cursor:pointer; }
		.mpp tbody tr:hover { background-color:#ffbbee; }
		.mpp .mp span { font-size:1.2rem; }
		.ggt input, .ggt select { height:25px; color:black; padding:0px 10px; }
	</style>

<div id="app" style="margin:20px;" >
	<p>School Database</p>
	<div style="padding: 5px; min-height: 25px; font-weight: 400;">
		<div v-if="verr" style=" color:red; border:1px solid orange;" >{{ verr }}</div>
		<div v-if="vmsg" style=" font-weight: 400; color:black; border:1px solid #888;" >{{ vmsg }}</div>
	</div>

	<div style="margin-bottom:20px; display:flex; column-gap:20px; " >
		<div>
			<input type="text" v-model="keyword" placeholder="Search" id="keyword" > &nbsp; <input type="button" value="Search" v-on:click="search" >
		</div>
		<div><input v-if="end<total" type="button" value="Next" v-on:click="snext()" > <input style="margin-left:20px;" v-if="p>1" type="button" value="Previous" v-on:click="sprev()" > </div>
		<div>{{ start }} to {{ end }} of {{ total }}</div>
		<div><input type="button" value="Add School" v-on:click="add_school()" ></div>
	</div>

	<table class="mpp" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">
		<thead style="position:sticky; top:0px; background-color:#f0f0f0; font-weight:400; box-shadow:0px 1px 0px #666;">
			<tr>
				<th>School ID</th>
				<th>Name</th>
				<th>Category</th>
				<th>District</th>
				<th>Mandal</th>
				<th>Village</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="d,i in records" v-on:click="editit(i)" >
				<td class="mp" nowrap v-html="repl(d['school_id'])" ></td>
				<td nowrap v-html="repl(d['school_name'])" ></td>
				<td nowrap v-html="repl(d['school_category'])" ></td>
				<td nowrap v-html="repl(d['district_name'])" ></td>
				<td nowrap v-html="repl(d['mandal_name'])" ></td>
				<td nowrap v-html="repl(d['village_name'])" ></td>
			</tr>
		</tbody>
	</table>

	<template v-if="vpop" >
	<div style="position: fixed; width: 100%; height: 100%; top:0px; left:0px; background-color: rgba(0,0,0,0.5); z-index:500;" >
	<div style="border:1px solid #333; background-color: white; box-shadow:2px 2px 5px #888; width:500px; position:fixed; top:50px; left:50px;" >
		<div style="padding:10px; background-color:#eee;">
			<div style="float:right; font-weight:bold; padding:0px 5px; cursor: pointer; " v-on:click="vpop=false" >Close</div>
			<div style="font-weight:bold; ">Edit School</div>
		</div>
		<div class="ggt" style="padding:10px; ">
			<table width="100%" cellpadding="5">
				<tr>
					<td width="120" align="right">School ID</td>
					<td><input v-if="edit_id==-1" type="text" v-model="edit['school_id']" placeholder="School ID" style="width:100%;" >
						<div v-else>{{ edit['school_id'] }}</div>
					</td>
				</tr>
				<tr>
					<td align="right">School</td>
					<td><input type="text" v-model="edit['school_name']" placeholder="School Name" style="width:100%;" ></td>
				</tr>
				<tr>
					<td align="right">Category</td>
					<td>
						<select v-model="edit['school_category']" style="width:100%;" >
							<option value="-" >-</option>
							<option value="Pr. Up Pr. and Secondary Only" >Pr. Up Pr. and Secondary Only</option>
							<option value="Pr. with Up.Pr. sec. and H.Sec." >Pr. with Up.Pr. sec. and H.Sec.</option>
							<option value="Primary with Upper Primary" >Primary with Upper Primary</option>
							<option value="Primary" >Primary</option>
							<option value="Secondary Only" >Secondary Only</option>
							<option value="Secondary School" >Secondary School</option>
							<option value="Secondary with Higher Secondary" >Secondary with Higher Secondary</option>
							<option value="Secondary" >Secondary</option>
							<option value="Senior Secondary" >Senior Secondary</option>
							<option value="Up. Pr. Secondary and Higher Sec" >Up. Pr. Secondary and Higher Sec</option>
							<option value="Upper Pr. and Secondary" >Upper Pr. and Secondary</option>
							<option value="Upper Primary only" >Upper Primary only</option>
						</select>
					</td>
				</tr>
				<tr>
					<td align="right">District</td>
					<td><input type="text" v-model="edit['district_name']" placeholder="Distrcit" style="width:100%;" ></td>
				</tr>
				<tr>
					<td align="right">Mandal</td>
					<td><input type="text" v-model="edit['mandal_name']" placeholder="Mandal" style="width:100%;" ></td>
				</tr>
				<tr>
					<td align="right">Village</td>
					<td><input type="text" v-model="edit['village_name']" placeholder="Village" style="width:100%;" ></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="button" value="Save" v-on:click="saveit()" > <input type="button" value="Cancel" v-on:click="vpop=false" style="float:right;" ></td>
				</tr>
			</table>
			<div style="padding:5px;color:red;">{{ editerr }}</div>
		</div>
	</div>
	</div>
	</template>

</div>
<script>
	var app = Vue.createApp({
		data: function(){
			return {
				editerr: "", verr: "", vmsg: "", records: [], keyword: "", kpr: false, p: 1,
				total: 0, start: 0, end: 0, vpop: false, edit: {}, edit_id: -1,
			};
		},
		mounted: function(){
			this.get();
		},
		methods: {
			add_school: function(){
				this.edit_id = -1;
				this.edit = {
					"school_id":"",
					"district_name":"",
					"mandal_name":"",
					"village_name":"",
					"school_name":"",
					"school_category":"Pr. with Up.Pr. sec. and H.Sec.",
					"enc":"new"
				};
				this.vpop = true;
			},
			repl: function(v){
				var s = v.match( this.kpr );
				if( s != null ){
					v = v.replace(s[0], "<span>" + s[0] + "</span>");
				}
				return v;
			},
			editit: function( vi ){
				this.edit_id = vi;
				this.edit = JSON.parse( JSON.stringify( this.records[ vi ] ) );
				this.vpop = true;
			},
			search: function(){
				this.p = 1;
				this.get();
			},
			snext: function(){
				this.p = this.p + 1;
				this.get();
			},
			sprev: function(){
				this.p = this.p - 1;
				this.get();
			},
			get: function(){
				this.kpr = new RegExp( this.keyword.trim(), "i");
				this.vmsg = "Loading...";
				this.verr = "";
				axios.get("?action=searchdb&keyword=" + this.keyword.trim() + "&p="+this.p ).then(response=>{
					this.total = response.data['total'];
					this.start = (Number(this.p)-1)*100 + 1;
					this.end = this.start + 100;
					if( this.end > this.total ){
						this.end = this.total;
					}
					this.vmsg = "";
					this.records = response.data['records'];
				}).catch(error=>{
					this.vmsg = "Error";
				});
			},
			saveit: function(){
				this.editerr = "";
				this.edit['school_id'] = this.edit['school_id'].trim();
				if( this.edit['school_id'].match(/^[0-9]{4,25}$/) == null ){
					this.editerr = "School Id incorrect";return false;
				}
				this.edit['school_name'] = this.edit['school_name'].trim();
				this.edit['district_name'] = this.edit['district_name'].trim();
				this.edit['mandal_name'] = this.edit['mandal_name'].trim();
				this.edit['village_name'] = this.edit['village_name'].trim();
				if( this.edit['school_name'].match(/^[a-z][a-z0-9\.\,\ \-\_\&\@\(\)]{4,100}$/i) == null ){
					this.editerr = "School Name Incorrect";return false;
				}
				if( this.edit['school_category'].match(/^[a-z][a-z0-9\.\,\ \-\_\&\@]{4,100}$/i) == null && this.edit['school_category'] != "-" ){
					this.editerr = "School Category Incorrect";return false;
				}
				if( this.edit['district_name'].match(/^[a-z][a-z0-9\.\,\ \-]{4,50}$/i) == null && this.edit['district_name'].trim() != "" ){
					this.editerr = "District Incorrect";return false;
				}
				if( this.edit['mandal_name'].match(/^[a-z][a-z0-9\.\,\ \-]{4,50}$/i) == null && this.edit['mandal_name'].trim() != "" ){
					this.editerr = "Mandal Incorrect";return false;
				}
				if( this.edit['village_name'].match(/^[a-z][a-z0-9\.\,\ \-]{4,50}$/i) == null && this.edit['village_name'].trim() != "" ){
					this.editerr = "Village Incorrect";return false;
				}
				this.editerr = "Saving...";
				axios.post("?", "action=master_school_edit&edit="+encodeURIComponent(JSON.stringify(this.edit)), {
					header:{"Content-Type":"application/x-www-form-urlencoded"}
				}).then(response=>{
					this.editerr = "";
					if( response.data['status'] == "success" ){
						this.editerr = "Database Updated";
						this.records[ this.edit_id ] = JSON.parse( JSON.stringify( this.edit ) );
					}else{
						this.editerr = response.data['error'];
					}
				}).catch(error=>{
					this.editerr = "Error ";
				})
			}
		}
	}).mount("#app");
</script>