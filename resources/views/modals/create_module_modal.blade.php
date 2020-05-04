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