<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="Prince Ivan Kent Tiburcio">

	<link rel="shortcut icon" type="image/x-icon" href="{{ url('public/favicon.ico') }}">
	
	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
	<!-- Web App Title -->
	<title>IPC E-learning</title>

	<!-- Compiled JS urls -->
	<script src="{{ url('public/js/app.js') }}"></script>
	
	<!-- Google Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel="stylesheet">

	<!-- Fontawesome 5.5 -->
	<link rel="stylesheet" href="{{ url('public/libraries/fontawesome/css/all.min.css') }}">

	<!-- Ionicons -->
	<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/Ionicons/css/ionicons.min.css') }}">	
	
	<!-- Parent Styles -->
	<link rel="stylesheet" href="{{ url('public/css/my-style.css') }}">
	
	<!-- Child Styles -->
	@stack('styles')

	<!-- Vue's Cloak Power -->
	<style> 
		[v-cloak] { display: none; } 
		.v-progress-circular {
			margin: 1rem
		}
		.clickable {
			cursor: pointer;
		}
	</style>
</head>
<body>
	<div id="app" v-cloak>
		<v-app id="inspire" style="background-color: #222D32;">
			@auth
				<v-toolbar 
					app 
					fixed 
					clipped-left 
					{{-- color="red darken-2" --}}
					style="background-color: #424242; z-index: 10;"
					dark>
						<v-toolbar-title class="clickable ml-2 mt-1" v-on:click="home">
							<img src="{{ url('public/images/isuzu-logo-compressor.png') }}" alt="image not found" height="25">
						</v-toolbar-title>
						<v-spacer></v-spacer>
				@auth
					@if (Auth::user()->user_type == 'trainor')
						<v-toolbar-items>
							<v-btn class="header_buttons font-weight-regular" v-bind:color="category_tab" href="{{ url('/trainor/category') }}" flat>Category</v-btn>
							<!-- <v-btn class="header_buttons font-weight-regular" v-bind:color="module_tab" href="{{ url('/trainor/modules') }}" flat>Modules</v-btn> -->
							<v-btn class="header_buttons font-weight-regular" v-bind:color="trainee_tab" href="{{ url('/trainor/trainee_list') }}" flat>Trainees</v-btn>
							<v-btn class="header_buttons font-weight-regular" v-bind:color="result_tab" href="{{ route('trainee_schedules') }}" flat>Exam Results</v-btn>
							<v-btn class="header_buttons font-weight-regular" v-bind:color="calendar_tab" href="{{ route('calendar') }}" flat>Calendar</v-btn>
							<v-btn class="header_buttons font-weight-regular" v-bind:color="contact_us_tab" href="{{ url('/trainor/contact_us') }}" flat>Contact Us</v-btn>
						</v-toolbar-items>
						<v-divider inset vertical></v-divider>
					@endif
					<v-menu
						:close-on-content-click="false">

						<v-btn slot="activator" flat>
							<v-avatar size="32px" tile>
								<img
								src="{{ url('public/images/profile-avarta@2x.png') }}"
								alt="Image"/>
							</v-avatar>&nbsp;
							{{ Auth::user()->name }}
							&nbsp;<v-icon>ios ion-android-arrow-dropdown</v-icon>
						</v-btn>

						<v-card dark>
							<v-list>
								<v-list-tile avatar>
									<v-list-tile-avatar>
										<img src="{{ url('public/images/profile-avarta@2x.png') }}" alt="Profile">
									</v-list-tile-avatar>

									<v-list-tile-content>
										<v-list-tile-title>{{ Auth::user()->name }}</v-list-tile-title>
										<v-list-tile-sub-title>{{ Auth::user()->user_type }}</v-list-tile-sub-title>
									</v-list-tile-content>

									<v-list-tile-action></v-list-tile-action>
								</v-list-tile>
							</v-list>

							<v-divider></v-divider>

							@if (Auth::user()->user_type == 'trainor')
								<v-list style="margin-top: -0.5px;">
									<v-list-tile href="{{ url('/trainor/profile') }}">
										<v-list-tile-avatar>
											{{-- <v-icon>fas fa-user</v-icon> --}}
											<v-icon class="mr-0" style="font-size: 16px; margin-bottom:3px;">fas fa-user</v-icon>
										</v-list-tile-avatar>
								
										<v-list-tile-content>
											<v-list-tile-title class="font-weight-thin">
												Edit Profile
											</v-list-tile-title>
										</v-list-tile-content>
									</v-list-tile>
								</v-list>
							@else
								<v-list style="margin-top: -0.5px;">
									<v-list-tile href="{{ url('/trainee/profile') }}">
										{{-- <v-list-tile-avatar>
											<v-icon>fas fa-user</v-icon>
										</v-list-tile-avatar> --}}
								
										<v-list-tile-content id="profile-tab">
											<v-list-tile-title class="font-weight-thin">
												<v-icon class="mr-2" style="font-size: 16px; margin-bottom:3px;">fas fa-user</v-icon>
												Edit Profile
											</v-list-tile-title>
										</v-list-tile-content>
									</v-list-tile>
								</v-list>
							@endif

							<v-card-actions>
								<v-spacer></v-spacer>
								<v-btn small dark color="red" 
									v-on:click="logout"
									>
									Logout
								</v-btn>

								<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
									@csrf
								</form>
							</v-card-actions>
							
						</v-card>
					</v-menu>
				@endauth
				</v-toolbar>
			@endauth

			<v-content>
				@yield('content')
			</v-content>

			{{-- <v-footer app fixed>
				<label style="text-align: center;">
					<strong>Copyright &copy; 2018 <a href="#">ISUZU Philippines Corporation</a>.</strong> All rights reserved.
				</label>
			</v-footer>  --}}

			<!-- Global Toast via Snackbar -->
			<v-snackbar v-cloak
				:color="`${toast.danger ? 'red' : 'green'} darken-1`"
				v-model="toast.status"
				right
				top
				:timeout="10000"
				multi-line
				auto-height
			>
				<v-icon class="mr-2" color="white">
					@{{ toast.danger ? 'close' : 'check' }}
				</v-icon>
				@{{ toast.message }}
				<v-btn
					color="white darken-3"
					flat
					v-on:click="toast.status = false"
					>
					Close
				</v-btn>
			</v-snackbar>

			<!-- Toast via Snackbar -->
			<v-snackbar
				color="red darken-2"
				v-model="app_snackbar"
				right
				bottom
				:timeout="0"
			>
				You lost internet connection
				<v-progress-circular
				size="18"
				:width="2"
				indeterminate
				color="white"
				></v-progress-circular>
			</v-snackbar>
		</v-app>
	</div>
	
	<!-- Sweet Alerts -->
	<script src="{{ url('public/libraries/sweetalert.min.js') }}"></script>

	<!-- Moment.js -->
	<script src="{{ url('public/libraries/moment.js') }}"></script>

	<!-- Mixins.js -->
	<script>
		Vue.mixin({
			data() {
				return {
					base_url: "{{ url('/') }}",
					app_snackbar: false,
					app_hasConnection: true,
					app_onExam: false,

					// Global toast notification
					toast: {
						status: false,
						message: '',
						danger: false
					},

					category_tab: '',
					module_tab: '',
					trainee_tab: '',
					result_tab: '',
					calendar_tab: '',
					contact_us_tab: ''
				}
			},
			filters: {
				dateFormat: function (value) {
					if (!value) return ''
					value = value.toString()
					return moment(value).format('MMMM D, YYYY')
				},
				dateTimeFormat: function (value) {
					if (!value) return ''
					value = value.toString()
					return moment(value).format('MMMM D, YYYY | h:mm:ss a')
				},
			},
			mounted() {
				this.asyncCheck();
			},
			methods: {
				home: function() {
					window.location.reload();
				},
				internetStatusIndicator: function() {
					if(!navigator.onLine) { 
						// Lost connection
						this.app_snackbar = true;
						this.app_hasConnection = false;
					}
					else {
						this.app_snackbar = false;
						this.app_hasConnection = true;
					}
				},
				asyncCheck: function() {
					setInterval(() => {
						if (document.readyState === 'complete') {
							this.internetStatusIndicator();
						}
					}, 1000);
				},
				logout: function(event) {
					event.preventDefault();

					axios.post(`${this.base_url}/user/logout`) //--> This will update is_active into false
					.then(() => {
						if (this.app_onExam) { //--> This will save remaining time
							this.updateRemainingTime();
						}
						else {
							document.getElementById('logout-form').submit(); // finally logout
						}
					})
					.catch((err) => {
						console.log(err.response);
					});
				},
				updateRemainingTime: function() {
					var data = {
						exam_schedule_id: localStorage.getItem('exam_schedule_id'),
						minutes: localStorage.getItem('minutes'),
						seconds: localStorage.getItem('seconds')
					}

					axios.post("{{ url('/') }}" + `/trainee/remaining_time/update`, data)
					.then(({data}) => {
						if (data) {
							localStorage.removeItem("exam_schedule_id");
							localStorage.removeItem("user_id");
							localStorage.removeItem("minutes");
							localStorage.removeItem("seconds");
						}
						document.getElementById('logout-form').submit(); // finally logout
					})
					.catch((err) => {
						console.log(err.response);
					});
				},

				// THIS SECTION IS FOR REUSABLE GLOBAL FUNCTIONS
				
				/**
				 * Toast Notification
				 */
				message: function(message, danger) {
					this.toast = {
						status: true,
						message: message,
						danger: danger ? true : false
					}
				},

				/**
				 * Scroll to element
				 */
				scrollTo: function(element, container) {
					document.addEventListener("DOMContentLoaded", (event) => {
						VueScrollTo.scrollTo(element, 1000, {
							container: container,
							easing: 'ease-in',
							offset: -60,
							cancelable: true,
							onStart: function(element) {},
							onDone: function(element) {},
							onCancel: function() {},
							x: false,
							y: true
						});
					});
				}
			}
		})
	</script> 
	<!-- Child Scripts -->
	@stack('scripts')
</body>
</html>
