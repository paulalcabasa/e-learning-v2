@extends('admin_template') 

@push('styles')
	<style>[v-cloak] { display: none; }</style>
	<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
	<style>
		#module_names:hover {
			background-color: darkred;
		}
	</style>
@endpush 

@section('content')
<div v-cloak>
	<section class="content-header clearfix">
		<h1>
			<span class="pull-left" style="font-size: 18px; margin-top: 7px; margin-right: 10px;">Select</span>
			<div class="dropdown pull-left">
				<button class="btn btn-flat btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					Modules
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu flat bg-red" aria-labelledby="dropdownMenu1">
					@foreach($modules as $module) 
						<li id="module-list">
							<a href="{{ url('/admin/submodules/' . $module->module_id) }}" id="module_names" style="color: white;">
								<i class="fas fa-caret-right"></i>
								<span>{{ $module->module }}</span>
							</a>
						</li>
					@endforeach
				</ul>
			</div>
		</h1>
	</section>

	<section class="content container-fluid">
		@if (isset($single_module))
		<div class="box box-danger sub-content shadow-lg">
			<div class="box-header with-border clearfix">
				<h3 class="pull-left">
					<i class="fas fa-wrench"></i>
					{{ $single_module->module }}
				</h3>
				<a href="javascript:sm.show_create_modal()" class="btn btn-sm btn-danger pull-right">
					<i class="fa fa-plus"></i>
					Add Submodule
				</a>
			</div>
			<div class="box-body">
				<table id="datatable" class="table table-responsive table-striped table-hover">
					<thead>
						<tr>
							<th width="25px">&nbsp;</th>
							<th>Submodule</th>
							<th width="100px">&nbsp;</th>
						</tr>
					</thead>
					<tbody id="sub-module-tbody"></tbody>
				</table>
			</div>
		</div>
		@else
		<div class="box box-danger sub-content">
			<div class="box-header with-border clearfix">
				<h3 class="pull-left">
					
				</h3>
				<a href="javascript:sm.show_create_modal()" class="btn btn-sm btn-danger pull-right">
					<i class="fa fa-plus"></i>
					Add Submodule
				</a>
			</div>
			<div class="box-body">
				<v-layout row wrap justify-center>
					<div style="margin: 200px;">
						<h3 class="grey--text">
							<v-icon style="margin: 4px;">fas fa-smile-wink</v-icon> 
							Select module first up there.
						</h3>
					</div>
				</v-layout>
			</div>
		</div>
		@endif
	</section>

	<!-- Modal -->
	<div class="modal" id="create_sub_module_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<form id="create_sub_module" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="_method">
						@csrf
						
						<input type="hidden" name="sub_module_id">
						<input type="hidden" name="module_id">
						<div class="form-group">
							<label for="sub-module">
								Sub-module Name
								<span class="text-danger">**</span>
							</label>
							<input type="text" class="form-control" name="sub_module" id="sub_module" required autofocus>
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
<script>new Vue({el: '#app'});</script>
<script>
	$('#sub_module_tab').addClass('active bg-red');
	$('#modules_treeview').addClass('active');
	var base_url = "{{ url('/') }}";
	var current_module_id = @if(isset($single_module)) {{ $single_module->module_id }} @else '' @endif;
	var sm = null;
	$(function() {
		sm = {
			init() {
				$.ajax({
					type: 'GET',
					url: base_url + '/admin/submodules/get/' + current_module_id
				})
				.done(function(res) {
					var counter = 0;
					var submodules = res.submodules;
					$("#sub-module-tbody tr").remove();
					jQuery.each(submodules, function(i, val) {
						counter += 1;
						$("#sub-module-tbody").append(`
						<tr>
							<td>
								<a href="`+ base_url +`/admin/submodule/`+ val.sub_module_id +`/questions">
									<i class="fa fa-folder-open text-orange"></i>
								</a>
							</td>
							<td>`+ counter + ". " + val.sub_module +`</td>
							<td>
								<a href="javascript:sm.show_update_modal(`+ val.sub_module_id +`)" class="btn">
									<i class="fa fa-pen text-primary"></i>
								</a>
								<a href="javascript:sm.delete(`+ val.sub_module_id +`)" class="btn">
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
			show_create_modal() {
				crud.reset_form('create_sub_module');
				crud.set_method('POST');
				$('.modal-title').text('Add Submodule');
				$('input[name="module_id"]').val(current_module_id);
				$('#create_sub_module_modal').modal('show');
			},
			show_update_modal(submodule_id) {
				$.ajax({
					type: 'GET',
					url: base_url + '/admin/submodule/get/' + submodule_id
				})
				.done(function(res) {
					crud.set_method('PUT');
					$('.modal-title').text('Update Submodule');
					$('input[name="module_id"]').val(current_module_id);
					$('input[name="sub_module_id"]').val(res.sub_module_id);
					$('input[name="sub_module"]').val(res.sub_module);
					$('#create_sub_module_modal').modal('show');
				}).fail(function(err) {
					console.log(err['responseJSON']);
				});
			},
			delete(id) {
				swal({
					title: "Delete this submodule?",
					text: "Questions and choices will also be deleted.",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete) {
						$.ajax({
							type: 'DELETE',
							url: base_url + '/admin/submodules/delete/' + id
						})
						.done(function(res) {
							sm.init();
							toastr.success(res.sub_module + 'has been deleted', 'Success!');
						})
						.fail(function(err) {
							console.log(err['responseJSON']);
						});
					}
				});
			}
		};
		sm.init();
	});

	$(document).on('submit', '#create_sub_module', function(event) {
		event.preventDefault();

		var url = base_url + '/admin/submodules/create';
		if (crud.get_method() == 'PUT') {
			url = base_url + '/admin/submodules/update/' + $('input[name="sub_module_id"]').val();
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
			sm.init();
			crud.reset_form('create_sub_module');
			if (crud.get_method() == 'POST') {
				toastr.success('New Submodule has been added.', 'Success!');
			}
			else {
				toastr.success(res.sub_module + ' has been updated.', 'Success!');
				crud.close_modal('create_sub_module_modal');
			}
		})
		.fail(function(err) {
			console.log(err['responseJSON']);
		});
	});
</script>
@endpush