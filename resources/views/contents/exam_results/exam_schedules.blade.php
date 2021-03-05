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
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-danger shadow-lg">
					<div class="box-header">
						<div class="row">
                            <div class="col-md-6">
								<ol class="breadcrumb" style="padding: 12px">
									<li class="active">Modules</li>
								</ol>
							</div>
							<div class="col-md-6">
								<v-text-field class="pull-right" style="width: 300px;" v-model="search" append-icon="search" label="Search" solo hide-details>
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
                                    <td><strong>@{{ props.item.category_name }}</strong></td>
                                    <td><strong>@{{ props.item.module }}</strong></td>
                                    <td>@{{ props.item.created_by }}</td>
                                    <td>@{{ props.item.created_at | dateTimeFormat }}</td>
                                    <td>
                                        <div v-if="props.item.status == 'completed'">
                                            <label class="label label-success text-lg">
                                                Completed
                                            </label>
                                        </div>
                                        <div v-else-if="props.item.status == 'on_going'">
                                            <label class="label label-info">
                                                On-going
                                            </label>
										</div>
										<div v-else-if="module_schedule.status == 'waiting'">
											<label class="label label-warning">
												waiting
											</label>
										</div>
									</td>
									<td class="text-xs-center">
										<v-tooltip left>
											<v-btn v-on:click="callScheduleSummary(props.item.exam_schedule_id)" slot="activator" small>
												Average Score
											</v-btn>
											<span>View Summary</span>
										</v-tooltip>
										<v-tooltip left>
											<v-btn v-on:click="navigateToDealers(props.item.exam_schedule_id, props.item)" slot="activator" color="red darken-1" small dark>
												Open
												<i class="fas fa-chevron-right ml-2"></i>
											</v-btn>
											<span>Open</span>
										</v-tooltip>
									</td>
                                </tr>
                            </template>
                        </v-data-table>	
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- dialogs -->
	<v-dialog v-model="summary_dialog" lazy persistent max-width="500px">
		<v-card>
			<v-toolbar color="red darken-1" dark>
				<v-toolbar-title>Average Score</v-toolbar-title>
	
				<v-spacer></v-spacer>
	
				<v-btn v-on:click="summary_dialog = false" class="hidden-xs-only" icon>
					<v-icon>close</v-icon>
				</v-btn>
			</v-toolbar>
	
			<!-- Card Body -->
			<v-card-text style="margin-top: -18px;">
			<br><br>
				{{-- <v-card-title>
					<div>
						<span class="grey--text">
							<v-icon class="mb-1">fas fa-user fa-sm</v-icon>
							Dealer
						</span><br>
						<span>
							<h4 class="my-0">
								<span class="green--text my-0">@{{ dealer.dealer }}</span> 
								<strong class="grey--text">|</strong> 
								<span class="green--text my-0">@{{ dealer.branch }}</span>
							</h4>
						</span>
					</div>
				</v-card-title> --}}
				<v-data-table :items="summary" :headers="summary_headers" :disable-initial-sort="true" class="elevation-1" hide-actions>
					<template slot="items" slot-scope="props">
						<td class="text-xs-left">@{{ props.index + 1 }}</td>
						<td class="text-xs-left">@{{ props.item.sub_module }}</td>
						<td width="80" class="text-xs-right">@{{ props.item.score }} / @{{ props.item.items }}</td>
					</template>
				</v-data-table>
				<table class="table">
					<tbody>
						<tr>
							<td>&nbsp;</td>
							<td class="text-xs-right pt-3 px-0 green--text">Total Average Score:</td>
							<td width="80" class="text-xs-right pr-4 pt-3">@{{ total.score }} / @{{ total.items }}</td>
						</tr>
					</tbody>
				</table>
			</v-card-text>
		</v-card>
	</v-dialog>
</div>
@endsection

@push('scripts')
<script src="{{ url('public/js/appCache.js') }}"></script>
<script>
	$('#exam_result_tab').addClass('active bg-red');
	$('#scores_treeview').addClass('active');
	const base_url = "{{ url('/') }}";

	var app = new AppCache;

	new Vue({
		el: '#app',
		data() {
			return {
				summary_dialog: false,
				loading: true,
                search: null,
				headers: [
					{ text: 'Category', value: 'category_name' },
					{ text: 'Module', value: 'module' },
					{ text: 'Created By', value: 'created_by' },
					{ text: 'Created At', value: 'created_at' },
					{ text: 'Status', value: 'status', sortable: false },
					{ text: 'Actions', value: '', sortable: false, align: 'center' }
				],
				exam_schedules: [],
                rows_per_page_items: [10, 30, 50, {"text":"$vuetify.dataIterator.rowsPerPageAll","value":-1}],
				summary_headers: [
					{ text: '#', value: '', sortable: false, align: 'left' },
					{ text: 'Submodule', value: 'sub_module' },
					{ text: 'Score', value: 'score', align: 'right' }
				],
				total: {
					score: 0,
					items: 0
				},
				summary: [],
			}
		},
		created() {
			this.fetchSchedules();
		},
		mounted() {
			this.scrollTo('#exam_result_tab', '.sidebar');
		},
		methods: {
			fetchSchedules: function() {
				axios.get(`${base_url}/admin/results/exam_schedules/get`)
				.then(({data}) => {
                    this.exam_schedules = data;
					
					setTimeout(() => {
						this.loading = false
					});
				})
				.catch((err) => {
					console.log(err.response);
				});
            },
            navigateToDealers: function(exam_schedule_id, module_details) {
				app.saveItem('module_details', module_details, true);
                window.location.href = `${base_url}/admin/results/exam_schedules/${exam_schedule_id}/dealers`;
            },
			callScheduleSummary: function(exam_schedule_id) {
				axios.get(`${base_url}/admin/schedule_summary/${exam_schedule_id}`)
				.then(({data}) => {
					this.summary = data;

					// // Compute for final score and total items
					this.total = { score: 0, items: 0 };
					this.summary.forEach(el => {
						this.total.score += parseInt(el.score);
						this.total.items += parseInt(el.items);
					});

					this.summary_dialog = true;
				})
				.catch((err) => {
					console.log(err.response);
				});
			}
		}
	});
</script>
@endpush