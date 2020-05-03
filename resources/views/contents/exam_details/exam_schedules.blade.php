@extends('admin_template') 

@push('styles')
	<style>
		[v-cloak] { display: none; }
	</style>
	<link rel="stylesheet" href="{{ url('public/libraries/bootstrap-switch.min.css') }}">
@endpush

@section('content')
<div v-cloak>
	<section class="content-header">
		<h1>
			Exam Schedules 
		</h1>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-danger shadow-lg">
					<div class="box-header with-border">
						<div class="row">
							<div class="col-md-6">
								<v-btn href="{{ url('/admin/exam_schedules/create') }}" small class="bg-red" dark>
									Add New Schedule
								</v-btn>
							</div>
							<div class="col-md-6">
								<v-text-field
									class="pull-right"
									style="width: 300px;"
									v-model="search"
									append-icon="search"
									label="Search"
									solo
									hide-details>
								</v-text-field>
							</div>
						</div>
					</div>
					<div class="box-body">
						<v-data-table class="table table-bordered"
                            :headers="headers"
                            :items="exam_schedules"
                            :search="search"
							:disable-initial-sort="true"
							:rows-per-page-items="rows_per_page_items"
                            :loading="loading"
                            class="elevation-1">

                            <template slot="headerCell" slot-scope="props">
                                <v-tooltip bottom>
                                    <span slot="activator">
                                        @{{ props.header.text }}
                                    </span>
                                    <span>
                                        @{{ props.header.text }}
                                    </span>
                                </v-tooltip>
                            </template>
                            
                            <template slot="items" slot-scope="props">
                                <tr>
                                    <td><strong>@{{ props.item.module.module }}</strong></td>
                                    <td>@{{ props.item.created_by }}</td>
                                    <td>@{{ props.item.created_at }}</td>
                                    <td>
                                        <div v-if="props.item.status == 'completed'">
                                            <label class="label label-success text-lg py-2 px-2">
                                                Completed
                                            </label>
                                        </div>
                                        <div v-else-if="props.item.status == 'on_going'">
                                            <label class="label label-info py-2 px-2">
                                                On-going
                                            </label>
                                        </div>
									</td>
									<td class="text-center">
										<v-menu transition="slide-y-transition" min-width="200" bottom>
											<v-btn
												slot="activator"
												color="green"
												dark
												small
											>
												ACTION
											</v-btn>
											<v-list>
												<v-list-tile v-on:click="openFile(props.item.exam_schedule_id)">
													<v-list-tile-title>
														<v-icon small color="warning">fa fa-folder-open</v-icon>&nbsp;
														View Dealer schedules
													</v-list-tile-title>
												</v-list-tile>
												<v-divider class="mt-0 mb-0"></v-divider>
												<v-list-tile v-on:click="editTimer(props.item.exam_schedule_id)">
													<v-list-tile-title>
														<v-icon small color="primary">far fa-clock</v-icon>&nbsp;
														Timer
													</v-list-tile-title>
												</v-list-tile>
												<v-list-tile v-on:click="editPassingScore(props.item.exam_schedule_id)">
													<v-list-tile-title>
														<v-icon small color="primary">fas fa-flag-checkered</v-icon>&nbsp;
														Passing Score
													</v-list-tile-title>
												</v-list-tile>
												<v-list-tile v-on:click="getExamSchedule(props.item.exam_schedule_id)">
													<v-list-tile-title>
														<v-icon small color="primary">fas fa-edit</v-icon>&nbsp;
														Open Submodules
													</v-list-tile-title>
												</v-list-tile>
												<v-list-tile v-on:click="deleteFile(props.item.exam_schedule_id, props.index)">
													<v-list-tile-title>
														<v-icon small color="red">fas fa-trash-alt</v-icon>&nbsp;
														Delete Schedule
													</v-list-tile-title>
												</v-list-tile>
											</v-list>
										</v-menu>
									</td>
                                </tr>
                            </template>
                        </v-data-table>	
					</div>
				</div>
			</div>
		</div>
	</section>

	{{-- Modal --}}
	<div v-if="isModalShown">
		<div class="modal" style="display: block">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button v-on:click="isModalShown = false" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>
					
					<div class="modal-body vue-modal">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th class="text-center" width="50px">#</th>
									<th>Submodules</th>
									<th class="text-center" width="50px">Quantity</th>
									<th class="text-center" width="50px">Include</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(submodule, index) in submodules">
									<td class="text-center">@{{ index + 1 }}</td>
									<td class="text-primary">@{{ submodule.sub_module }}</td>
									<td class="text-xs-center">
										<input type="text" 
										v-model="submodule.items"
										v-on:change="countPickedSubmodules"
										class="form-control text-xs-center" 
										:disabled="submodule.isSelected ? false : true">
										{{-- <v-text-field 
											color="green"
											:disabled="submodule.isSelected ? false : true"
											v-on:change="countPickedSubmodules"
											v-model="submodule.items" justify-right>
										</v-text-field> --}}
									</td>
									<td class="text-xs-center">
										{{-- <v-checkbox 
											v-model="submodule.isSelected" color="green"
											hide-details>
										</v-checkbox> --}}
										<input v-model="submodule.isSelected" type="checkbox" name="my-checkbox" checked>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="modal-footer">
						<v-chip color="green" text-color="white">
							<v-avatar class="green darken-4">@{{ total_items }}</v-avatar>
							Total Items
						</v-chip>
						<v-btn 
							v-on:click="onUpdate"
							class="bg-red" 
							small 
							dark>
						Save Changes
						</v-btn>
					</div>
				</div>
			</div>
		</div>
	</div>
	{{-- end modal --}}

	{{-- Timer thru dialog --}}
	<v-dialog v-model="dialog" persistent max-width="290">
		<v-card>
				<v-card-text>
					<v-text-field
						class="mt-3"
						label="Timer in minute(s)"
						v-model="exam_schedule.timer"
						box
						clearable
						hide-details autofocus>
					</v-text-field>
				</v-card-text>
				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn color="green darken-1" flat v-on:click="dialog = false">Close</v-btn>
					<v-btn color="green darken-1" flat v-on:click="saveTimer(exam_schedule.timer)">Save</v-btn>
				</v-card-actions>
		</v-card>
	</v-dialog>

	<v-dialog v-model="passingScoreDialog" persistent max-width="290">
		<v-card>
				<v-card-text>
					<v-text-field
						class="mt-3"
						label="Passing Score"
						v-model="exam_schedule.passing_score"
						box
						hide-details autofocus>
					</v-text-field>
				</v-card-text>
				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn color="green darken-1" flat v-on:click="passingScoreDialog = false">Close</v-btn>
					<v-btn color="green darken-1" flat v-on:click="savePassingScore(exam_schedule.passing_score)">Save</v-btn>
				</v-card-actions>
		</v-card>
	</v-dialog>
	{{-- end dialog --}}
