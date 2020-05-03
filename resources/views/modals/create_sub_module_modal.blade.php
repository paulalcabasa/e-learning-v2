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