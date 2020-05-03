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