</div>
@endsection

@push('scripts')
<script src="{{ url('public/libraries/bootstrap-switch.min.js') }}"></script>
<script>
	$('#exam_schedule_tab').addClass('active bg-red');
	$('#exams_treeview').addClass('active');
	const base_url = "{{ url('/') }}";

	new Vue({
		el: '#app',
		data() {
			return {
				dialog: false,
				passingScoreDialog: false,
				isModalShown: false,
				loading: true,
                search: '',
				headers: [
					{ text: 'Module', value: 'module' },
					{ text: 'Created By', value: 'created_by' },
					{ text: 'Created At', value: 'created_at' },
					{ text: 'Exam Status', value: 'status', sortable: false },
					{ text: 'Actions', value: '', align: 'center', sortable: false },
				],
				exam_schedules: [],
				exam_schedule: {},
				rows_per_page_items: [10, 30, 50, {"text":"$vuetify.dataIterator.rowsPerPageAll","value":-1}],
				submodules: [],
				total_items: '0',
			}
		},
		mounted() {
			this.ExamSchedules();
		},
		methods: {
			ExamSchedules: function() {
				axios.get(`${base_url}/admin/exam_schedules/get`)
				.then(({data}) => {
					this.exam_schedules = data.exam_schedules;
                    setTimeout(() => {
						this.loading = false
					}, 1000)
				})
				.catch((err) => {
					console.log(err.response);
				});
			},
			openSubmodules: function(module_id) {
				axios.get(`${base_url}/admin/submodules/get/${module_id}`)
                .then(({data}) => {
					this.submodules = [];
					this.total_items = 0;

                    this.submodules = data.submodules;
					this.isModalShown = true;

					// Merge Submodule Object from QuestionDetails to plain submodule
					var sum = 0;
					this.exam_schedule.question_details.forEach(el1 => {
						this.submodules.forEach(el2 => {
							if (el2.sub_module_id == el1.sub_module_id) {
								el2['items'] 	  		  = el1.items;
								el2['isSelected']         = true;
								el2['method']             = 'PUT';
								el2['question_detail_id'] = el1.question_detail_id;
								if (el2.isSelected) sum += parseFloat(el2.items);
							}
						});
						if (!isNaN(sum)) return this.total_items = sum;
					});
                })
                .catch((err) => {
                    console.log(err.response);
                });
			},
			getExamSchedule: function(exam_schedule_id) {
				axios.get(`${base_url}/admin/exam_schedules/get/${exam_schedule_id}`)
                .then(({data}) => {
					this.openSubmodules(data.module_id);
					this.exam_schedule = data;
                })
                .catch((err) => {
                    console.log(err.response);
                });
			},
			countPickedSubmodules: function() {
                var sum = 0;
                this.submodules.forEach(el => {
                    if (el.isSelected) sum += parseFloat(el.items);
                });
                
                if (!isNaN(sum)) return this.total_items = sum;
            },
            openFile: function(exam_schedule_id) {
                window.location = `${base_url}/admin/exam_schedule_id/${exam_schedule_id}/exam_details`;
			},
			onUpdate: function() {
				this.submodules.forEach(el => {
					el.exam_schedule_id = this.exam_schedule.exam_schedule_id
				});
				
				let data = this.submodules;
				axios.post(`${base_url}/admin/question_detail/post`, data)
				.then(({data}) => {
					this.submodules;
					this.message('Successfully Saved');
				})
				.catch((err) => {
					console.log(err.response);
				});
			},
			editTimer: function(exam_schedule_id) {
				this.dialog = true;
				axios.get(`${base_url}/admin/exam_schedules/get/${exam_schedule_id}`)
                .then(({data}) => {
					this.exam_schedule = data;
                })
                .catch((err) => {
                    console.log(err.response);
                });
			},
			saveTimer: function(timer) {
				if (isNaN(timer)) return swal("Timer must be a proper time!", "", "error");
				axios.put(`${base_url}/admin/exam_schedules/update_timer/${this.exam_schedule.exam_schedule_id}`, {timer:timer})
                .then(({data}) => {
					this.dialog = false;
					this.message('Successfully saved!');
                })
                .catch((err) => {
                    console.log(err.response);
                });
			},
			editPassingScore: function(exam_schedule_id) {
				this.passingScoreDialog = true;
				axios.get(`${base_url}/admin/exam_schedules/get/${exam_schedule_id}`)
                .then(({data}) => {
					this.exam_schedule = data;
                })
                .catch((err) => {
                    console.log(err.response);
                });
			},
			savePassingScore: function(passing_score) {
				if (isNaN(passing_score)) return swal("Passing Score must be a number!", "", "error");
				axios.put(`${base_url}/admin/exam_schedules/update_passing_score/${this.exam_schedule.exam_schedule_id}`, {passing_score:passing_score})
                .then(({data}) => {
					this.passingScoreDialog = false;
					this.message('Successfully saved!');
                })
                .catch((err) => {
                    console.log(err.response);
                });
			},
			deleteFile: function(exam_schedule_id, index) {
				swal({
					title: "Delte this Schedule?",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete) {
						axios.delete(`${base_url}/admin/exam_schedules/delete/${exam_schedule_id}`)
						.then(({data}) => {
							this.exam_schedules.splice(index, 1);
							this.message('Successfully Deleted');
						})
						.catch((err) => {
							console.log(err.response);
						});
					}
				});
			}
		}
	});

	//iCheck for checkbox and radio inputs
	$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
		checkboxClass: 'icheckbox_minimal-blue',
		radioClass   : 'iradio_minimal-blue'
	});
	//Red color scheme for iCheck
	$('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
		checkboxClass: 'icheckbox_minimal-red',
		radioClass   : 'iradio_minimal-red'
	});
	//Flat red color scheme for iCheck
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		radioClass   : 'iradio_flat-green'
	});

	$("[name='my-checkbox']").bootstrapSwitch();
</script>
@endpush