<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="shortcut icon" type="image/x-icon" href="{{ url('public/favicon.ico') }}">
	
	<!-- CSRF TOKEN-->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
	<!-- Title -->
	<title>IPC E-learning</title>

	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">

	<!-- Compiled Vuejs and Vuetify Libraries -->
	<script src="{{ url('public/js/app.js') }}"></script>

	<!-- Google Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic" rel="stylesheet">

	<!-- iCheck CSS -->
	<link rel="stylesheet" href="{{ url('public/libraries/iCheck/all.css') }}">
	
	<!-- Ionicons -->
	<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/Ionicons/css/ionicons.min.css') }}">	
	
	<!-- Theme style -->
	<link rel="stylesheet" href="{{ url('public/admin-lte/dist/css/AdminLTE.min.css') }}">
	
	<!-- Theme Color -->
	<link rel="stylesheet" href="{{ url('public/admin-lte/dist/css/skins/skin-red.min.css') }}">
		
	<!-- Toastr CSS -->
	<link rel="stylesheet" href="{{ url('public/libraries/toastr.css') }}">

	<!-- Fontawesome 5.5 -->
	<link rel="stylesheet" href="{{ url('public/libraries/fontawesome/css/all.min.css') }}">
	{{-- <link rel="stylesheet" href="{{ url('public/libraries/fontawesome.min.css') }}"> --}}

	<!-- Parent Styles -->
	<link rel="stylesheet" href="{{ url('public/css/my-style.css') }}">
	<link rel="stylesheet" href="{{ url('public/css/styles.css') }}">

	<style>
		body, html {
			overflow-y: auto;
		}
	</style>

	<!-- Child Styles -->
	@stack('styles')
</head>
		
<body class="hold-transition skin-red sidebar-mini">

	<div class="wrapper">
		@include('templates.user_headers')
		@include('templates.header')
		@include('templates.sidebar')
		<div id="app" class="content-wrapper">
			<v-app style="min-height: 500px;">
				<v-content>
					@yield('content')

					<!-- Global Toast via Snackbar -->
					<v-snackbar v-cloak
						v-model="toast.status"
						right
						bottom
						:timeout="8000"
					>
						@{{ toast.message }}
						<v-btn
							color="pink"
							flat
							v-on:click="toast.status = false"
							>
							Close
						</v-btn>
					</v-snackbar>

					<!-- Toast for internet status notification -->
					<v-snackbar v-cloak
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
				</v-content>
			</v-app>
		</div>
		@include('templates.footer')
	</div>
	
	<!-- Bootstrap 3.3.7 -->
	<script src="{{ url('public/admin-lte/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>

	<!-- iCheck -->
	<script src="{{ url('public/libraries/iCheck/icheck.min.js') }}"></script>
	
	<!-- AdminLTE App -->
	<script src="{{ url('public/admin-lte/dist/js/adminlte.min.js') }}"></script>
	
	<!-- CSRF config thru AJAX request -->
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
	</script>

	<!-- Slim Scroll -->
	<script src="{{ url('public/libraries/jquery.slimscroll.min.js') }}"></script>
	
	<!-- Sweet Alerts -->
	<script src="{{ url('public/libraries/sweetalert.min.js') }}"></script>
		
	<!-- Toastr JS -->
	<script src="{{ url('public/libraries/toastr.min.js') }}"></script>
	<script>
		@if (Session::has('message'))
			var type = "{{ Session::get('alert-type', 'info') }}";
			switch(type){
				case 'info':
				toastr.info("{{ Session::get('message') }}", "{{ Session::get('title') }}");
				break;
				
				case 'warning':
				toastr.warning("{{ Session::get('message') }}", "{{ Session::get('title') }}");
				break;
				
				case 'success':
				toastr.success("{{ Session::get('message') }}", "{{ Session::get('title') }}");
				break;
				
				case 'error':
				toastr.error("{{ Session::get('message') }}", "{{ Session::get('title') }}");
				break;
			}
		@endif
	</script>

	<!-- Toastr Configuration -->
	<script src="{{ url('public/js/toastr.config.js') }}"></script>

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
						message: ''
					}
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
					return moment(value).format('MMM D, YYYY | h:mm a')
				}
			},
			mounted() {
				this.asyncCheck();
			},
			methods: {
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

				// THIS SECTION IS FOR REUSABLE GLOBAL FUNCTIONS
				
				/**
				 * Toast Notification
				 */
				message: function(message) {
					this.toast = {
						status: true,
						message: message
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
		});
	</script>
	
	<!-- Child Scripts -->
	@stack('scripts')
</body>
</html>