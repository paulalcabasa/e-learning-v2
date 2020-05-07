@extends('admin_template') 

@push('styles')
	<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
	<style>
		.drop_down_menu li a:hover {
			color: #337AB7;
		}

		.dataTables_scrollBody {
			min-height : 200px;
		}
	</style>
@endpush

@section('content')
<div v-cloak>
	<section class="content-header">
		<h1>
			Trainees
		</h1>
	</section>

	<section class="content container-fluid">
		<div class="box box-danger sub-content shadow-lg">
			<div class="box-body">
				<table class="no-wrap table table-responsive table-striped table-hover" id="datatable" style="width: 100%">
					<thead>
						<tr>
							<th class="text-center" width="25px">#</th>
							<th class="text-center">Trainee</th>
							<th class="text-center">Email</th>
							<th class="text-center">Created by Trainor</th>
							<th class="text-center">Dealer</th>
							<th class="text-center">Date Created</th>
							<th class="text-center">Status</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(item, index) in items" v-bind:key="index">
							<td class="text-center">
								<div class="btn-group">
									<button type="button" 
									class="btn btn-sm btn-default dropdown-toggle py-0 px-4" 
									data-toggle="dropdown" 
									aria-haspopup="true" aria-expanded="false">
										<i class="fa fa-ellipsis-h"></i>
									</button>
									<ul class="dropdown-menu drop_down_menu shadow">
										<li class="text-left">
											<a v-bind:href="`{{ url('admin/trainee_details/') }}/${item.trainee_id}`">
												<i class="fa fa-folder text-primary"></i>&nbsp;
												View Tranining History
											</a>
										</li>
									</ul>
								</div>
							</td>
							<td class="text-center">@{{ `${item.lname}, ${item.fname} ${item.mname == null ? '' : item.mname}` }}</td>
							<td class="text-center text-primary">@{{ item.email }}</td>
							<td class="text-center">@{{ item.trainor }}</td>
							<td class="text-center">@{{ item.dealer_name + ' @ ' + item.branch }}</td>
							<td class="text-center">@{{ item.created_at | dateTimeFormat }}</td>
							<td class="text-center">
								<span v-if="item.is_approved" class="label bg-success py-1 px-2"><i class="fa fa-check"></i>&nbsp; approved</span>
								{{-- <button v-else type="button" class="btn btn-sm btn-flat btn-default" v-on:click="approveTrainee(item.trainee_id)">
									<i class="fa fa-pen"></i>&nbsp;
									UPDATE STATUS
								</button> --}}
								<button v-else-if="item.is_approved == 0" type="button" class="btn btn-sm btn-flat btn-default" v-on:click="updateStatus(item.trainee_id)">
									<i class="fa fa-pen"></i>&nbsp;
									UPDATE STATUS
								</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</section>
</div>
@include('contents.trainees.update_status_modal')
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
				items: [],
				trainee_id: 0
			}
		},
		created() {
			this.getTrainees();
		},
		methods: {
			getTrainees: function() {
				axios.get(`${this.base_url}/admin/trainees/get`)
				.then(({data}) => {
					this.items = data.trainees;
					setTimeout(() => {
						$('#datatable').DataTable({
							"scrollX": true
						});	
					});
				})
				.catch((error) => {
					console.log(error.response);
					swal('Ooops!', 'Something went wrong.', 'error', {timer:4000,button:false});
				});
			},
			approveTrainee: function(trainee_id) {
				swal({
					title: "Do you want to approve this trainee?",
					icon: "warning",
					buttons: true
				})
				.then((willDelete) => {
					if (willDelete) {
						axios.put(`${this.base_url}/admin/trainees/approve/${trainee_id}`)
						.then(({data}) => {
							if (data) {
								this.getTrainees();
								swal('Success!', 'You have approved one trainee', 'success', {timer:4000,button:false});
							}
						})
						.catch((error) => {
							console.log(error.response);
							swal('Ooops!', 'Something went wrong.', 'error', {timer:4000,button:false});
						});
					}
				});
			},
			updateStatus: function(trainee_id) {
				this.trainee_id = trainee_id;
				$('#update_status_modal').modal('show');
			},
			updateSaveStatus: function(status) {
				swal({
					title: status == 1 ? "This will approve Trainee" : "This will disapprove Trainee",
					text: status == 1 ? "" : "Once Trainee is disapproved, it will deleted.",
					icon: "warning",
					buttons: {
						cancel: true,
						confirm: 'Proceed'
					},
					closeOnClickOutside: false
				})
				.then((res) => {
					if (res) {
						return axios.put(`${this.base_url}/admin/update_trainee_status/${this.trainee_id}/${status}`)
						.then(({data}) => {
							console.log(data);
							this.get_trainees();
						})
						.catch((error) => {
							console.log(error.response);
							swal('Ooops!', 'Something went wrong.', 'error', {timer:4000,button:false});
						});
					}
				});
			},
			get_trainees: function() {
				return axios.get(`${this.base_url}/admin/trainees/get`)
					.then(({data}) => {
						this.items = data.trainees;
						$('#update_status_modal').modal('hide');
						swal('Alright!', 'Operation Succeeded', 'success', {timer:4000,button:false});
					})
					.catch((error) => {
						console.log(error.response);
						swal('Ooops!', 'Something went wrong.', 'error', {timer:4000,button:false});
					});
			}
		}
	})
</script>
@endpush