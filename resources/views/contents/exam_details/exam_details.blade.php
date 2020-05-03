@extends('admin_template') 

@push('styles')
	<style>[v-cloak] { display: none; }</style>
@endpush

@section('content')
<div v-cloak>
	<section class="content-header">
		<h1>
			Exam Schedules
		</h1>
	</section>

	<section class="content container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-danger shadow-lg">
					<div class="box-header with-border">
						<v-toolbar flat color="white">
							<v-toolbar-title class="grey--text">Module</v-toolbar-title>
							<v-divider
								class="mx-2"
								inset
								vertical
							></v-divider>
							<v-toolbar-title class="ml-0">@{{ module.module }}</v-toolbar-title>
							<v-spacer></v-spacer>
							<v-btn 
								href="{{ url('/admin/exam_schedules') }}"
								class="pull-right bg-red"
								small>
								<v-icon class="text-sm" style="margin-right: 5px;">fas fa-arrow-left</v-icon>
								Back
							</v-btn>
						</v-toolbar>
					</div>
					<div class="box-body">
                        <v-data-table
							:headers="headers"
							:items="exam_details"
							:disable-initial-sort="true"
							:loading="loading"
							hide-actions
							class="elevation-1">

							<template slot="items" slot-scope="props">
								<td class="subheading info--text">
									<strong>
										<div class="label label-warning py-2 px-2">
											@{{ props.item.completed_exam }} / @{{ props.item.trainees }}
										</div>
									</strong>
								</td>
								<td><strong>@{{ props.item.dealer_name }}</strong></td>
								<td>@{{ props.item.branch }}</td>
								
								<td class="text-xs-center">
									<input type="date" class="form-control" 
									v-model="props.item.start_date"
									v-on:change="updateTriggered" id="start_date">
								</td>
								<td class="text-xs-center">
									<input type="date" class="form-control" 
									v-model="props.item.end_date"
									v-on:change="updateTriggered">
								</td>
								<td>
									{{-- Label must be optimize --}}
									<div v-if="props.item.status == 'waiting'">
										<label class="label label-warning py-2 px-2">
											Waiting
										</label>
									</div>
									<div v-else-if="props.item.status == 'on_progress'">
										<label v-if="props.item.is_opened" class="label label-info py-2 px-2" style="margin-right: 2px;">
											Opened
										</label>
										<label v-else class="label label-danger py-2 px-2" style="margin-right: 2px;">
											Unopened
										</label>

										<label class="label label-success py-2 px-2">
											On Progress
										</label>
									</div>
									<div v-else-if="props.item.status === 'ended'">
										<label v-if="props.item.is_opened" class="label label-info py-2 px-2" style="margin-right: 2px;">
											Opened
										</label>
										<label v-else class="label label-danger py-2 px-2" style="margin-right: 2px;">
											Unopened
										</label>

										<label class="label label-danger py-2 px-2">
											Ended
										</label>
									</div>
									{{--  --}}
								</td>

								<td class="text-center">
									<v-switch 
										v-model="props.item.is_enabled"
										v-on:change="updateTriggered" color="green">
									</v-switch>
								</td>
							</template>
						</v-data-table>
						<div class="row">
							<div class="col-md-12">
								{{-- <v-btn 
									style="width: 200px;"
									small 
                                    class="bg-red pull-right mt-3"
                                    dark
									v-on:click="onSave()"
									v-if="forUpdate">
									Save Changes
								</v-btn> --}}
								<v-btn v-on:click="onSave" class="pull-right" small color="success" v-bind:disabled="disableToSave">
									Save Changes
								</v-btn>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@push('scripts')
<script>
	$('#exam_schedule_tab').addClass('active bg-red');
	$('#exams_treeview').addClass('active');
	const base_url = "{{ url('/') }}";

	new Vue({
		el: '#app',
		data: function() {
			return {
				isModalShown: boolean = false,
				loading: boolean = true,
				search: string = '',
				forUpdate: boolean = false,
				exam_schedule_id: string = '{{ Request::segment(3) }}',
				module: {},
				headers: [
					{ text: 'Completed/Trainees', value: '', sortable: false },
					{ text: 'Dealer', value: 'module' },
					{ text: 'Branch', value: 'branch' },
					{ text: 'Start Date', value: 'start_date' },
					{ text: 'End Date', value: 'end_date' },
					{ text: 'Status', value: 'status' },
					{ text: 'Enabled', value: 'is_enabled' },
				],
				exam_details: [],
				submodules: array = [],
				disableToSave: false
			}
		},
		mounted: function() {
			this.getExamSchedule(this.exam_schedule_id);
			this.getExamDetails(this.exam_schedule_id);
		},
		methods: {
			getExamDetails: function(exam_schedule_id) {
				axios.get(`${base_url}/admin/dealers_exam_schedule/get/${exam_schedule_id}`)
				.then(({data}) => {
					this.exam_details = data.dealers_schedule;
                    setTimeout(() => {this.loading = false}, 1000)
				})
				.catch((err) => {
					console.log(err.response);
				});
			},
			getExamSchedule: function(exam_schedule_id) {
				axios.get(`${base_url}/admin/exam_schedules/get/${exam_schedule_id}`)
				.then(({data}) => {
					this.getModule(data.module_id);
				})
				.catch((err) => {
					console.log(err.response);
				});
			},
			getModule: function(module_id) {
				axios.get(`${base_url}/admin/get_module/${module_id}`)
				.then(({data}) => {
					this.module = data;
				})
				.catch((err) => {
					console.log(err.response);
				});
			},
			onSave: function() {
				this.exam_details.forEach(el => {
					el._token = '{{ csrf_token() }}';
					el._method = 'POST';
				});
				
				axios.post(`${base_url}/admin/exam_details/post`, this.exam_details)
				.then(({data}) => {
					this.forUpdate = false;
					toastr.success('', 'Successfully saved!');
					this.getExamDetails(this.exam_schedule_id);
				})
				.catch((err) => {
					console.log(err.response);
				});
			},
			getSubModules: function(exam_detail_id) {
				axios.get(`/admin/submodules/get/${this.module.module_id}`)
				.then(({data}) => {
					this.submodules = data.submodules;
					this.isModalShown = true;
				})
				.catch((err) => {
					console.log(err.response);
				});
			},
			viewQuestions: function(exam_detail_id) {
				this.isModalShown = true;
			},
            openFile: function(exam_schedule_id) {
                window.location = `${base_url}/admin/exam_schedule_id/${exam_schedule_id}/exam_details`;
			},
			updateTriggered: function(event) {
				this.forUpdate = true;
				var start_date = new Date(document.querySelector('#start_date').value);
                var end_date = '';
                if (start_date) {
                    end_date = new Date(event.target.value);
                    
                    if (start_date > end_date) {
                        this.disableToSave = true;
                        console.log('End date cannot be lower than start date');
                        swal('Ooops!', 'End date must ahead or equal to start date', 'error');
                    }
                    else {
                        this.disableToSave = false;
                    }
                }
			},
			resetSchedule: function(exam_detail_id) {
				if (exam_detail_id) {
					swal({
						title: "Reset this Schedule?",
						icon: "warning",
						buttons: true,
						dangerMode: true,
					})
					.then((willDelete) => {
						if (willDelete) {
							axios.delete(`${base_url}/admin/exam_details/delete/${exam_detail_id}`)
							.then(({data}) => {
								toastr.success('', 'Successfully deleted!');
								this.getExamDetails();
							})
							.catch((err) => {
								console.log(err.response);
							});
						}
					});
				}
			},
		}
	});
</script>
@endpush