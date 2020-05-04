@extends('admin_template') 

@push('styles')
    <style>[v-cloak] { display: none; }</style>
    <link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endpush 

@section('content')
<div v-cloak>
    <section class="content-header">
        <a onclick="module.update_module({{ $module_id }})"
        class="btn btn-sm btn-danger pull-right">
            <i class="fas fa-pen"></i>
            EDIT    
        </a>
        <a href="{{ url('/admin/modules') }}" 
        class="btn btn-sm btn-default pull-right"
        style="margin-right: 5px;">
            <i class="fas fa-chevron-left"></i>
            BACK
        </a>
        <h1>
            <div id="module_title"></div>
            <small id="module_description"></small>
        </h1>
    </section>

    <section class="content container-fluid">
        <div class="box box-danger sub-content">
            <div id="pdf_container"></div>
        </div>
    </section>

    <!-- Update modal -->
    <div class="modal" id="update_module_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form id="update_module" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_method" value="PUT">
                        @csrf
                        <input type="hidden" name="module_id" id="module_id" value="{{ $module_id }}">
                        <input type="hidden" name="old_file_name" value="{{ $file_name }}">

                   

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
                            <label for="file_name">Upload New PDF File</label>
                            <small style="color: gray;">( optional )</small>
                            <input type="file" name="file_name" id="file_name">
    
                            @include('validation.client_side_error', ['field_id' => 'file_name'])
                            <br>
                            Previous file : <span class="text-danger">{{ $file_name }}</span>
                        </div>
                        
                        <div class="clearfix">
                            <button type="submit" class="btn btn-primary pull-right">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload modal -->
    <div class="modal" id="upload_pdf_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4>
                        <i class="fas fa-file-upload"></i>
                        Upload New PDF
                    </h4>
                </div>
                <div class="modal-body">
                    <form id="upload_pdf" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    
                        <input type="hidden" name="old_file_name" value="{{ $file_name }}">
                        <input type="hidden" name="module_id" id="module_id" value="{{ $module_id }}">
                        <div class="form-group">
                            <label for="file_name">PDF File</label>
                            <input type="file" name="file_name" id="file_name" required>
    
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
    $('#module_tab').addClass('active bg-red');
    $('#modules_treeview').addClass('active');
    const base_url = "{{ url('/') }}";
    var module = null;
    $(function() {
        module = {
            display_pdf(module_id) {
                $.ajax({
                    type: 'GET',
                    url: base_url + '/admin/module/display_pdf/' + module_id
                })
                .done(function(res) {
                    $('#pdf_container div').remove();
                    if (res.file_name) {
                        $('#pdf_container').append(`
                            <div class="box-header with-border clearfix"></div>
                            <div class="box-body">
                                <iframe src="{{ url("public/storage/ViewerJS/?zoom=1.5#../`+ res.file_name +`") }}" 
                                    width="100%" 
                                    height="700px" 
                                    allowfullscreen 
                                    webkitallowfullscreen
                                    class="iframe">
                                </iframe> 
                            </div>
                        `);
                    }
                    else {
                        $('#pdf_container').append(`
                            <div class="box-header with-border clearfix"></div>
                            <div class="box-body">
                                <br><br>
                                <div class="container">
                                    <div class="callout callout-warning text-center">
                                        <i class="fa fa-warning"></i>
                                        <a href="javascript:module.update_pdf()">Click here</a> to upload PDF.
                                    </div>
                                </div>
                            </div>
                        `);
                    }

                    $('#module_title').text(res.module);
                    $('#module_description').text(res.description);
                    $('.iframe').ready(function() {
                        setTimeout(function() {
                            $('.iframe').contents().find('#download').remove();
                            $('.iframe').contents().find('.about').remove();
                        }, 1000);
                    });
                })
                .fail(function(err) {
                    console.log(err['responseJSON']);
                });
            },
            update_module(id) {
                $.ajax({
                    type: 'GET',
                    url: base_url + '/admin/get_module/' + id
                })
                .done(function(res) {
                    $('input[name="module_id"]').val(res.module_id);
                    $('input[name="module"]').val(res.module);
                    $('textarea[name="description"]').val(res.description);
                    $('#update_module_modal').modal('show');
                })
                .fail(function(err) {
                    console.log(err['responseJSON']);
                });
            },
            update_pdf($id) {
                $('#upload_pdf_modal').modal('show');
            }
        };
        module.display_pdf({{ $module_id }});
    });

    $(document).on('submit', '#update_module', function(event) {
		event.preventDefault();
		var url = base_url + '/admin/update_module/' + $('#module_id').val();
		$.ajax({
			type: 'POST',
			url: url,
			data: new FormData($(this)[0]),
			contentType: false,
			cache: false,
			processData: false,
		})
		.done(function(res) {
			crud.reset_form('update_module');
            module.display_pdf({{ $module_id }});
            toastr.success('Successfully uploaded.', 'Success!');
            crud.close_modal('update_module_modal');
		})
		.fail(function(err) {
            console.log(err);
			// if (err['responseJSON']['errors']['file_name']) 
			// 	$('#file_name_err').text(err['responseJSON']['errors']['file_name'][0]);
            // else if (err['responseJSON']['errors']['description']) 
			// 	$('#description_err').text(err['responseJSON']['errors']['description'][0]);
		});
    });
    
    $(document).on('submit', '#upload_pdf', function(event) {
		event.preventDefault();
		var url = base_url + '/admin/upload_pdf/' + $('#module_id').val();
		$.ajax({
			type: 'POST',
			url: url,
			data: new FormData($(this)[0]),
			contentType: false,
			cache: false,
			processData: false,
		})
		.done(function(res) {
			crud.reset_form('upload_pdf');
            module.display_pdf({{ $module_id }});
            toastr.success('Successfully uploaded.', 'Success!');
            crud.close_modal('upload_pdf');
		})
		.fail(function(err) {
            console.log(err);
			if (err['responseJSON']['errors']['file_name']) 
				$('#file_name_err').text(err['responseJSON']['errors']['file_name'][0]);
		});
	});
</script>
@endpush