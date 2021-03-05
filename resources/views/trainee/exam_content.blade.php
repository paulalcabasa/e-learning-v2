@extends('layouts.app')

@push('styles')
	<link href="https://fonts.googleapis.com/css?family=Raleway:300,400,500,700" rel="stylesheet" type="text/css">
	<style>
		.question-container {
			color: #636b6f;
			font-family: 'Raleway', sans-serif;
			font-weight: 600;
		}
	</style>
@endpush

@section('content')


<v-container>
		
	<v-layout row wrap>
		<v-flex xs12 sm12 md12>
			<v-toolbar color="grey darken-2" dark>
				<v-toolbar-title>
					@{{ exam_header.module }}
				</v-toolbar-title>
				<v-spacer></v-spacer>
				<div>Remaining Time: <span id="time" class="green--text lighten-2 font-weight-bold title"></span></div>
			</v-toolbar>

			<v-card>
				<v-container
					fluid
					grid-list-md>

					<v-layout>
						<v-flex xs12 sm8 md9 lg10>
							<!-- Exam Content -->
							<v-card v-if="exam_container" color="" class="mt-2 elevation-2" style="min-height: 390px;">
								<v-card-title class="question-container">
									<v-container>
										<v-badge left color="red" class="ml-3 mr-1" style="margin-bottom: 7px;">
											<span slot="badge" style="font-size: 15px;">@{{ question.number }}</span>
										</v-badge>
										<span style="font-size: 19px;">
											@{{ formatQuestion(question.question) }}
										</span>
										<br/>

										<v-container fluid v-show="media.length > 0">
											<v-layout justify-space-around>
												<v-flex xs3 v-if="media.length > 1">
													<v-layout column >
													<div class="subheading">@{{ media[0].name }}</div>
													<v-img v-if="media[0].file_type == 'image'" :src="media[0].url" width="300"></v-img>
													<video v-if="media[0].file_type == 'video'" width="320" height="240" controls controlsList="nodownload">
														<source :src="media[0].url" type="video/mp4">
														Your browser does not support the video tag.
													</video>
												</v-flex>
												<v-flex xs3 v-if="media.length > 2">
													<v-layout column >
													<div class="subheading">@{{ media[1].name }}</div>
													<v-img v-if="media[1].file_type == 'image'" :src="media[1].url" width="300"></v-img>
													<video v-if="media[1].file_type == 'video'" width="320" height="240" controls controlsList="nodownload">
														<source :src="media[1].url" type="video/mp4">
														Your browser does not support the video tag.
													</video>
												</v-flex>
											</v-layout>

											<v-layout justify-space-around v-if="media.length > 3">
												<v-flex xs3 v-if="media.length > 3">
													<v-layout column >
													<div class="subheading">@{{ media[2].name }}</div>
													<v-img v-if="media[2].file_type == 'image'" :src="media[2].url" width="300"></v-img>
													<video v-if="media[2].file_type == 'video'" width="320" height="240" controls controlsList="nodownload">
														<source :src="media[2].url" type="video/mp4">
														Your browser does not support the video tag.
													</video>
												</v-flex>
												<v-flex xs3 v-if="media.length > 4">
													<v-layout column >
													<div class="subheading">@{{ media[3].name }}</div>
													<v-img v-if="media[3].file_type == 'image'" :src="media[3].url" width="300"></v-img>
													<video v-if="media[3].file_type == 'video'" width="320" height="240" controls controlsList="nodownload">
														<source :src="media[3].url" type="video/mp4">
														Your browser does not support the video tag.
													</video>
												</v-flex>
											</v-layout>
											<v-layout justify-space-around v-if="media.length > 5">
												<v-flex xs3 v-if="media.length > 5">
													<v-layout column >
													<div class="subheading">@{{ media[4].name }}</div>
													<v-img v-if="media[4].file_type == 'image'" :src="media[4].url" width="300"></v-img>
													<video v-if="media[4].file_type == 'video'" width="320" height="240" controls controlsList="nodownload">
														<source :src="media[4].url" type="video/mp4">
														Your browser does not support the video tag.
													</video>
												</v-flex>
												
											</v-layout>
										
										
										</v-container>
									
										<div>
											<v-radio-group
												v-on:change="getChoiceAnswered"
												v-on:click="choiceHasChanged = true"
												v-model="question.choice_id">
												<v-radio
													v-for="(choice, index) in choices"
													:key="choice.choice_id"
													:label="`${choice.choice_letter}. ${choice.choice}`"
													:value="choice.choice_id"
													color="green"
													class="mt-2">
												</v-radio>
											</v-radio-group>
										</div>
									</v-container>
								</v-card-title>
							</v-card>
						</v-flex>
						<v-flex xs12 sm4 md3 lg2>
							<v-card v-if="exam_container" class="mt-2 elevation-2" style="min-height: 390px;">
								<v-card-title><h4>EXAM DETAILS</h4></v-card-title>
								<v-divider></v-divider>

								<v-list dense>
									<v-list-tile>
										<v-list-tile-content>Alloted Time:</v-list-tile-content>
										<v-list-tile-content class="align-end">
											<strong>
												@{{ exam_header.timer }} min(s)
											</strong>
										</v-list-tile-content>
									</v-list-tile>
									<v-list-tile style="margin-top: -14px;">
										<v-list-tile-content>Total Items:</v-list-tile-content>
										<v-list-tile-content class="align-end">
											<strong>
												@{{ exam_header.items }} item(s)
											</strong>
										</v-list-tile-content>
									</v-list-tile>
								</v-list>

								<v-flex>
									<v-btn
									v-on:click="finishedExam"
									color="success"
									block
									bottom
									dark
									small
									>
										Finish
									</v-btn>
								</v-flex>

							</v-card>
						</v-flex>
					</v-layout>

					<!-- Pagination Content -->
					<v-layout justify-center>
						<v-flex>
							<v-card v-if="exam_container" color="grey lighten-4" class="mt-2 elevation-2">
								<v-card-text style="text-align: center;">
									<v-pagination
										v-model="page"
										:length="items"
										color="red darken-2"
									></v-pagination>
								</v-card-text>
							</v-card>
						</v-flex>
					</v-layout>

					<div v-if="exam_container == false">
						<v-layout row wrap justify-center>
							<div style="margin-top: 200px; margin-bottom: 200px;">
								<h3 class="red--text font-weight-medium">
									<v-icon color="red" style="margin-top: 5px;">mood_bad</v-icon>
									Please wait for an internet connection.
								</h3>
							</div>
						</v-layout>
					</div>
				</v-container>
			</v-card>
		</v-flex>
	</v-layout>
