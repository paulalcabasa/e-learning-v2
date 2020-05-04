@extends('layouts.app')

@push('styles')
	<style>
		#__image-container {
			background-image: url('/e-learning/public/images/e-learning2.jpg');
			background-repeat: no-repeat;
  			background-position: 0 0;
    		background-size: cover;
			margin: -24px;
		}
		#login-box {
			margin: 100px 50px;
		}
		.custom-loader {
			animation: loader 1s infinite;
			display: flex;
		}
		.sub-header {
			color: #D8D8D8;
		}
		.sub-title {
			color: white;
		}
		@-moz-keyframes loader {
			from {
			transform: rotate(0);
			}
			to {
			transform: rotate(360deg);
			}
		}
		@-webkit-keyframes loader {
			from {
			transform: rotate(0);
			}
			to {
			transform: rotate(360deg);
			}
		}
		@-o-keyframes loader {
			from {
			transform: rotate(0);
			}
			to {
			transform: rotate(360deg);
			}
		}
		@keyframes loader {
			from {
			transform: rotate(0);
			}
			to {
			transform: rotate(360deg);
			}
		}
	</style>
@endpush

@section('content')
<v-container fluid fill-height>
	<v-layout>
		{{-- <v-flex xs12 sm8 md6 lg4> --}}
		{{-- <v-flex xs12 sm8 md6 lg4> --}}
		<v-flex xs0 sm0 md6 lg8 id="__image-container"></v-flex>
		<v-flex xs12 sm12 md6 lg4>
			<v-card class="transparent" flat>
				{{-- <v-toolbar dark color="red darken-2">
					<v-spacer></v-spacer>
					<v-avatar class="mb-5" size="6rem" tile>
						<img src="{{ url('public/images/profile-avarta@2x.png') }}"
						alt="Image"/>
					</v-avatar>
					<v-spacer></v-spacer>
				</v-toolbar> --}}
				
				<v-container id="login-box" align-center>
					<v-layout class="mb-4" align-center>
						<v-spacer></v-spacer>
						<img src="{{ url('public/images/new_logo.png') }}" width="150" alt="Image"/>
						{{-- <img src="{{ url('public/images/logo.png') }}" width="150" alt="Image"/> --}}
						<v-spacer></v-spacer>
					</v-layout>
					<v-layout class="mb-3 text--red" align-center>
						<v-spacer></v-spacer>
						<h3 class="sub-header title font-weight-thin">
							Welcome to <strong class="sub-title">IPC E-learning System</strong>
							</h3>
						<v-spacer></v-spacer>
					</v-layout>
					<v-divider class="center my-1 mx-3" dark></v-divider>
					<v-layout align-center>
						<v-flex>
							@if (session('status'))
								<v-alert
								:value="true"
								dismissible
								type="error"
								>
								{{ session('status') }}
								</v-alert>
							@endif
							<v-card-text>
								<form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
									@csrf
									<div>
										<v-text-field 
										v-model="email"
										label="Email Address"
										id="email" 
										type="text" 
										name="email" 
										:error-messages="error.email"
										:rules="[rules.required, rules.email]"
										:loading="email_loading"
										v-on:blur="checkIfLoggedYet"
										validate-on-blur
										solo
										autofocus>
										</v-text-field>
									</div>
						
									<div>
										<v-text-field
										:append-icon="show3 ? 'visibility_off' : 'visibility'"
										:type="show3 ? 'text' : 'password'"
										@click:append="show3 = !show3"
										class="input-group--focused"

										label="Password"
										id="password" 
										name="password" 
										:error-messages="error.password"
										:rules="[rules.required]"
										:loading="password_loading"
										validate-on-blur
										solo
										></v-text-field>
									</div>
						
									<v-card-actions>
										<v-spacer></v-spacer>
										<v-btn 
											type="submit"
											@click="onSubmit()"
											class="white--text"
											:loading="loading4"
											color="red"
											block
										>
											SIGN IN &nbsp;
											<v-icon style="font-size: 18px;">fa fa-sign-in-alt</v-icon>
											<span slot="loader" class="custom-loader">
												<v-icon>cached</v-icon>
											</span>
										</v-btn>
										{{-- <v-btn
											type="submit" 
											v-on:click="onSubmit"
											class="white--text"
											:loading="loading4"
											:disabled="loading4"
											@click.native="loader = 'loading4'"
											color="red"
											block
											:disabled="hasLoggedYet"
											>
											SIGN IN &nbsp;
											<v-icon style="font-size: 18px;">fa fa-sign-in-alt</v-icon>
											<span slot="loader" class="custom-loader">
												<v-icon>cached</v-icon>
											</span>
										</v-btn> --}}
										<v-spacer></v-spacer>
									</v-card-actions>
								</form>
							</v-card-text>
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
	const base_url = "{{ url('/') }}";
	new Vue({
		el: '#app',
		data() {
			return {
				loader: null,
				loading4: false,
				show1: false,
				show2: true,
				show3: false,
				show4: false,
				drawer: null,
				password: 'Password',
				email_loading: false,
				password_loading: false,
				rules: {
					required: value => !!value || 'Required.',
					counter: value => value.length <= 20 || 'Max 20 characters',
					email: value => {
						const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
						return pattern.test(value) || 'Invalid e-mail.'
					}
				},
				hasLoggedYet: false,
				email: "{{ old('email') }}",
				error: {
					email: "{{ session('errors') }}",
					password: "{{ session('password') }}"
				}
			}
		},
		props: {
			source: String
		},
		watch: {
			email: function() {
				if (!this.email) {
					this.hasLoggedYet = false;
					this.error.email = '';
				}
			},
			loader () {
				const l = this.loader
				this[l] = !this[l]

				setTimeout(() => (this[l] = false), 3000)

				this.loader = null
			}
		},
		methods: {
			onSubmit: function() {
				this.email_loading = true;
				this.password_loading = true;
				this.loader = 'loading4'
			},
			checkIfLoggedYet: function() {
				if (this.email) {
					axios.post(`${base_url}/check_logging`, {email: this.email})
					.then(({data}) => {
						if (data.is_active && !data.user_type == 'trainor') {
							this.error.email = 'Ooops! logout first to your previous device.';
							return this.hasLoggedYet = true;
						}
						this.error.email = '';
						return this.hasLoggedYet = false;
					})
					.catch((err) => {
						console.log(err.response);
					});
				}
			}
		}
	})
</script>
@endpush