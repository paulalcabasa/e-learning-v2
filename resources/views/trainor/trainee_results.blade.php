@extends('layouts.app')

@section('content')
<v-container>
	<v-layout>
		<v-flex md12 sm12>
			<v-card>
				<v-container class="pb-0" fluid>
					<v-layout>
						<v-flex xs12 align-end flexbox>
							<v-icon class="mr-1" style="font-size: 20px; margin-bottom: 2px;">fas fa-users</v-icon>
							<span class="headline">Trainee's Exam Result</span>
						</v-flex>
					</v-layout>
				</v-container>

				<v-container fluid grid-list-md>
					<v-layout row wrap>
						<v-flex xs12 sm12 sm12 md12 lg12>
							<v-card class="elevation-10">
								<v-layout row wrap>
									<v-flex xs12 sm6 md6 lg8>
										<v-btn
											href="{{ url('/trainor/trainee_schedules') }}"
											color="red darken-1" 
											small 
											dark>
											<v-icon>chevron_left</v-icon>
											Go Back
										</v-btn>
									</v-flex>
									<v-flex xs12 sm6 md6 lg4>
										{{-- <v-text-field
											class="mr-4 ml-4 mt-2"
											v-model="search"
											append-icon="search"
											solo
											label="Search"
											hide-details>
										</v-text-field> --}}
									</v-flex>
								</v-layout>
								<v-layout row wrap>
									<v-flex xs12 sm6 md6 lg12>
										<v-toolbar flat color="white">
											<v-toolbar-title class="grey--text">Module</v-toolbar-title>
											<v-divider
												class="mx-2"
												inset
												vertical
											></v-divider>
											<v-toolbar-title class="ml-0">@{{ module_details.module }}</v-toolbar-title>
											<v-spacer></v-spacer>
											<v-text-field
											class="w50"
												v-model="search"
												append-icon="search"
												label="Search"
												hide-details>
											</v-text-field>
										</v-toolbar>
									</v-flex>
								</v-layout>
								
								<v-data-table
									:headers="headers"
									:items="items"
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
											<td>
												<v-icon class="caption mr-1">fas fa-user</v-icon>
												<strong>@{{ props.item.trainee }}</strong>
											</td>
											<td>@{{ props.item.created_at | dateTimeFormat }}</td>
											<td>@{{ props.item.updated_at | dateTimeFormat }}</td>
											{{-- <td>@{{ props.item.passing_score }}</td> --}}
											<td class="text-xs-center">@{{ props.item.items }}</td>

											<td>
												@{{ props.item.score }} / @{{ props.item.items }}
											</td>
											<td v-if="props.item.score >= props.item.passing_score"
												class="text-xs-center white--text" style="background-color: #9CCC65">
												Passed
											</td>
											<td v-else
												class="text-xs-center white--text error lighten-1">
												Failed
											</td>
										</tr>
									</template>
								</v-data-table>
							</v-card>
						</v-flex>
					</v-layout>
				</v-container>
			</v-card>
		</v-flex>
	</v-layout>
</v-container>
@endsection

@push('scripts')
<script src="{{ url('public/js/appCache.js') }}"></script>
<script>
	const TRAINOR_ID = "{{ str_replace_last('trainor_', '', Auth::user()->app_user_id) }}";
	const base_url = "{{ url('/') }}";
	const EXAM_SCHEDULE_ID = "{{ Request::segment(4) }}";

	var app = new AppCache;
	
	new Vue({
		el: '#app',
		data() {
			return {
				loading: true,
				module_details: {},
				search: '',
				headers: [
					{ text: 'Trainee', value: 'trainee' },
					{ text: 'Date Started', value: 'created_at' },
					{ text: 'Date Ended', value: 'updated_at' },
					// { text: 'Passing Score', value: 'passing_score' },
					{ text: 'No. of Items', value: 'items' },
					{ text: 'Score', value: 'score' },
					{ text: 'Status', value: 'status' },
				],
				items: [],
				rows_per_page_items: [10, 30, 50, {"text":"$vuetify.dataIterator.rowsPerPageAll","value":-1}]
			}
		},
		created() {
			this.getItem();
			this.result_tab = 'red';
		},
		mounted() {
			this.module_details = JSON.parse(app.getItem('module_details'));
		},
		methods: {
			getItem: function() {
				axios.get(`${base_url}/trainor/trainee_results/get/${EXAM_SCHEDULE_ID}/${TRAINOR_ID}`)
				.then(({data}) => {
					this.items = data;

					setTimeout(() => {
						this.loading = false
					}, 1000)
				})
				.catch((err) => {
					console.log(err.response);
				});
			},
		}
	})
</script>
@endpush
