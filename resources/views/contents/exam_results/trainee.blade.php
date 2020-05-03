@extends('admin_template') 

@push('styles')
	<style>[v-cloak] { display: none; }</style>
	<link href="https://fonts.googleapis.com/css?family=Raleway:300,400,500,700" rel="stylesheet" type="text/css">
	<style>
		.question-container {
			font-family: 'Raleway', sans-serif;
			font-weight: 500;
		}
		.grey-letter {
			color: #424647;
			font-size: 15px;
		}
	</style>
@endpush

@section('content')
<div v-cloak>
	<section class="content-header">
		<a href="{{ url('/admin/results/exam_schedules/' . Request::segment(4) . '/dealers/' . Request::segment(6)) }}" 
		class="btn btn-sm back-button pull-right">
			<i class="fas fa-chevron-left mr-1"></i>
			BACK
		</a>
		<h1 class="sub-editable">
			Detailed Result
		</h1>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-danger shadow-lg shadow-lg">
					<div class="box-header with-border">
						<div class="row">
                            <div class="col-md-8">
								<ol class="breadcrumb" style="padding: 12px">
									<li>
                                        <a href="{{ url('/admin/results/exam_schedules/') }}">
                                            @{{ module_details.module }}
                                        </a>
                                    </li>
									<li>
                                        <a href="{{ url('/admin/results/exam_schedules/' . Request::segment(4) . '/dealers') }}">
											@{{ dealer_details.dealer_name }} @ @{{ dealer_details.branch }}
										</a>
									</li>
									<li>
                                        <a href="{{ url('/admin/results/exam_schedules/' . Request::segment(4) . '/dealers/' . Request::segment(6)) }}">
											List of Trainees
										</a>
									</li>
									<li class="active">
										<i class="fa fa-user"></i>&nbsp;
										@{{ trainee_details.trainee }}
									</li>
								</ol>
							</div>
							<div class="col-md-4">
								<v-text-field class="pull-right" style="width: 300px;" v-model="search" append-icon="search" label="Search" solo hide-details>
								</v-text-field>
							</div>
                        </div>
					</div>
					<div class="box-body">
						<v-container fluid>
							<v-layout row wrap>
								<v-flex>
									<div class="row" style="margin-top: -16px;">
										<div class="col-md-3">
											<div class="info-box" style="background-color: #2D3C42">
												<!-- Apply any bg-* class to to the icon to color it -->
												<span class="info-box-icon bg-red"><i class="far fa-user"></i></span>
												<div class="info-box-content white--text">
													<span class="info-box-text grey--text">Trainee</span>
													<span class="info-box-number">@{{ trainee.lname }}, @{{ trainee.fname }}</span>
												</div>
												<!-- /.info-box-content -->
											</div>
										</div>
										<div class="col-md-3">
											<div class="info-box" style="background-color: #2D3C42">
												<!-- Apply any bg-* class to to the icon to color it -->
												<span class="info-box-icon bg-red"><i class="far fa-file-alt"></i></span>
												<div class="info-box-content white--text">
													<span class="info-box-text grey--text">Module</span>
													<span class="info-box-number">@{{ header.module }}</span>
												</div>
												<!-- /.info-box-content -->
											</div>
										</div>
										<div class="col-md-3">
											<div class="info-box" style="background-color: #2D3C42">
												<!-- Apply any bg-* class to to the icon to color it -->
												<span class="info-box-icon bg-red"><i class="far fa-star"></i></span>
												<div class="info-box-content white--text">
													<span class="info-box-text grey--text">Score</span>
													<span class="info-box-number">@{{ exam_status.score }} / @{{ exam_status.items }}</span>
												</div>
												<!-- /.info-box-content -->
											</div>
										</div>
										<div class="col-md-3">
											<div class="info-box" style="background-color: #2D3C42">
												<!-- Apply any bg-* class to to the icon to color it -->
												<span class="info-box-icon bg-red"><i class="far fa-clock"></i></span>
												<div class="info-box-content white--text">
													<span class="info-box-text grey--text">Timer</span>
													<span class="info-box-number">@{{ header.timer }} min(s)</span>
												</div>
												<!-- /.info-box-content -->
											</div>
										</div>
									</div>
									<v-divider style="margin-top: 6px;"></v-divider>
									<v-expansion-panel popout>
										<v-expansion-panel-content
											class="question-container"
											v-for="(dr, index) in detailed_results"
											:key="dr.trainee_question_id"
											ripple
										>
											<div slot="header" v-on:click="getCorrectAnswer(dr.trainee_question_id)">
												<span class="grey-letter" style="max-width: 200px;">
													<strong>@{{ dr.number }} ).</strong> @{{ dr.question.question }}
												</span>
												<span class="pull-right mr-4">
													<v-icon v-if="dr.is_correct" color="success">fas fa-check-circle</v-icon>
													<v-icon v-else color="red">fas fa-times-circle</v-icon>
												</span>
											</div>
											<v-divider class="my-0"></v-divider>
											<v-card>
												<v-card-text>
													<div class="row">
														<div class="col-md-12">
															<div class="mx-5" style="margin-top: -20px;">
																<v-radio-group 
																	v-model="dr.choice_id" 
																	readonly>
																	<v-radio
																		v-for="(choice, index) in dr.question.choices"
																		:key="choice.choice_id"
																		:label="`${choice.choice_letter}. ${choice.choice}	`"
																		:value="choice.choice_id"
																		:color="`${choice.is_correct == 1 ? 'green' : 'red'}`"
																		class="mt-2">
																	</v-radio>
																</v-radio-group>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<v-chip class="pull-right" color="green" text-color="white">
																<v-avatar class="green darken-4">
																	<strong>
																		@{{ dr.correct_answer }}
																	</strong>
																</v-avatar>
																Correct Answer
															</v-chip>
														</div>
													</div>
												</v-card-text>
											</v-card>
										</v-expansion-panel-content>
									</v-expansion-panel>
								</v-flex>
							</v-layout>
						</v-container>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@push('scripts')
