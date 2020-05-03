@extends('admin_template') 

@push('styles')
	<style>[v-cloak] { display: none; }</style>
	<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endpush

@section('content')
	<div v-cloak>
		<section class="content-header">
			<h1>Archive Trainors</h1>
		</section>

		<section class="content container-fluid">
			<div class="box box-danger sub-content shadow-lg">
				<div class="box-body">
					<table id="datatable" class="table table-responsive table-hover table-bordered">
						<thead>
							<tr>
								<th width="25px">#</th>
								<th class="text-center text-uppercase">Trainor</th>
								<th class="text-center text-uppercase">Email</th>
								<th class="text-center text-uppercase">Dealer</th>
								<th class="text-center text-uppercase">Branch</th>
								<th class="text-center text-uppercase">Date Deleted</th>
								<th class="text-center text-uppercase" width="200">ACTION</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(item, index) in items">
								<td class="text-center">@{{ index+1 }}</td>
								<td class="text-center">@{{ item.lname }}, @{{ item.fname }} @{{ item.mname }}</td>
								<td class="text-center">@{{ item.email }}</td>
								<td class="text-center">@{{ item.dealer_name }}</td>
								<td class="text-center">@{{ item.branch }}</td>
								<td class="text-center">@{{ item.deleted_at | dateTimeFormat}}</td>
								<td class="text-center">
									<button v-on:click="retrieveTrainor(item.trainor_id)" class="btn btn-flat btn-sm btn-success text-uppercase">Retrieve</button>
									<button v-on:click="permanentDelete(item.trainor_id)" class="btn btn-flat btn-sm btn-danger text-uppercase">Delete</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</section>
	</div>
@endsection

@push('scripts')
	<script src="{{ url('public/admin-lte/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ url('public/admin-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
	<script>
		$('#archive_trainor_tab').addClass('active bg-red');
		new Vue({
			el: '#app'
			data() {
				return {
					items: []
				}
			},
			created() {
				this.fetchArchiveTrainors();
			},
			mounted() {
				this.scrollTo('#active_user_tab', '.sidebar');
			},
			methods: {
				fetchArchiveTrainors() {
					axios.get(`${this.base_url}/admin/archives/archive_trainors/get`)
					.then(({data}) => {
						this.items = data;
					})
					.catch((error) => {
						console.log(error.response);
					});
				},
				retrieveTrainor(trainor_id) {
					swal({
						title: "Retrieve Trainor",
						icon: "warning",
						buttons: true,
					})
					.then((res) => {
						if (res) {
							axios.put(`${this.base_url}/admin/archives/retrieve_trainors/update/${trainor_id}`)
							.then(({data}) => {
								if (data) {
									toastr.success('Trainor has been retrieved', 'Success!');
									this.fetchArchiveTrainors();
								}
							})
							.catch((error) => {
								console.log(error.response);
							});
						}
					});
				},
				permanentDelete(trainor_id) {
					swal({
						title: "Delete Trainor",
						text: "This will permanently delete Trainor",
						icon: "warning",
						buttons: {
							cancel: true,
							confirm: 'Delete permanently'
						},
						dangerMode: true
					})
					.then((res) => {
						if (res) {
							axios.delete(`${this.base_url}/admin/archives/delete_trainor/delete/${trainor_id}`)
							.then(({data}) => {
								if (data) {
									toastr.success('Trainor has been deleted', 'Success!');
									this.fetchArchiveTrainors();
								}
							})
							.catch((error) => {
								console.log(error.response);
							});
						}
					});
				}
			}
		})
	</script>
@endpush