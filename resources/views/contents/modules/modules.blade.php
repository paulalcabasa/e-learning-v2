@extends('admin_template') 

@push('styles')
	<style>[v-cloak] { display: none; }</style>
	<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endpush 

@section('content')
<div v-cloak>
	<section class="content-header">
		<h1>
			Modules
			<small>Optional description</small>
		</h1>
	</section>

	<section class="content container-fluid">
		<div class="box box-danger sub-content shadow-lg">
			<div class="box-header with-border clearfix">
				<a href="javascript:module.show_modal('Create Module', '1', '#create_module')" class="btn btn-sm btn-danger">
					<i class="fa fa-plus"></i>
					Add New Module
				</a>
			</div>
			<div class="box-body">
				<table id="datatable" class="table table-responsive table-striped table-hover">
					<thead>
						<tr>
							<th width="25px">&nbsp;</th>
							<th>Modules</th>
							<th>Description</th>
							<th>PDF</th>
							<th width="25px">&nbsp;</th>
						</tr>
					</thead>
					<tbody id="module-tbody"></tbody>
				</table>
			</div>
		</div>
	</section>

	<!-- Modal -->
	<div class="modal" id="create_module_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<form id="create_module" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="_method">
						@csrf
						<input type="hidden" name="module_id">
	
						<div class="form-group">
							<label for="module">
								Module Name 
								<span class="text-danger">**</span>
							</label>
							<input type="text" class="form-control" name="module" id="module" required autofocus>
						</div>
	
						<div class="form-group">
							<label for="module">Description</label>
							<textarea class="form-control" name="description" id="description" cols="30" rows="3" placeholder="Optional field"></textarea>

							@include('validation.client_side_error', ['field_id' => 'description'])
						</div>
	
						<div class="form-group">
							<label for="file_name">Upload PDF File</label>
							<small style="color: gray;">( optional )</small>
							<input type="file" class="form-control-file" name="file_name" id="file_name">
	
							@include('validation.client_side_error', ['field_id' => 'file_name'])
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
<script>new Vue({el: '#app'})</script>
<script>
	const base_url = "{{ url('/') }}";
	$('#module_tab').addClass('active bg-red');
	$('#modules_treeview').addClass('active');
	var will_create = 1; // true = create, false = update
	var module = null;
	var module_method = 'POST';
	
	$(function() {
		module = {
			init() {
				$.ajax({
					type: 'GET',
					url: base_url + '/admin/get_modules'
				})
				.done(function(res) {
					var counter = 0;
					$("#module-tbody tr").remove();
					jQuery.each(res, function(i, val) {
						counter += 1;
						var description = val.description ? val.description : '(no description specified)';
						$("#module-tbody").append(`
						<tr>
							<td>
								<a href="`+ base_url +`/admin/modules/`+ val.module_id +`">
									<i class="fa fa-folder-open text-orange"></i>
								</a>
							</td>
							<td>`+ counter + ". " + val.module +`</td>
							<td>`+ description +`</td>
							<td>`+ val.file_name +`</td>
							<td>
								<a href="javascript:module.delete_module(`+ val.module_id +`)">
									<i class="fa fa-trash text-danger"></i>
								</a>
							</td>
						</tr>
						`);
					});
					$('#datatable').DataTable();
				}).fail(function(err) {
					console.log(err['responseJSON']);
				});
			},
			delete_module(id) {
				swal({
					title: "Delete this module?",
					text: "Once deleted, you will be able to delete all its submodules!",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete) {
						$.ajax({
							type: 'DELETE',
							url: base_url + '/admin/delete_module/' + id,
						})
						.done(function(res) {
							module.init();
							toastr.success('Module has been deleted', 'Success!');
						})
						.fail(function(err) {
							console.log(err['responseJSON']);
						});
					}
				});
			},
			show_module(id) {
				$.ajax({
					type: 'GET',
					url: base_url + base_url + '/admin/get_module/' + id,
				})
				.done(function(res) {
					will_create = 0;
					module.set_method('PUT');
					module.show_modal('Update Module', 0);
					$('input[name="module_id"]').val(res.module_id);
					$('input[name="module"]').val(res.module);
					$('textarea[name="description"]').val(res.description);
				})
				.fail(function(err) {
					console.log(err['responseJSON']);
				});
			},
			// modal parameters ['modal_title', 'will_create', 'form_id']
			show_modal(title, will_create, form_id) {
				if (will_create) {
					module.reset_modal(form_id);
					module.set_method('POST');
				}
				
				$('.modal-title').text(title);
				$('#create_module_modal').modal('show').map(function() {
					$('#module').focus();
				});
			},
			reset_modal(form_id) {
				$(form_id)[0].reset();
			},
			set_method(method) {
				$('input[name="_method"]').val(method);
			},
			get_method() {
				return $('input[name="_method"]').val();
			}
		};
		module.init();
	});
	
	$(document).on('submit', '#create_module', function(event) {
		event.preventDefault();
		var url = base_url + '/admin/create_module';
		
		if (module.get_method() == 'PUT') {
			url = base_url + '/admin/update_module/' + $('input[name="module_id"]').val();
		}
		
		$.ajax({
			type: 'POST',
			url: url,
			data: new FormData($(this)[0]),
			contentType: false,
			cache: false,
			processData: false,
		})
		.done(function(res) {
			module.init();
			crud.reset_form('create_module');
			if (module.get_method() == 'POST') {
				toastr.success('New Module has been added.', 'Success!');
			}
			else {
				toastr.success(res.module + ' module has been updated.', 'Success!');
			}
		})
		.fail(function(err) {
			console.log(err);
			if (err['responseJSON']['errors']['file_name']) 
				$('#file_name_err').text(err['responseJSON']['errors']['file_name'][0]);
			else if (err['responseJSON']['errors']['description']) 
				$('#description_err').text(err['responseJSON']['errors']['description'][0]);
		});
	});
	
</script>
@endpush