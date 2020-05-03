@extends('admin_template') 

@push('styles')
	<style>
		[v-cloak] { display: none; }
		.drop_down_menu li a:hover {
			color: #337AB7;
		}
	</style>
	<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endpush 

@section('content')
<div v-cloak>
	<section class="content-header">
		<h1>
			Trainors
		</h1>
	</section>

	<section class="content container-fluid">
		<div class="box box-danger sub-content shadow-lg">
			<div class="box-header with-border clearfix">
				<v-btn onclick="app.show_create_trainor()" small class="bg-red" dark>
					Add New Trainor
				</v-btn>
			</div>
			<div class="box-body">
				<table id="datatable" class="no-wrap table table-responsive table-hover table-bordered" style="width: 100%">
					<thead>
						<tr>
							<th class="text-center" width="25px">&nbsp;</th>
							<th class="text-center">Trainor</th>
							<th class="text-center">Email</th>
							<th class="text-center">Dealer</th>
							<th class="text-center">Branch</th>
							<th class="text-center">Date Created</th>
							<th class="text-center">Status</th>
						</tr>
					</thead>
					{{-- <tbody id="trainor_table"> --}}
					<tbody>
						<tr v-for="(item, index) in trainors" v-bind:key="item.trainor_id">
							<td class="text-center">
								<div class="btn-group">
									<button type="button" 
									class="btn btn-sm btn-default dropdown-toggle py-0 px-4" 
									data-toggle="dropdown" 
									aria-haspopup="true" aria-expanded="false">
										<i class="fa fa-ellipsis-h"></i>
									</button>
									<ul class="dropdown-menu shadow-lg drop_down_menu">
										<li class="text-left">
											<a v-on:click="show_update_trainor(item.trainor_id)">
												<i class="fa fa-pen text-primary"></i>
												Edit
											</a>
										</li>
										<li class="text-left">
											<a v-if="item.deleted_at" 
											v-on:click="retrieveTrainor(item.trainor_id)" 
											data-toggle="tooltip" 
											data-placement="right" 
											title="User is on INACTIVE state">
												<i class="fa fa-toggle-off text-primary" style="font-size: 16px; margin-left: -4px;"></i>
												Activate Account
											</a>
											<a v-else
											v-on:click="delete_trainor(item.trainor_id)" 
											data-toggle="tooltip" 
											data-placement="right" 
											title="User is on ACTIVE state">
												<i class="fa fa-toggle-on text-primary" style="font-size: 16px; margin-left: -4px;"></i>
												Deactivate Account
											</a>
										</li>
										<li class="text-left">
											<a v-on:click="resetPassword(item.trainor_id, 'trainor')">
												<i class="ion ion-refresh text-primary"></i>
												Reset Password
											</a>
										</li>
										<li class="text-left">
											<a v-on:click="viewDetails(item.trainor_id)">
												<i class="ion ion-folder text-primary"></i>
												View Tranining History
											</a>
										</li>
									</ul>
								</div>
							</td>
							<td class="text-center">@{{ item.lname }}, @{{ item.fname }} @{{ item.mname }}</td>
							<td class="text-center">@{{ item.email }}</td>
							<td class="text-center">@{{ item.dealer_name }}</td>
							<td class="text-center">@{{ item.branch }}</td>
							<td class="text-center">@{{ item.created_at | dateTimeFormat }}</td>
							<td class="text-center">
								<div v-if="!item.deleted_at" class="label label-success" style="padding: 5px 7px;">
									<i class="fa fa-check-circle" style="font-size: 14px;"></i>&nbsp;
									ACTIVE
								</div>
								<div v-else class="label label-danger" style="padding: 5px 7px">
									<i class="fa fa-times-circle" style="font-size: 14px;"></i>&nbsp;
									@{{ item.deleted_at | dateTimeFormat }}
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</section>
</div>
@include('contents.trainors.trainor_modal')
@include('contents.trainors.details_modal')
@endsection

