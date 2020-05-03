@extends('admin_template') 

@push('styles')
	<style>[v-cloak] { display: none; }</style>
	<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endpush

@section('content')
<div v-cloak>
	<section class="content-header">
		<h1>
			Dealers
		</h1>
	</section>

	<section class="content container-fluid">
		<div class="box box-danger sub-content shadow-lg">
			<div class="box-header with-border clearfix">
				<v-btn onclick="app.show_create_dealer()" small class="bg-red" dark>
					Add New Dealer
				</v-btn>
			</div>
			<div class="box-body">
				<table id="datatable" class="no-wrap table table-responsive table-hover table-striped" style="width: 100%">
					<thead>
						<tr>
							<th class="text-center text-uppercase" width="25px">#</th>
							<th class="text-center text-uppercase">Dealer</th>
							<th class="text-center text-uppercase">Company</th>
							<th class="text-center text-uppercase">Date Created</th>
							<th class="text-center text-uppercase" width="50px">&nbsp;</th>
						</tr>
					</thead>
					<tbody id="dealer_table"></tbody>
				</table>
			</div>
		</div>
	</section>

	<!-- Modal -->
	<div class="modal fade" id="dealer_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal_title"></h4>
				</div>
				<div class="modal-body">
					<form id="create_dealer" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="_method">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="dealer_id">
	
						<div class="form-group">
							<label for="module">
								Dealer Name
							<span class="text-danger">**</span>
							</label>
							<input type="text" class="form-control" 
							name="dealer_name" id="dealer_name" 
							required autofocus>
						</div>
	
						<div class="form-group">
							<label for="module">Company</label>
							<input type="text" class="form-control" 
							name="branch" id="branch" 
							required autofocus>
						</div>
	
						<div class="clearfix">
							<button type="submit" class="btn btn-primary pull-right">Save changes</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script src="{{ url('public/admin-lte/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('public/admin-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ url('public/js/crud.js') }}"></script>
<script>
	new Vue({
		el: '#app'
	})
</script>
<script>
	const base_url = "{{ url('/') }}";
	$('#dealer_tab').addClass('active bg-red');
	$('#dealer_treeview').addClass('active');
	var app = null;
	$(function() {
		app = {
			init() {
				this.get_dealers();
			},
			get_dealers() {
				$.ajax({
					type: 'GET',
					url: base_url + '/admin/dealers/get'
				})
				.then((res) => {
					$('#datatable').DataTable().destroy()
					$("#dealer_table tr").remove();
					var counter = 0;
					jQuery.each(res.dealers, function(i, val) {
						counter += 1;
						$("#dealer_table").append(`
							<tr id="dealer_`+ val.dealer_id +`">
								<td class="text-center">
									`+ counter +`
								</td>
								<td class="text-center">`+ val.dealer_name +`</td>
								<td class="text-center">`+ val.branch +`</td>
								<td class="text-center">`+ moment(val.created_at).format('MMM D, YYYY | h:mm:ss a')  +`</td>
								<td class="text-center">
									<button onclick="app.show_update_dealer(`+ val.dealer_id +`)" class="btn btn-xs btn-primary">
										<i class="fas fa-pen"></i>
									</button>
									<button onclick="app.delete_dealer(`+ val.dealer_id +`)" class="btn btn-xs btn-danger">
										<i class="fas fa-trash-alt"></i>
									</button>
								</td>
							</tr>
						`);
					});
				})
				.then(() => {
					$('#datatable').DataTable({
						scrollX: true
					});
				})
				.fail((err) => {
					console.log(err['responseJSON']);
				});
			},
			show_create_dealer() {
				$('#dealer_modal').modal('show');
				$('.modal_title').text('Add Dealer');
				crud.set_method('POST');
				crud.reset_form('create_dealer');
			},
			show_update_dealer(dealer_id) {
				$.ajax({
					type: 'GET',
					url: base_url + '/admin/dealers/get/' + dealer_id
				})
				.done((res) => {
					$('#dealer_modal').modal('show');
					$('.modal_title').text('Update Dealer');
					crud.set_method('PUT');
					
					// data fields:
					$('input[name="dealer_id"]').val(res.dealer_id);
					$('input[name="dealer_name"]').val(res.dealer_name);
					$('input[name="branch"]').val(res.branch);
				}).fail((err) => {
					console.log(err['responseJSON']);
				});
			},
			delete_dealer(dealer_id) {
				swal({
					title: "Delete this Dealer?",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete) {
						$.ajax({
							type: 'DELETE',
							url: base_url + '/admin/dealers/delete/' + dealer_id,
						})
						.done((res) => {
							$('#dealer_' + res.dealer_id).remove();
							app.init();
							toastr.success('Successfully deleted!');
						})
						.fail((err) => {
							console.log(err['responseJSON']);
						});
					}
				});
			}
		}
		app.init();
	});

	$(document).on('submit', '#create_dealer', function(event) {
		event.preventDefault();

		var dealer_id = $('input[name="dealer_id"]').val();
		var url = base_url + '/admin/dealers/post';
		if (crud.get_method() == 'PUT') {
			url = base_url + '/admin/dealers/put/' + dealer_id
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
			app.init();
			if (crud.get_method() == 'POST') {
				toastr.success('New Dealer has been added.', 'Success!');
				crud.reset_form('create_dealer');
			}
			else {
				toastr.success('Successfully updated!');
				crud.close_modal('dealer_modal');
			}
		})
		.fail((err) => {
			console.log(err['responseJSON']);
		});
	});
</script>
@endpush