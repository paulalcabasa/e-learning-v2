<div class="modal fade" id="trainor_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal_title"></h4>
            </div>
            <div class="modal-body">
                <form id="create_trainor" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_method">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="trainor_id">
                    
                    <div class="form-group">
                        <label for="module">Select Dealer<span class="text-danger">**</span></label>
                        <div class="row">
                            <div class="col-md-7">
                                <select 
                                class="form-control"
                                name="dealer_id" id="dealer_id"></select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-7">
                                <label for="">Firstname</label>
                                <input type="text" 
                                class="form-control"
                                name="fname" id="fname" required>
                            </div>
                            <div class="col-md-5">
                                <label for="">Middlename</label>
                                <input type="text" 
                                class="form-control"
                                name="mname" id="mname" placeholder="Optional field">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-7">
                                <label for="">Lastname</label>
                                <input type="text" 
                                class="form-control"
                                name="lname" id="lname" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="email" 
                        class="form-control"
                        name="email" id="email" required>

                        @include('validation.client_side_error', ['field_id' => 'email'])
                    </div>

                    <div class="form-group">
                        
                    </div>
                    
                    <div class="clearfix">
                        <button type="submit" class="btn btn-primary pull-right">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>