<script src="{{ url('public/js/appCache.js') }}"></script>
<script>
	$('#exam_result_tab').addClass('active bg-red');
	$('#scores_treeview').addClass('active');
	const base_url = "{{ url('/') }}";

	var app = new AppCache;

	var vm = new Vue({
		el: '#app',
		data() {
			return {
				header: {},
				trainee: {},
				detailed_results: [],
				correct_letter: '',
				exam_status: {},
                module_details: {},
                dealer_details: {},
				trainee_details: {}
			}
		},
		mounted() {
			this.fetchDetailedResult('{{ Request::segment(4) }}', '{{ Request::segment(8) }}');
			this.fetchHeader('{{ Request::segment(4) }}');
			this.fetchTrainee('{{ Request::segment(8) }}');
			this.fetchExamStatus('{{ Request::segment(4) }}', '{{ Request::segment(6) }}', '{{ Request::segment(8) }}');

			this.module_details = JSON.parse(app.getItem('module_details'))
			this.dealer_details = JSON.parse(app.getItem('dealer_details'))
			this.trainee_details = JSON.parse(app.getItem('trainee_details'))
		},
		methods: {
			fetchHeader: function(exam_schedule_id) {
				return axios.get(`${base_url}/admin/results/header/${exam_schedule_id}`)
					.then(({data}) => {
						this.header = data;
					})
					.catch((err) => {
						console.log(err.response);
					});
			},
			fetchTrainee: function(trainee_id) {
				return axios.get(`${base_url}/admin/results/trainee/${trainee_id}`)
					.then(({data}) => {
						this.trainee = data;
					})
					.catch((err) => {
						console.log(err.response);
					});
			},
			fetchExamStatus: function(exam_schedule_id, dealer_id, trainee_id) {
				return axios.get(`${base_url}/admin/results/exam_status/${exam_schedule_id}/${dealer_id}/${trainee_id}`)
					.then(({data}) => {
						this.exam_status = data;
					})
					.catch((err) => {
						console.log(err.response);
					});
			},
			fetchDetailedResult: function(exam_schedule_id, trainee_id) {
				return axios.get(`${base_url}/admin/results/detailed_result/${exam_schedule_id}/${trainee_id}`)
					.then(({data}) => {
						this.detailed_results = data;
					})
					.then(() => {
						this.detailed_results.forEach((element, index) => {
							element.question.choices.forEach((el, i) => {
								if (el.is_correct == 1) this.$set(this.detailed_results[index], 'correct_answer', el.choice_letter)
							});
						});
						this.loading = false
					})
					.catch((err) => {
						console.log(err);
					});
			}
		}
	});
</script>
@endpush