@push('scripts')
<script src="{{ url('public/admin-lte/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('public/admin-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ url('public/js/crud.js') }}"></script>
<script>
	$('#trainor_tab').addClass('active bg-red');
	$('#dealer_treeview').addClass('active');
	const base_url = "{{ url('/') }}";

	var vm = new Vue({
		el: '#app',
		data() {
			return {
				trainors: [],
				trainor_details: [],
				trainor: {}
			}
		},
		created() {
			this.getTrainors();
		},
		methods: {
			getTrainor: function(trainor_id) {
				axios.get(`${this.base_url}/admin/trainors/get/${trainor_id}`)
				.then(({data}) => {
					this.trainor = data;
				})
				.catch((error) => {
					console.log(error.response);
					swal('Ooops!', 'Something went wrong.', 'error', {timer:4000,button:false});
				});
			},
			resetPassword(id, user_type) {
				swal({
					title: 'Reset Password?',
					text: 'This will notify trainor for his default password.',
					icon: 'warning',
					dangerMode: true,
					buttons: {
						cancel: {
							text: "Cancel",
							value: null,
							visible: true,
							closeModal: true,
						},
						confirm: {
							text: "Proceed",
							value: true,
							visible: true,
							closeModal: false,
						},
					}
				})
				.then((res) => {
					if (res)
						axios.put(`${this.base_url}/admin/reset_password/${id}/${user_type}`)
						.then(({data}) => {
							if (data) 
								swal({
									title: 'Success!',
									text: 'Password has been successfully reset.',
									icon: 'success',
									button: false,
									timer: 4000
								});
						})
						.catch((error) => {
							console.log(error.response);
							swal({
								title: "Ooops!",
								text: "Something went wrong. Please try again.",
								icon: "error",
								button: false,
								timer: 4000,
							})
						});
				});
			},
			retrieveTrainor(trainor_id) {
				swal({
					title: "Activate Trainor",
					icon: "warning",
					buttons: true,
				})
				.then((res) => {
					if (res) {
						axios.put(`${this.base_url}/admin/archives/retrieve_trainors/update/${trainor_id}`)
						.then(({data}) => {
							if (data) {
								toastr.success('Trainor has been activated', 'Success!');
								this.getTrainors();
							}
						})
						.catch((error) => {
							console.log(error.response);
							swal({
								title: "Ooops!",
								text: "Something went wrong. Please try again.",
								icon: "error",
								button: false,
								timer: 4000,
							})
							swal({
								title: "Ooops!",
								text: "Something went wrong. Please try again.",
								icon: "error",
								button: false,
								timer: 4000,
							})
						});
					}
				});
			},
			getTrainors() {
				axios.get(`${this.base_url}/admin/trainors/get`)
				.then(({data}) => {
					$('#datatable').DataTable().destroy()
					this.trainors = data.trainors
				})
				.then(() => {
					$('#datatable').DataTable({
						scrollX: true
					});
				})
				.catch((error) => {
					console.log(error);
					swal({
						title: "Ooops!",
						text: "Something went wrong. Please try again.",
						icon: "error",
						button: false,
						timer: 4000,
					})
				});
			},
			show_update_trainor(trainor_id) {
				$.ajax({
					type: 'GET',
					url: base_url + '/admin/trainors/get/' + trainor_id
				})
				.done((res) => {
					$('#trainor_modal').modal('show');
					$('.modal_title').text('Update Trainor');
					crud.set_method('PUT');
					
					// data fields:
					$('#dealer_id').val(res.dealer_id);
					$('input[name="trainor_id"]').val(res.trainor_id);
					$('input[name="fname"]').val(res.fname);
					$('input[name="mname"]').val(res.mname);
					$('input[name="lname"]').val(res.lname);
					$('input[name="email"]').val(res.email);
				}).fail((err) => {
					console.log(err['responseJSON']);
				});
			},
			delete_trainor(trainor_id) {
				swal({
					title: "Deactivate Trainor?",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete) {
						$.ajax({
							type: 'DELETE',
							url: base_url + '/admin/trainors/delete/' + trainor_id,
						})
						.done((res) => {
							$('#trainor_' + res.trainor_id).remove();
							this.getTrainors();
							toastr.success('Successfully deactivated!');
						})
						.fail((err) => {
							console.log(err['responseJSON']);
						});
					}
				});
			},
			viewDetails: function(trainor_id) {
				this.getTrainor(trainor_id);
				axios.get(`${this.base_url}/admin/trainor_history/${trainor_id}`)
				.then(({data}) => {
					this.trainor_details = data;

					$('#details_modal').modal('show');
				})
				.catch((error) => {
					console.log(error.response);
					swal('Ooops!', 'Something went wrong.', 'error', {timer:4000,button:false});
				});
			}
		}
	})

	var app = null;
	$(function() {
		app = {
			init() {
				this.get_dealers();
				// this.get_trainors();
			},
			get_trainors() {
				$.ajax({
					type: 'GET',
					url: base_url + '/admin/trainors/get'
				})
				.done((res) => {
					$("#trainor_table tr").remove();
					var counter = 0;
					jQuery.each(res.trainors, function(i, val) {
						counter += 1;
						var mname = val.mname == null ? '' : val.mname;
						$("#trainor_table").append(`
							<tr id="trainor_`+ val.trainor_id +`">
								<td class="text-center">
									`+ counter +`
								</td>
								<td>`+ val.lname +`, `+ val.fname +` `+ mname +`</td>
								<td>`+ val.email +`</td>
								<td>`+ val.dealer_name +`</td>
								<td>`+ val.branch +`</td>
								<td>
									<button onclick="app.show_update_trainor(`+ val.trainor_id +`)" class="btn btn-sm btn-primary">
										<i class="fas fa-pen"></i>
									</button>
									<button onclick="app.delete_trainor(`+ val.trainor_id +`)" class="btn btn-sm btn-danger">
										<i class="ion ion-archive"></i>
									</button>
								</td>
							</tr>
						`);
					});

					$('#datatable').DataTable();
				}).fail((err) => {
					swal({
						title: "Ooops!",
						text: "Something went wrong. Please try again.",
						icon: "error",
						button: false,
						timer: 4000,
					})
					console.log(err['responseJSON']);
				});
			},
			get_dealers() {
				$.ajax({
					type: 'GET',
					url: base_url + '/admin/dealers/get'
				})
				.done((res) => {
					$("#dealer_id option").remove();
					jQuery.each(res.dealers, function(i, val) {
						$("#dealer_id").append(`
							<option value="`+ val.dealer_id +`">`+ val.dealer_name + ' | ' + val.branch +`</option>
						`);
					});
				}).fail((err) => {
					swal({
						title: "Ooops!",
						text: "Something went wrong. Please try again.",
						icon: "error",
						button: false,
						timer: 4000,
					})
					console.log(err['responseJSON']);
				});
			},
			show_create_trainor() {
				$('#trainor_modal').modal('show');
				$('.modal_title').text('Add Trainor');
				crud.set_method('POST');
				crud.reset_form('create_trainor');

				// $('input[name="fname"]').removeAttr('disabled');
				// $('input[name="mname"]').removeAttr('disabled');
				// $('input[name="lname"]').removeAttr('disabled');
			},
			show_update_trainor(trainor_id) {
				$.ajax({
					type: 'GET',
					url: base_url + '/admin/trainors/get/' + trainor_id
				})
				.done((res) => {
					$('#trainor_modal').modal('show');
					$('.modal_title').text('Update Trainor');
					crud.set_method('PUT');
					
					// data fields:
					$('#dealer_id').val(res.dealer_id);
					$('input[name="trainor_id"]').val(res.trainor_id);
					$('input[name="fname"]').val(res.fname);
					$('input[name="mname"]').val(res.mname);
					$('input[name="lname"]').val(res.lname);
					$('input[name="email"]').val(res.email);

					// $('input[name="fname"]').attr('disabled', 'true');
					// $('input[name="mname"]').attr('disabled', 'true');
					// $('input[name="lname"]').attr('disabled', 'true');
				}).fail((err) => {
					swal({
						title: "Ooops!",
						text: "Something went wrong. Please try again.",
						icon: "error",
						button: false,
						timer: 4000,
					})
					console.log(err['responseJSON']);
				});
			},
			delete_trainor(trainor_id) {
				swal({
					title: "Archive trainor?",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete) {
						$.ajax({
							type: 'DELETE',
							url: base_url + '/admin/trainors/delete/' + trainor_id,
						})
						.done((res) => {
							$('#trainor_' + res.trainor_id).remove();
							// app.init();
							this.get_trainors();
							toastr.success('Successfully archived!');
						})
						.fail((err) => {
							swal({
								title: "Ooops!",
								text: "Something went wrong. Please try again.",
								icon: "error",
								button: false,
								timer: 4000,
							})
							console.log(err['responseJSON']);
						});
					}
				});
			}
		}
		app.init();
	});

	$(document).on('submit', '#create_trainor', function(event) {
		event.preventDefault();

		var trainor_id = $('input[name="trainor_id"]').val();
		var url = base_url + '/admin/trainors/post';
		if (crud.get_method() == 'PUT') {
			url = base_url + '/admin/trainors/put/' + trainor_id
		}
		
		$.ajax({
			type: 'POST',
			url: url,
			data: new FormData($(this)[0]),
			contentType: false,
			cache: false,
			processData: false,
		})
		.done((res) => {
			// app.init();
			app.get_trainors();
			if (crud.get_method() == 'POST') {
				toastr.success('Successfully added!');
				crud.reset_form('create_trainor');
				vm.getTrainors();
			}
			else {
				toastr.success('Successfully updated!');
				vm.getTrainors();
				crud.close_modal('trainor_modal');
			}
		})
		.fail((err) => {
			swal({
				title: "Ooops!",
				text: "Something went wrong. Please try again.",
				icon: "error",
				button: false,
				timer: 4000,
			})
			console.log(err['responseJSON']);
			if (err['responseJSON']['errors']['email']) {
				$('#email_err').text(err['responseJSON']['errors']['email'][0]);
			}
		});
	});
</script>
@endpush