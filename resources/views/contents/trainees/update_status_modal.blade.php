<div class="modal fade" id="update_status_modal" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    Update Trainee Status
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Approve or Disapprove?</label>
                        </div>
                        <div class="form-group ml-5">
                            <button v-on:click="updateSaveStatus('1')" class="btn btn-success">
                                <i class="fa fa-check-circle"></i>&nbsp;
                                Approve
                            </button>
                            <button v-on:click="updateSaveStatus('2')" class="btn btn-danger">
                                <i class="fa fa-times-circle"></i>&nbsp;
                                Disapprove
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" data-dismiss="modal">
                    <i class="fa fa-times"></i>&nbsp;
                    Close
                </button>
            </div>
        </div>
    </div>
</div>