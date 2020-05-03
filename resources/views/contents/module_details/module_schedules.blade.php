@extends('admin_template') 

@push('styles')
	<style>
		[v-cloak] { display: none; }
	</style>
	<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endpush

@section('content')
<div v-cloak>
	<section class="content-header">
		<h1>
			Module Schedules 
		</h1>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-danger shadow-lg">
					<div class="box-header with-border">
						<v-btn href="{{ url('/admin/module_schedules/create') }}" small class="bg-red" dark>
							Add New Schedule
						</v-btn>
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
					<div class="box-body">
						<v-data-table class="table table-bordered"
                            :headers="headers"
                            :items="module_schedules"
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
                                    <td><strong>@{{ props.item.module }}</strong></td>
                                    <td>@{{ props.item.created_by }}</td>
                                    <td>@{{ props.item.created_at | dateTimeFormat }}</td>
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
										<div v-else-if="module_schedule.status == 'waiting'">
											<label class="label label-warning py-2 px-2">
												waiting
											</label>
										</div>
									</td>
									<td class="text-xs-center">
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
												<v-list-tile v-on:click="openFile(props.item.module_schedule_id)">
													<v-list-tile-title>
														<v-icon small color="warning">fa fa-folder-open</v-icon>&nbsp;
														Open Folder
													</v-list-tile-title>
												</v-list-tile>
												<v-list-tile v-on:click="deleteFile(props.item.module_schedule_id, props.index)">
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
					<div v-if="loading" class="overlay">
						<div class="container text-center">
							<v-progress-circular 
								class="mt-5"
								:size="70"
								:width="3"
								color="red"
								indeterminate>
							</v-progress-circular>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@push('scripts')
<script src="{{ url('public/admin-lte/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('public/admin-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script>
	$('#module_schedule_tab').addClass('active bg-red');
	$('#exams_treeview').addClass('active');
	const base_url = "{{ url('/') }}";
	const test = window.location.origin + '/e-learning/public';

	new Vue({
		el: '#app',
		data() {
			return {
				loading: true,
                search: '',
				headers: [
					{ text: 'Module', value: 'module' },
					{ text: 'Created By', value: 'created_by' },
					{ text: 'Created At', value: 'created_at' },
					{ text: 'Status', value: 'status' },
					{ text: 'Actions', value: '', align: 'center', sortable: false },
				],
				module_schedules: [],
				rows_per_page_items: [10, 30, 50, {"text":"$vuetify.dataIterator.rowsPerPageAll","value":-1}]
			}
		},
		created() {
			this.getModuleSchedules();
		},
		methods: {
			getModuleSchedules: function() {
				axios.get(`${base_url}/admin/module_schedules/get`)
				.then(({data}) => {
					this.module_schedules = data.module_schedules;
					setTimeout(() => {
						this.loading = false
					});
				})
				.catch((err) => {
					console.log(err.response);
				});
			},
			deleteFile: function(module_schedule_id, index) {
				swal({
					title: "Delete this schedule?",
					icon: "warning",
					buttons: {
						cancel: true,
						confirm: "Proceed"
					},
					dangerMode: true,
					closeOnClickOutside: false
				})
				.then((next) => {
					if (next)
						axios.delete(`${base_url}/admin/module_schedules/delete/${module_schedule_id}`)
						.then(({data}) => {
							this.module_schedules.splice(index, 1);
							this.message('Successfully Deleted');
						})
						.catch((err) => {
							console.log(err.response);
						});
				});
			},

            openFile: function(module_schedule_id) {
                window.location = `${base_url}/admin/module_schedule_id/${module_schedule_id}/module_details`;
            }
		}
	});
</script>
@endpush