</v-container>
@endsection

@push('scripts')
<script>
	const user_id = "{{ str_replace_last('trainee_', '', Auth::user()->app_user_id) }}";
	const EXAM_DETAIL_ID = "{{ Request::segment(3) }}";
	const base_url = "{{ url('/') }}";

	var vm = new Vue({
		el: '#app',
		data() {
			return {
				base_url : '',
				exam_container: true,
				choiceHasChanged: false,
				page: 1,
				exam_header: {},
				question: {},
				items: 0,
				has_blank_answer: 0,
				didTimerStart: false,
				choices: [],
				pickedChoice: {
					trainee_question_id: 0,
					choice_id: 0
				},
				stateCheck: '',
				media : []
			}
		},
		computed: {
			choice_id: function() {
				return this.question.choice_id;
			},
			remainingMinutes: function() {
				return this.question.remaining_time;
			},
			remainingSeconds: function() {
				return this.question.seconds;
			}
		},
		watch: {
			// watch page navigation
			page: function() {
				this.getQuestion(this.page);
			},
			choiceHasChanged: function() {
				this.answerQuestion(this.choice_id);
			},
			// watch connection: app_hasConnection status
			app_hasConnection: function() {
				this.startTime();
				if (!this.app_hasConnection) return this.exam_container = false;
				return this.exam_container = true;
			}
		},
		created() {
			this.app_onExam = true;
			this.getExamContent(EXAM_DETAIL_ID);
		},
		mounted() {
			// this.startTime();
			this.base_url = base_url;
			this.startWatchingTimer();
			this.checkReloadDuringExam(this.app_onExam);
		},
		methods: {
			startWatchingTimer: function() {
				setInterval(() => {
					if (!this.didTimerStart) this.startTime();
				}, 1000);
			},
			triggerExam: function(exam_detail_id) {
				axios.put(`${base_url}/trainee/trigger_exam/${exam_detail_id}`)
				.then(({data}) => {
					console.log('exam initiated');
				})
				.catch((err) => {
					console.log(err.response);
					swal('Ooops!', 'Something went wrong.', 'error', {timer:4000,button:false});
				});
			},
			getExamContent: function(exam_detail_id) {
				axios.get(`${base_url}/trainee/exam_content/get/${exam_detail_id}`)
				.then(({data}) => {
					this.exam_header = data;
					this.items = parseInt(data.items);
					this.triggerExam(EXAM_DETAIL_ID);
					this.getQuestion(this.page);
				})
				.catch((err) => {
					console.log(err.response);
				});
			},
			getQuestion: function(number) {
				var params = {
					exam_schedule_id: this.exam_header.exam_schedule_id,
					trainee_id: user_id,
					number: number
				};
				axios.post(`${this.base_url}/trainee/question`, params)
				.then(({data}) => {
					this.question = data.questions;
					this.choices = data.choices;
					this.choiceHasChanged = false;
					this.media = this.getMedia(this.question.question, this.exam_header.module.toLowerCase().trim());
				})
				.catch((err) => {
					console.log(err.response);
				});
			},
			getChoiceAnswered: function(val) {
				this.updateChoiceAnswered({
					trainee_question_id: this.question.trainee_question_id,
					choice_id: val
				});
			},
			updateChoiceAnswered: function(pickedChoice) {
				return axios.post(`${this.base_url}/trainee/update_answered_choice`, pickedChoice)
					.then(({data}) => {
						console.log(data);
					})
					.catch((error) => {
						console.log(error.response);
					});
			},	
			answerQuestion: function(choice_id) {
				axios.put(`${base_url}/trainee/answer/${this.question.trainee_question_id}`, {'choice_id': choice_id})
				.then(({data}) => {
					this.choiceHasChanged = false;
				})
				.catch((err) => {
					console.log(err);
					swal('Ooops!', 'Something went wrong.', 'error', {timer:4000,button:false});
				});
			},

			// Timer Handle
			timer: function(minutes, seconds, display) {
				var duration = minutes > 0 ? 60 * minutes : 0;  // Convert minutes into seconds
				var timer = duration + seconds;                 //--> Add remaining Seconds

				this.stateCheck = setInterval(() => {
					minutes = parseInt(timer / 60, 10)
					seconds = parseInt(timer % 60, 10);

					// Wrap Minutes and Seconds
					minutes = minutes < 10 ? "0" + minutes : minutes;
					seconds = seconds < 10 ? "0" + seconds : seconds;

					display.textContent = minutes + ":" + seconds; //--> display into HTML

					this.backupTime(minutes, seconds); //--> Backup time into localStorage

					if (!this.checkInternetConnection()) {
						this.pauseTime(this.stateCheck); //--> capture if Lost internet connection
					}

					if (--timer < 0) {
						this.pauseTime(this.stateCheck);
						this.forceEndTime(); //--> capture if timer is done!
					}
				}, 1000);
			},

			resetTimer: function () {
				clearInterval(this.stateCheck);
			},

			// ---> Time Backup <---
			backupTime: function(minutes, seconds) {
				localStorage.setItem("exam_schedule_id", this.exam_header.exam_schedule_id);
				localStorage.setItem("user_id", user_id);
				localStorage.setItem("minutes", minutes);
				localStorage.setItem("seconds", seconds);
			},
			getRemainingMinutes: function() {
				if (localStorage.getItem('user_id') != user_id) return null;
				return localStorage.getItem('minutes');
			},
			getRemainingSeconds: function() {
				if (localStorage.getItem('user_id') != user_id) return null;
				return localStorage.getItem('seconds');
			},
			removeTimer: function() {
				this.resetTimer();
				localStorage.clear();
			},

			// Timer Logic
			startTime: function() {
				this.didTimerStart = true;
				this.exam_container = true;
				setTimeout(() => {
					if (this.app_hasConnection) {
						this.saveRemainingTimeEveryFiveMinutes();
						// Parameters
						var minutes;
						if (this.getRemainingMinutes() <= 0
							&& this.getRemainingSeconds() <= 0) minutes = this.remainingMinutes; // Reset
						else minutes = this.getRemainingMinutes();

						var seconds;
						if (this.getRemainingMinutes() <= 0
							&& this.getRemainingSeconds() <= 0) seconds = this.remainingSeconds; // Reset
						else seconds = new Number(this.getRemainingSeconds());

						var display = document.querySelector('#time');

						if (minutes == '00' || minutes > 0
							&& seconds == '00' || seconds > 0) return this.timer(minutes, seconds, display);
					}
				}, 1000);
			},
			pauseTime: function(fn) {
				clearInterval(fn);
				this.exam_container = false;
			},
			endTime: function() {
				this.removeTimer();

				var data = {
					timer: 0,
					exam_schedule_id: this.exam_header.exam_schedule_id,
					exam_detail_id: EXAM_DETAIL_ID
				}

				axios.put(`${base_url}/trainee/timers_up/${user_id}`, data)
				.then(({data}) => {
					window.location = `${base_url}/trainee`;
				})
				.catch((err) => {
					console.log(err.response);
					swal('Ooops!', 'Something went wrong.', 'error', {timer:4000,button:false});
				});
			},

			forceEndTime: function() {
				var params = {
					exam_schedule_id: this.exam_header.exam_schedule_id,
					trainee_id: user_id
				};
				axios.post(`${base_url}/trainee/has_blank_answer`, params)
				.then(({data}) => {
					var text = data ?
						   'It seems that you didn\'t answered all of the questions.':
						   'Well done, your score has been generated.';

						swal({
							title: "TIME'S UP!",
							text: text,
							icon: "error",
							buttons: {
								// cancel: true,
								confirm: "Proceed Anyway"
							},
							dangerMode: true,
							closeOnClickOutside: false
							// timer: 5000
						})
						.then((next) => {
							if (next) return this.endTime();
						});
				})
				.catch((err) => {
					console.log(err.response);
				});
			},

			finishedExam: function() {
				this.hasBlankAnswer();
			},

			hasBlankAnswer: function() {
				var params = {
					exam_schedule_id: this.exam_header.exam_schedule_id,
					trainee_id: user_id
				};
				axios.post(`${base_url}/trainee/has_blank_answer`, params)
				.then(({data}) => {
					var text = data ?
						   'You have not answered all of the questions' :
						   'It\'s not too late you can double check your answers';

					swal({
						title: "Are you sure?",
						text: text,
						icon: "warning",
						buttons: {
							cancel: true,
							confirm: "Proceed Anyway"
						},
						dangerMode: true,
						closeOnClickOutside: false,
					})
					.then((next) => {
						if (next)
							swal("Alright!", {
								icon: "success"
							})
							.then((next) => {
								this.endTime();
							});
						return;
					});
				})
				.catch((err) => {
					console.log(err.response);
				});
			},

			checkReloadDuringExam: function(app_onExam) {
				window.onbeforeunload = function () {
					var seconds = new Number(localStorage.getItem('seconds')),
						deducted = seconds - 1;

					if (app_onExam) {

						/**
						 * Offset seconds to 59 since we deduct 1 to prevent negative
						*/
						if (deducted < 0) deducted = 59;

						/**
						 * Proceed Deducting
						*/
						else localStorage.setItem('seconds', parseInt(deducted));
					}
				}
			},

			saveRemainingTimeEveryFiveMinutes: function() {
				setInterval(() => {
					var data = {
						exam_schedule_id: localStorage.getItem('exam_schedule_id'),
						minutes: this.getRemainingMinutes(),
						seconds: this.getRemainingSeconds()
					}

					axios.post(`${base_url}/trainee/remaining_time/update`, data)
					.then()
					.catch((err) => {
						console.log(err.response);
					});
				}, 5000);
			},

			// Connection
			checkInternetConnection: function() {
				return this.app_hasConnection;
			},

			// extract media

			getMedia(string, exam_module){
				// var exam_module = exam_header.module;
				// console.log(exam_module);
				var self = this;
				var mediaUrls = [];
				var startIndex = string.indexOf('See media: '); // 11 is the whole word
				if(startIndex !== -1){
					startIndex+= 11;
					var media = string.substr(startIndex, string.length).split(';');
					for(var i = 0; i < media.length; i++){
						let fileExtension = media[i].split('.').pop();
						let fileType = '';
						if(fileExtension == 'jpg' || fileExtension == 'png'){
							fileType = 'image';
						}
						else if(fileExtension == 'mp4'){
							fileType = 'video';
						}
						let url = self.base_url + '/public/storage/ftp-media/' +  exam_module + '/' + media[i];
						mediaUrls.push({
							url : url,
							name : media[i],
							file_type : fileType
						});
					}
				}
				return mediaUrls;
			},

			formatQuestion(question){
				var startIndex = question.indexOf('See media: '); // 11 is the whole word
				if(startIndex !== -1){
					return question.substr(0, startIndex)
				}
				return question;
			}

		}
	});
</script>
@endpush
