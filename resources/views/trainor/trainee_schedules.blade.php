@extends('layouts.app')

@section('content')
<v-container>
	<v-layout>
		<v-flex md12 sm12>
			<v-card>
				<v-container class="pb-0" fluid>
					<v-layout>
						<v-flex xs12 align-end flexbox>
							<v-icon class="mr-1" style="font-size: 20px; margin-bottom: 2px;">fas fa-calendar-alt</v-icon>
							<span class="headline">Schedules</span>
						</v-flex>
					</v-layout>
				</v-container>

				<v-container fluid grid-list-md>
					<v-layout row wrap>
						<v-flex xs12 sm12 sm12 md12 lg12>
							<v-card class="elevation-10">
							
							<v-layout row wrap>
								<v-flex xs12 sm6 md6 lg8>
									
								</v-flex>
								<v-flex xs12 sm6 md6 lg4>
									<v-text-field
										class="mr-4 ml-4 mt-2"
										v-model="search"
										append-icon="search"
										solo
										label="Search"
										hide-details>
									</v-text-field>
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
										<tr 
										class="clickable" 
										v-on:click="navigateIntoTraineeResults(props.item.exam_schedule_id, props.item.module)">
											<td><strong>@{{ props.item.module.module }}</strong></td>
											<td>@{{ props.item.module.description }}</td>
											<td>@{{ props.item.created_at }}</td>
											<td>@{{ props.item.passing_score }}</td>
											<td>@{{ props.item.timer }}</td>
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

	var app = new AppCache;
	
	new Vue({
		el: '#app',
		data() {
			return {
				loading: true,
				search: '',
				headers: [
					{ text: 'Module', value: 'module' },
					{ text: 'Description', value: 'description' },
					{ text: 'Date Created', value: 'created_at' },
					{ text: 'Passing Score', value: 'passing_score' },
					{ text: 'Timer(mins)', value: 'timer' }
				],
				items: [],
				rows_per_page_items: [10, 30, 50, {"text":"$vuetify.dataIterator.rowsPerPageAll","value":-1}],
			}
		},
		created() {
			this.getItem();
			this.result_tab = 'red';
		},
		methods: {
			getItem: function() {
				axios.get(`${base_url}/trainor/trainee_schedules/get/${TRAINOR_ID}`)
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

			navigateIntoTraineeResults: function(exam_schedule_id, module_details) {
				app.saveItem('module_details', module_details, true);
				window.location.href = `${base_url}/trainor/trainee_results/exam_schedule_id/${exam_schedule_id}`;
			}
		}
	})
</script>
@endpush
