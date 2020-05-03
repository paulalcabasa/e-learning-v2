@extends('admin_template') 

@push('styles')
	<style>[v-cloak] { display: none; }</style>
@endpush

@section('content')
<div v-cloak>
	<section class="content-header">
        <a href="{{ url('/admin/results/exam_schedules/' . Request::segment(4) . '/dealers/') }}" 
        class="btn btn-sm back-button pull-right">
            <i class="fas fa-chevron-left mr-1"></i>
            BACK
        </a>
        <h1 class="sub-editable">
            Trainees 
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
									<li>
                                        <a href="{{ url('/admin/results/exam_schedules/') }}">
                                            @{{ module_details.module }}
                                        </a>
                                    </li>
									<li>
                                        <a href="{{ url('/admin/results/exam_schedules/' . Request::segment(4) . '/dealers') }}">
                                            @{{ dealer_details.dealer_name }} @ @{{ dealer_details.branch }}
                                        </a>
                                    </li>
									<li class="active">List of Trainees</li>
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
                            :items="trainees"
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
                                    <td><strong>@{{ props.item.trainee }}</strong></td>
                                    <td>@{{ props.item.created_at | dateTimeFormat }}</td>
                                    <td>@{{ props.item.updated_at | dateTimeFormat }}</td>

                                    <td>
                                        <strong>
                                            @{{ props.item.score }} / @{{ props.item.items }} &nbsp; 
                                        </strong>
                                        <span class="label label-success py-1 px-2" style="font-size: 12px;">
                                            @{{ generatePercentage(props.item.score, props.item.items) }} %
                                        </span>
                                        
                                    </td>

                                    <td v-if="props.item.score >= props.item.passing_score"
                                        class="text-xs-center green lighten-2  white--text">
                                        Passed
                                    </td>
                                    <td v-else
                                        class="text-xs-center red lighten-2 white--text">
                                        Failed
                                    </td>
                                    <td class="text-xs-center">
                                        <v-tooltip left>
                                            <v-btn 
                                                v-on:click="callSummary(props.item.trainee_id)"
                                                slot="activator"
                                                small>
                                                Summary
                                            </v-btn>
                                            <span>View Summary</span>
                                        </v-tooltip>

                                        <v-tooltip left>
                                            <v-btn 
                                                v-on:click="navigateToTrainees('{{ Request::segment(4) }}', '{{ Request::segment(6) }}', props.item)"
                                                slot="activator"
                                                small>
                                                Detailed
                                            </v-btn>
                                            <span>View Detailed</span>
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
    <v-dialog v-model="summary_dialog" 
    lazy persistent max-width="500px">
        <v-card>
            <v-toolbar color="red darken-1" dark>
                <v-toolbar-title>Summary</v-toolbar-title>
    
                <v-spacer></v-spacer>
    
                <v-btn 
                    v-on:click="summary_dialog = false"
                    class="hidden-xs-only" 
                    icon>
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <!-- Card Body -->
            <v-card-text style="margin-top: -18px;">
                <v-card-title>
                    <div>
                        <span class="grey--text">
                            <v-icon class="mb-1">fas fa-user fa-sm</v-icon>
                            Trainee
                        </span><br>
                        <span>
                            <h4 class="green--text my-0">
                                @{{ trainee.fname }} @{{ trainee.lname }}
                            </h4>
                        </span>
                    </div>
                </v-card-title>
                <v-data-table
                    :items="summary"
                    :headers="summary_headers"
                    :disable-initial-sort="true"
                    class="elevation-1"
                    hide-actions
                    >
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
                            <td class="text-xs-right pt-3 px-0 green--text">Total Score:</td>
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
				headers: [
					{ text: 'Trainee', value: 'trainee', width: 225 },
					{ text: 'Date Started', value: 'date_started', width: 240 },
					{ text: 'Date Ended', value: 'date_ended', width: 240 },
					{ text: 'Final Score', value: 'score' },
					{ text: 'Status', value: 'status', width: 10 },
					{ text: 'Actions', value: '', align: 'center', sortable: false, width: 260 },
				],
				trainees: [],
                rows_per_page_items: [10, 30, 50, {"text":"$vuetify.dataIterator.rowsPerPageAll","value":-1}],
                summary: [],
                summary_headers: [
                    { text: '#', value: '', sortable: false, align: 'left' },
                    { text: 'Submodule', value: 'sub_module' },
                    { text: 'Score', value: 'score', align: 'right' }
                ],
                trainee: {},
                total: {
                    score: 0,
                    items: 0
                },
                module_details: {},
                dealer_details: {}
			}
        },
        watch: {
            summary_dialog: function() {
                if (this.summary_dialog == false) this.total = {score: 0, items: 0};
            }
        },
		created() {
			this.fetchTrainees('{{ Request::segment(4) }}', '{{ Request::segment(6) }}');
		},
        mounted() {
			this.module_details = JSON.parse(app.getItem('module_details'))
			this.dealer_details = JSON.parse(app.getItem('dealer_details'))
		},
		methods: {
            generatePercentage: function(score, items) {
                if (!score || !items) return ''
                score = parseInt(score)
                items = parseInt(items)
                
                return score/items*100;
            },
			fetchTrainees: function(exam_schedule_id, dealer_id) {
				axios.get(`${base_url}/admin/results/trainees/get/${exam_schedule_id}/${dealer_id}`)
				.then(({data}) => {
                    this.trainees = data;
					setTimeout(() => {
						this.loading = false
					});
				})
				.catch((err) => {
					console.log(err.response);
				});
            },
            navigateToTrainees: function(exam_schedule_id, dealer_id, trainee) {
                app.saveItem('trainee_details', trainee, true);
                window.location.href = `${base_url}/admin/results/exam_schedules/${exam_schedule_id}/dealers/${dealer_id}/trainees/${trainee.trainee_id}`;
            },
            callSummary: function(trainee_id) {
                var exam_schedule_id = '{{ Request::segment(4) }}';
                var params = {
                    trainee_id: trainee_id,
                    exam_schedule_id: parseInt(exam_schedule_id)
                }

                axios.post(`${base_url}/admin/results/summary`, params)
                .then(({data}) => {
                    this.fetchTrainee(trainee_id);
                    this.summary = data;

                    // Compute for final score and total items
                    this.summary.forEach(el => {
                        this.total.score += parseInt(el.score);
                        this.total.items += parseInt(el.items);
                    });

                    if (this.summary.length > 0) {
                        this.summary_dialog = true; 
                    }
                })
                .catch((err) => {
                    console.log(err.response);
                });
            },
            fetchTrainee: function(trainee_id) {
                axios.get(`${base_url}/admin/trainees/get/${trainee_id}`)
                .then(({data}) => {
                    this.trainee = data;
                })
                .catch((err) => {
                    console.log(err.response);
                });
            }
		}
	});
</script>
@endpush