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