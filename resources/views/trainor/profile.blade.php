@extends('layouts.app') 
@push('styles')
<style>
	[v-cloak] { display: none; }
	.card-container { min-height: 500px; }
</style>
@endpush 

@section('content')
<v-container>
	<v-layout row wrap>
		<v-flex xs12 sm12 md12>
			<v-toolbar color="grey darken-2" dark>
				<v-toolbar-title>
					<v-icon color="green">account_circle</v-icon>
					User Profile
				</v-toolbar-title>
				<v-spacer></v-spacer>
				<v-btn v-on:click="updatePassword" color="red darken-1" dark :disabled="disallowSaveChanges">
					<v-icon class="mr-1">check_circle</v-icon>
					Save Changes
				</v-btn>
			</v-toolbar>

			<v-card class="card-container">
				<v-container fluid grid-list-lg>
					<v-layout row wrap justify-center>
						<v-flex xs12 sm6 md4>
							<label for="" class="grey--text">Firstname</label>
							<v-text-field 
							v-model="form.fname"
							:error-messages="error.fname"
							:rules="[rules.required]"
							validate-on-blur
							style="margin-bottom: -10px;" 
							solo
							></v-text-field>

							<v-layout row wrap justify-center>
								<v-flex md6>
									<label for="" class="grey--text">Middlename</label>
									<v-text-field 
									v-model="form.mname"
									:error-messages="error.mname"
									validate-on-blur
									style="margin-bottom: -10px;" 
									solo
									></v-text-field>
								</v-flex>
								<v-flex md6>
									<label for="" class="grey--text">Lastname</label>
									<v-text-field 
									v-model="form.lname"
									:rules="[rules.required]"
									validate-on-blur
									:error-messages="error.lname"
									style="margin-bottom: -10px;" 
									solo
									></v-text-field>
								</v-flex>
							</v-layout>

							<label for="" class="grey--text">Email Address</label>
							<v-text-field 
							v-model="form.email"

							:error-messages="error.email"
							:rules="[rules.required, rules.email]"
							validate-on-blur

							style="margin-bottom: -12px;" 
							solo
							></v-text-field>

							<v-checkbox
							label="Edit Password?"
							v-model="editPassword"
							></v-checkbox>

							<div v-if="editPassword">
								<label for="" class="grey--text">New password</label>
								<v-text-field 
								id="password"
								v-model="form.password"
	
								:append-icon="show1 ? 'visibility_off' : 'visibility'"
								:type="show1 ? 'text' : 'password'"
								v-on:click:append="show1 = !show1"
	
								:error-messages="error.password"
								:rules="[rules.required]"
								validate-on-blur
								style="margin-bottom: -10px;" 
								solo
								></v-text-field>
	
								<label for="" class="grey--text">Retype password</label>
								<v-text-field 
								v-model="form.re_password"
								v-on:keyup="shouldMatchPassword"
	
								:append-icon="show2 ? 'visibility_off' : 'visibility'"
								:type="show2 ? 'text' : 'password'"
								v-on:click:append="show2 = !show2"
	
								:error-messages="error.re_password"
								:rules="[rules.same_password, rules.required]"
	
								style="margin-bottom: -10px;" 
								solo
								></v-text-field>
							</div>
						</v-flex>
					</v-layout>
				</v-container>
			</v-card>
		</v-flex>
	</v-layout>
</v-container>
@endsection
 @push('scripts')
<script>
	const TRAINOR_ID = "{{ str_replace_last('trainor_', '', Auth::user()->app_user_id) }}";
	const base_url = "{{ url('/') }}";
	
	new Vue({
		el: '#app',
		data() {
			return {
				editPassword: false,
				submitButton: false,
				instructionDialog: false,
				exams: [],
				exam_detail_id: 0,
				has_noConnection: false,
				// form: {
				// 	fname: '',
				// 	mname: '',
				// 	lname: '',
				// 	email: '',
				// 	password: '',
				// 	re_password: ''
				// },
				form: {},
				error: {},
				rules: {
					required: value => !!value || 'Required.',
					counter: value => value.length <= 20 || 'Max 20 characters',
					email: value => {
						const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
						return pattern.test(value) || 'Invalid e-mail.'
					},
					same_password: value => value == this.form.password || 'Password must be same.'
				},
				show1: false,
				show2: false,
				disallowSaveChanges: false
			}
		},
		watch: {
			// watch connection: app_hasConnection status
			app_hasConnection: function() {
				if (!this.app_hasConnection) return this.has_noConnection = true;
				return this.has_noConnection = false;
			},

			form() {
				if (this.form.password > 0) {
					if (this.form.re_password === this.form.password) {
						this.submitButton = true;
					}
				}
			},
			editPassword() {
				if (this.editPassword) {
					
				}
				else {
					this.form.password = '';
					this.form.re_password = '';
					this.disallowSaveChanges = false;
				}
			}
		},
		created() {
			this.getProfile(TRAINOR_ID);
		},
		methods: {
			form_state: function() {
				return this.form = {
					fname: '',
					mname: '',
					lname: '',
					email: '',
					password: '',
					re_password: ''
				}
			},
			getProfile: function(trainor_id) {
				axios.get(`${base_url}/trainor/profile/get/${trainor_id}`)
			    .then(({data}) => {
					this.form = data;
			    })
			    .catch((err) => {
			        console.log(err.response);
			    });
			},
			updatePassword: function() {
			    axios.put(`${base_url}/trainor/profile/update/${TRAINOR_ID}`, this.form)
			    .then(({data}) => {
					if (data.status == 'logout') {
						window.location.href = "{{ route('login') }}";
					}
					else {
						this.message('Successfully saved!');
					}
			    })
			    .catch((err) => {
					var field = err.response.data.errors;
					if (field.password) return this.message(err.response.data.errors.password[0], true);
					else if (field.email) return this.message(err.response.data.errors.email[0], true);
					else if (field.lname) return this.message(err.response.data.errors.lname[0], true);
					else if (field.fname) return this.message(err.response.data.errors.fname[0], true);
			        console.log(err.response);
			    });
			},
			shouldMatchPassword(event) {
				if (event.target.value != document.querySelector('#password').value) {
					this.disallowSaveChanges = true;
				}
				else {
					this.disallowSaveChanges = false;
					
				}
			}
		}
	})
</script>
@endpush