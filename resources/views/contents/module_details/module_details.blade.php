@extends('admin_template')

@push('styles')
	<style>[v-cloak] { display: none; }</style>
@endpush

@section('content')
<div v-cloak>
	<section class="content-header">
		<h1>
			Module Scheduler
		</h1>
	</section>
	<section class="content">
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
								href="{{ url("/admin/module_schedules") }}"
								class="pull-right bg-red"
								small>
								<v-icon class="text-sm" style="margin-right: 5px;">fas fa-arrow-left</v-icon>
								Back
							</v-btn>
						</v-toolbar>
					</div>
					<div class="box-body">
						<v-data-table
							:headers="dealer_headers"
							:items="dealers_schedule"
							:disable-initial-sort="true"
							:loading="loading"
							hide-actions
							class="elevation-1">

							<template slot="items" slot-scope="props">
								<td>
									<label :class="`label label-${ props.item.is_finished ? 'success' : 'warning' } py-2 px-2`">
										@{{ props.item.is_finished ? 'DONE READING' : 'NOT YET' }}
									</label>
								</td>
								<td><strong>@{{ props.item.dealer_name }}</strong></td>
								<td>@{{ props.item.branch }}</td>
								
								<td class="text-xs-center">
									<input type="date" class="form-control" 
									v-model="props.item.start_date"
									id="start_date"
									v-on:change="updateTriggered">
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

								<td>
									<v-switch 
										v-model="props.item.is_enabled"
										v-on:change="updateTriggered">
									</v-switch>
								</td>
							</template>
						</v-data-table>
						<div class="row">
							<div class="col-md-12">
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
<script src="{{ url('public/js/crud.js') }}"></script>
<script>
	$('#module_schedule_tab').addClass('active bg-red');
	$('#exams_treeview').addClass('active');
	const base_url = "{{ url('/') }}";
	
	new Vue({
		el: '#app',
		data() {
			return {
				loading: boolean = true,
				isHidden: true,
				module_schedule_id: '{{ Request::segment(3) }}',
				module: {},
				dealers_schedule: [],	
				dealer_headers: [
					{ text: 'Viewing Status', value: '' },
					{ text: 'Dealer', value: 'dealer_name' },
					{ text: 'Branch', value: 'branch' },
					{ text: 'Start Date', value: 'start_date', align: 'left' },
					{ text: 'End Date', value: 'end_date', align: 'left' },
					{ text: 'Status', value: 'status', align: 'left' },
					{ text: 'Enabled', value: 'is_enabled' },
				],
				disableToSave: false
			}
		},
		mounted() {
			this.getModuleSchedule(this.module_schedule_id);
			this.getModuleDetails(this.module_schedule_id);
		},
		methods: {
			getModuleDetails: function(module_schedule_id) {
				axios.get(`${base_url}/admin/dealers_schedule/get/${module_schedule_id}`)
				.then(({data}) => {
					this.dealers_schedule = data.dealers_schedule;
					setTimeout(() => {
						this.loading = false
					}, 1000)
				})
				.catch((err) => {
					console.log(err.response);
				});
			},

			getModuleSchedule(module_schedule_id) {
				axios.get(`${base_url}/admin/module_schedules/get/${module_schedule_id}`)
				.then(({data}) => {
					this.getModule(data.module_id);
				})
				.catch((err) => {
					console.log(err.response);
				});
			},

			getModule(module_id) {
				axios.get(`${base_url}/admin/get_module/${module_id}`)
				.then(({data}) => {
					this.module = data;
				})
				.catch((err) => {
					console.log(err.response);
				});
			},
			
			updateTriggered: function(event) {
				this.isHidden = false;

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

			onSave: function() {
				let postData = {};
				this.dealers_schedule.forEach(element => {
					element._token = '{{ csrf_token() }}';
					element._method = 'POST';
					element.module_id = this.module.module_id;
				});

                // Request to be sent merged
                postData = {
                    dealer_schedules: this.dealers_schedule,
                    module_schedule: ''
				}
				
				axios.post(`${base_url}/admin/module_details/post`, postData)
				.then(({data}) => {
					toastr.success('', 'Successfully saved!');
					this.getModuleDetails(this.module_schedule_id);
				})
				.catch((err) => {
					console.log(err.response);
					if (err.response) this.message('Something went wrong, please try again.')
				});
			},

			resetSchedule: function(module_detail_id) {
				if (module_detail_id) {
					swal({
						title: "Reset this Schedule?",
						icon: "warning",
						buttons: true,
						dangerMode: true,
					})
					.then((willDelete) => {
						if (willDelete) {
							axios.delete(`${base_url}/admin/module_details/delete/${module_detail_id}`)
							.then(({data}) => {
								toastr.success('', 'Successfully deleted!');
								this.getModuleDetails(this.module_schedule_id);
							})
							.catch((err) => {
								console.log(err.response);
							});
						}
					});
				}
			}
		}
	});
</script>
@endpush