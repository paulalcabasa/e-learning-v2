@extends('admin_template') 

@push('styles')
	<style>
		[v-cloak] {
			display: none;
		}
	</style>
@endpush

@section('content')
<div v-cloak>
	<section class="content-header">
		<a href="{{ url('/admin/results/exam_schedules') }}" class="btn btn-sm back-button pull-right">
			<i class="fas fa-chevron-left mr-1"></i>
			BACK
		</a>
		<h1 class="sub-editable">
			Dealers
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
									<li><a href="{{ url('/admin/results/exam_schedules/') }}">@{{ module_details.module }}</a></li>
									<li class="active">List of Dealers</li>
								</ol>
							</div>
							<div class="col-md-6">
								<v-text-field class="pull-right" style="width: 300px;" v-model="search" append-icon="search" label="Search" solo hide-details>
								</v-text-field>
							</div>
						</div>
					</div>
					<div class="box-body">
						<v-data-table class="table table-bordered" :headers="headers" :items="dealers" :search="search" :disable-initial-sort="true"
							:rows-per-page-items="rows_per_page_items" :loading="loading" class="elevation-1">

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
									<td class="text-xs-center"><strong>@{{ props.item.dealer_name }}</strong></td>
									<td class="text-xs-center">@{{ props.item.branch }}</td>
									<td class="text-xs-center">
										<v-tooltip left>
											<v-btn v-on:click="callSummary(props.item.dealer_id,props.item.dealer_name,props.item.branch)" slot="activator" small>
												Average Score
											</v-btn>
											<span>View Summary</span>
										</v-tooltip>
										<v-tooltip left>
											<v-btn v-on:click="navigateToTrainees('{{ Request::segment(4) }}', props.item)" slot="activator" color="red darken-1" small dark>
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
				<v-card-title>
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
				</v-card-title>
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
				search: '',
				module_details: {},
				headers: [
					{ text: 'Dealer', value: 'dealer_name', align: 'center' },
					{ text: 'Branch', value: 'branch', align: 'center' },
					{ text: 'Actions', value: 'total_items', align: 'center' },
				],
				dealers: [],
				rows_per_page_items: [10, 30, 50, {"text":"$vuetify.dataIterator.rowsPerPageAll","value":-1}],
				summary: [],
				summary_headers: [
					{ text: '#', value: '', sortable: false, align: 'left' },
					{ text: 'Submodule', value: 'sub_module' },
					{ text: 'Score', value: 'score', align: 'right' }
				],
				dealer: {
					dealer_id: 0,
					dealer: '',
					branch: ''
				},
				total: {
					score: 0,
					items: 0
				}
			}
		},
		created() {
			this.fetchDealers('{{ Request::segment(4) }}');
		},
		mounted() {
			this.module_details = JSON.parse(app.getItem('module_details'))
		},
		methods: {
			fetchDealers: function(exam_schedule_id) {
				axios.get(`${base_url}/admin/results/dealer_average/get/${exam_schedule_id}`)
				.then(({data}) => {
					this.dealers = data;
					setTimeout(() => {
						this.loading = false
					});
				})
				.catch((err) => {
					console.log(err.response);
				});
			},
			navigateToTrainees: function(exam_schedule_id, dealer) {
				app.saveItem('dealer_details', dealer, true);
				window.location.href = `${base_url}/admin/results/exam_schedules/${exam_schedule_id}/dealers/${dealer.dealer_id}`;
			},
			callSummary: function(dealer_id,dealer,branch) {
				var exam_schedule_id = '{{ Request::segment(4) }}';
				this.dealer = {
					dealer_id: dealer_id,
					dealer: dealer,
					branch: branch
				}
				
				axios.get(`${base_url}/admin/dealer_summary/${this.dealer.dealer_id}/${parseInt(exam_schedule_id)}`)
				.then(({data}) => {
					this.summary = data;

					// Compute for final score and total items
					this.total = { score: 0, items: 0 };
					this.summary.forEach(el => {
						this.total.score += parseInt(el.score);
						this.total.items += parseInt(el.items);
					});

					this.summary_dialog = true;
				})
				.catch((err) => {
					console.log(err);
				});
			}
		}
	});
</script>
@endpush