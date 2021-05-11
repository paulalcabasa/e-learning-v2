@extends('layouts.app')

@push('styles')
	<style>
		[v-cloak] { display: none; }
		.card-container {
			min-height: 500px;
		}
	</style>
@endpush

@section('content')
<v-container>
	<v-layout row wrap>
		<v-flex xs12 sm12 md12>
			<v-toolbar color="grey darken-2" dark>
				<v-toolbar-title>
					List of Examinations
				</v-toolbar-title>
			</v-toolbar>
			
			<v-card class="card-container">
				<v-container
					fluid
					grid-list-lg>

					<div v-cloak v-if="exams.length > 0">
						<v-layout row wrap justify-center>
							<v-flex xs12 sm6 md10 v-for="(data, index) in exams" :key="data.index">
								<v-card color="grey lighten-5" hover>
									<v-card-title class="pt-2 pb-1" primary-title>
										<div>
											<h3 class="headline font-weight-medium mb-0">
												@{{ data.module }}
											</h3>
											<div class="grey--text">
												@{{ data.description }}
											</div>
											<v-spacer></v-spacer>
										</div>
									</v-card-title>

									<v-divider></v-divider>

									<v-card-title class="pb-0">
										<div>
											<p style="color: grey;">
												Time: <strong><span class="green--text">@{{ data.timer }}</span> minute(s)</strong><br>
												<v-divider class="my-2"></v-divider>
												Item(s): <strong><span class="green--text">@{{ data.items }}</span> question(s)</strong><br>
												<v-divider class="my-2"></v-divider>
												Valid Until: 
												<v-chip 
													color="green darken-1" 
													text-color="white" small>
													@{{ data.end_date | dateFormat }}
												</v-chip>
											</p>
										</div>
									</v-card-title>
										
									<v-card-actions>
										<v-spacer></v-spacer>
										<v-btn 
											v-on:click="initiateExam(data.exam_detail_id)"
											color="red"
											small
											ripple
											dark
											:disabled="has_noConnection"
											>
											<v-icon class="mr-1" small>create</v-icon>
											Take exam
										</v-btn>
									</v-card-actions>
								</v-card>
							</v-flex>
						</v-layout>
					</div>
					<div v-else>
						<v-layout row wrap justify-center>
							<div style="margin-top: 200px;">
								<h3 class="red--text font-weight-medium">
									<!-- <v-icon color="red">fas fa-sad-tear</v-icon>  -->
									@{{ message }}
								</h3>
							</div>
						</v-layout>
					</div>
				</v-container>
			</v-card>
		</v-flex>
	</v-layout>

	<!-- Instruction Dialog -->
	<div class="text-xs-center">
		<v-dialog
			v-model="instructionDialog"
			width="700"
			persistent>
	
			<v-card>
				<v-card-title
					class="headline red lighten-1 white--text"
					primary-title
				>
					<i class="fa fa-exclamation-circle"></i>&nbsp;
					General Instruction
				</v-card-title>

				<v-list>
					<template>
						<v-subheader class="ml-4">
							1. Please surrender your mobile phone to your instructor/coordinator before taking the examination.
						</v-subheader>
						<v-divider></v-divider>
						<v-subheader class="ml-4">
							2. Read carefully the the questions before answering.
						</v-subheader>
						<v-divider></v-divider>
						<v-subheader class="ml-4">
							3. Kindly ask your instructor/proctor for any clarifications and concerns regarding your exam.
						</v-subheader>
						<v-divider></v-divider>
						<v-subheader class="ml-4">
							4. Please verify your answer per question before submitting and kindly inform your instructor/proctor if done, for final instruction.
						</v-subheader>
					</template>
				</v-list>
		
				<v-divider></v-divider>
		
				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn
						color="red"
						flat
						v-on:click="instructionDialog = false"
					>
						Cancel
					</v-btn>
					<v-btn
						color="green"
						flat
						v-on:click="goToExam(exam_detail_id)"
					>
						Start Exam
					</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
	</div>
</v-container>
@endsection

@push('scripts')
<script>
	const TRAINEE_ID = "{{ str_replace_last('trainee_', '', Auth::user()->app_user_id) }}";
	const base_url = "{{ url('/') }}";
	
	new Vue({
		el: '#app',
		data() {
			return {
				instructionDialog: false,
				exams: [],
				exam_detail_id: 0,
				has_noConnection: false,
				message : 'Please wait while we load your exam'
			}
		},
		watch: {
			// watch connection: app_hasConnection status
			app_hasConnection: function() {
				if (!this.app_hasConnection) return this.has_noConnection = true;
				return this.has_noConnection = false;
			}
		},
		created() {
			this.getExamList(TRAINEE_ID);
		},
		methods: {
			getExamList: function(trainee_id) {
				var self = this;
				axios.get(`${base_url}/trainee/list_of_exams/get/${trainee_id}`)
				.then(({data}) => {
					this.exams = data;
					if(data.length == 0){
						self.message = "You have no scheduled exam for today.";
					}
				})
				.catch((err) => {
					alert("Failed loading the exam, please refresh the page.");
					console.log(err.response);
				});
			},
			initiateExam: function(exam_detail_id) {
				this.exam_detail_id = exam_detail_id;
				this.instructionDialog = true;
			},
			goToExam: function(exam_detail_id) {
				this.instructionDialog = false
				window.location.href = `${base_url}/trainee/exam/${exam_detail_id}`
			}
		}
	})
</script>
@endpush
