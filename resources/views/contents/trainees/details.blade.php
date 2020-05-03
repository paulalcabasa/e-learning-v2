@extends('admin_template') 

@push('styles')
	<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}"> 
@endpush

@section('content')
<div v-cloak>
	<section class="content-header">
		<h1>
			Trainings
		</h1>
        <ol class="breadcrumb">
            <li>
                <a href="#" onclick="window.history.back()">
                    <i class="fa fa-users"></i> Back to Trainees
                </a>
            </li>
            <li class="active">Trainings</li>
        </ol>
	</section>

	<section class="content container-fluid">
		<div class="box box-danger sub-content shadow-lg">
			<div class="box-body">
				<div class="row">
                    <div class="col-md-3">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <label><i class="fa fa-user"></i>&nbsp; Trainee</label><br>
                                <span>@{{ trainee_details.lname }}, @{{ trainee_details.fname }} @{{ trainee_details.mname == null ? '' : trainee_details.mname }}</span>
                            </li>
                            <li class="list-group-item">
                                <label><i class="fa fa-envelope"></i>&nbsp; Email</label><br>
                                <span>@{{ trainee_details.email }}</span>
                            </li>
                            <li v-if="is_rendered" class="list-group-item">
                                <label><i class="fa fa-truck"></i>&nbsp; Dealer</label><br>
                                <span>@{{ trainee_details.dealer_name }}</span>
                            </li>
                            <li v-if="is_rendered" class="list-group-item">
                                <label><i class="fa fa-warehouse"></i>&nbsp; Branch</label><br>
                                <span>@{{ trainee_details.branch }}</span>
                            </li>
                            <li class="list-group-item">
                                <label><i class="far fa-running"></i>&nbsp; Total Trainings</label>&nbsp;
                                <div class="label label-success">
                                    @{{ total_trainings }}
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-9">
                        <table class="table table-responsive table-hover" id="datatable">
                            <thead>
                                <tr>
                                    <th class="text-center" width="25px">#</th>
                                    <th class="text-left">Module</th>
                                    <th class="text-center">Score</th>
                                    <th class="text-center">Result</th>
                                    <th class="text-center">Date Taken</th>
                                    <th class="text-center">Date Finished</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in items" v-bind:key="item.training_history_id">
                                    <td class="text-center">@{{ index+1 }}</td>
                                    <td class="text-center">@{{ item.module }}</td>
                                    <td class="text-center">@{{ item.score }}</td>
                                    <td class="text-center">
                                        <div v-bind:class="`label label-${item.result == 'failed' ? 'danger' : 'success'}`">
                                            @{{ item.result }}
                                        </div>
                                    </td>
                                    <td class="text-center">@{{ item.date_taken | dateTimeFormat }}</td>
                                    <td class="text-center">@{{ item.date_finished | dateTimeFormat }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="is_empty" class="callout callout-danger">
                            <h4 class="text-center"><i class="fa fa-exclamation-circle"></i>&nbsp; Have no trainings yet</h4>
                        </div>
                    </div>
                </div>
			</div>
            <div v-if="is_waiting" class="overlay">
                <i class="fa fa-sync-alt fa-spin"></i>
            </div>
		</div>
	</section>
</div>
@endsection

@push('scripts')
<script src="{{ url('public/admin-lte/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('public/admin-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ url('public/js/crud.js') }}"></script>
<script>
	$('#trainee_tab').addClass('active bg-red');
	$('#dealer_treeview').addClass('active');

	new Vue({
		el: '#app',
		data() {
			return {
                is_empty: false,
                is_waiting: true,
                is_rendered: false,
                trainee_id: "{{ Request::segment(3) }}",
				items: [],
                trainee_details: {},
                total_trainings: 0
			}
		},
        watch: {
            items() {
                if (this.items.length == 0) {
                    this.is_empty = true;
                }

                this.total_trainings = this.items.length;
            }
        },
		created() {
            this.getTrainee();
            this.getHistory();
		},
		methods: {
			getTrainee: function() {
                axios.get(`${this.base_url}/admin/trainee/${this.trainee_id}`)
                .then(({data}) => {
                    this.is_rendered = true;
                    this.trainee_details = data;
                })
                .catch((error) => {
                    console.log(error.response);
                    swal('Ooops!', 'Something went wrong.', 'error', {timer:4000,button:false});
                });
            },
            getHistory: function() {
                axios.get(`${this.base_url}/admin/trainee_history/${this.trainee_id}`)
                .then(({data}) => {
                    this.items = data;
                    this.is_waiting = false;
                })
                .catch((error) => {
                    console.log(error.response);
                });
            }
		}
	})
</script>
@endpush