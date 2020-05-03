@extends('admin_template') 

@push('styles')
	<style>
		[v-cloak] { display: none; }
	</style>
@endpush

@section('content')
@verbatim
<div v-cloak>
<section class="content-header">
		<h1>
			Module Scheduler
			<small>Optional description</small>
		</h1>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-danger">
					<div class="box-header with-border">
						<h3 class="box-title">
							<a :href="paths.back" class="btn btn-xs btn-danger">
								<i class="fas fa-chevron-left fa-xs"></i>
								Back
							</a>
							{{ module.module }}
						</h3>
					</div>
					<div class="box-body">
						<v-data-table
							:headers="dealer_headers"
							:items="dealers"
							:disable-initial-sort="true"
							:loading="loading"
							hide-actions
							class="elevation-1">

							<template slot="items" slot-scope="props">
								<td>{{ props.index + 1 }}</td>
								<td>{{ props.item.dealer_name }}</td>
								<td>{{ props.item.branch }}</td>
								
								<td class="text-xs-center">
									<input type="date" class="form-control" 
									v-model="props.item.start_date"
									@change="updateTriggered()">
								</td>
								<td class="text-xs-center">
									<input type="date" class="form-control" 
									v-model="props.item.end_date" 
									@change="updateTriggered()">
								</td>
								<td class="text-xs-center" width="10px">
									<input type="text" class="form-control" 
									v-model="props.item.timer" 
									@change="updateTriggered()">
								</td>
								<td class="text-center">
									<a @click="getSubModules" class="btn">
										<i class="fas fa-folder-open text-yellow"></i>
									</a>
								</td>
							</template>
						</v-data-table>
						<div class="row">
							<div class="col-md-12">
								<a @click="onSave()" class="btn btn-sm btn-primary pull-right" style="margin-top: 10px;" :hidden="isHidden">
									Save Changes
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div v-if="isModalShown">
			<div class="modal" style="display: block">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button @click="isModalShown = false" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">
								Submodules ( )
							</h4>
						</div>
						
						<div class="modal-body vue-modal">
							<table class="table table-bordered table-hover">
								<thead>
									<tr>
										<th class="text-center" width="50px">#</th>
										<th>Submodules</th>
										<th class="text-center" width="50px">Quantity</th>
										<th class="text-center" width="50px">&nbsp;</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(submodule, index) in submodules">
										<td class="text-center">{{ index + 1 }}</td>
										<td class="text-primary">{{ submodule.sub_module }}</td>
										<td>
											<v-text-field 
											v-if="submodule.isSelected" 
											v-model="submodule.items"
											v-model="value"></v-text-field>
										</td>
										<td class="text-center">
											<v-checkbox 
											v-model="submodule.isSelected" color="red"
											hide-details></v-checkbox>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div class="modal-footer">
							<button @click="getSubmodulesToSave" class="btn btn-sm btn-success">Save</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endverbatim
@endsection

@push('scripts')
<script>
	$('#exam_schedule_tab').addClass('active bg-red');
	const base_url = "{{ url('/') }}";
	const exam_schedule_id = window.location.pathname.split( '/' )[3];

	new Vue({
		el: '#app',
		data() {
			return {
				isModalShown: boolean = false,
				loading: boolean = true,
				isHidden: boolean = true,
				module: object = {},
				dealers: array = [],
				dealers_schedule: array = [],
				dealer_headers: array = [
					{ text: '#', value: '#', sortable: false },
					{ text: 'Dealer', value: 'dealer_name' },
					{ text: 'Branch', value: 'branch' },
					{ text: 'Start Date', value: 'start_date', align: 'left' },
					{ text: 'End Date', value: 'end_date', align: 'left' },
					{ text: 'Timer', value: 'timer' },
					{ text: 'Questions', value: 'questions', align: 'center', sortable: false },
				],
				submodules: array = [],
				submodule_to_save: array = [],
				paths: object = {
					back: `${base_url}/admin/exam_schedule_id/${exam_schedule_id}/exam_details`
				}
			}
		},
		mounted() {
			this.getModuleSchedule(exam_schedule_id);
			this.getDealersModule(exam_schedule_id);
		},
		methods: {
			getModules: function(exam_schedule_id) {
				axios.get(`${base_url}/admin/get_module/${exam_schedule_id}`)
				.then(({data}) => {
					this.module = data;
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

			getModuleSchedule(exam_schedule_id) {
				axios.get(`${base_url}/admin/module_schedules/get/${exam_schedule_id}`)
				.then(({data}) => {
					this.getModule(data.module_id);
				})
				.catch((err) => {
					console.log(err.response);
				});
			},

			getDealersModule: function(exam_schedule_id) {
				axios.get(`${base_url}/admin/dealers/get`)
				.then(({data}) => {
					this.dealers = data.dealers;

					this.dealers.forEach(el => {
						el.start_date = '';
						el.end_date = '';
					});

					setTimeout(() => {
						this.loading = false
					}, 1000)
				})
				.catch((err) => {
					console.log(err.response);
				});
			},

			getSubModules: function() {
				axios.get(`	${this.module.module_id}`)
				.then(({data}) => {
					this.submodules = data.submodules;
					this.isModalShown = true;
				})
				.catch((err) => {
					console.log(err.response);
				});
			},

			onSave: function() {
				this.dealers.forEach(el => {
					el._token = '{{ csrf_token() }}';
					el._method = 'POST';
					el.exam_schedule_id = exam_schedule_id;
				});

				this.submodules.forEach(el => {
					if (el.isSelected) {
						// this.submodule_to_save.push(el);
						this.submodule_to_save = el;
						this.submodule_to_save.exam_schedule_id = exam_schedule_id;
					}
				});

				let data = {
					dealers: this.dealers,
					submodules: this.submodules
				};

				axios.post(`${base_url}/admin/exam_details/post`, data)
				.then(({data}) => {
					console.log(data);
					// toastr.success('', 'Successfully saved!');
					// this.getDealersModule(exam_schedule_id);
				})
				.catch((err) => {
					console.log(err.response);
				});
			},

			// alalalalala
			getSubmodulesToSave() {
				this.submodules.forEach(el => {
					if (el.isSelected) {
						this.submodule_to_save.push(el);
						// this.submodule_to_save = el;
						// this.submodule_to_save.exam_schedule_id = exam_schedule_id;
					}
				});
				console.log(Object.assign({}, this.submodule_to_save));
			},
			
			updateTriggered: function() {
				this.isHidden = false;
			}
		}
	});
</script>
@endpush