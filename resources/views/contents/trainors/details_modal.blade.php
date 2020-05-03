<div class="modal fade" id="details_modal" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">	
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    Trainings
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="ion ion-person"></i>
                                <label>Trainor:</label>&nbsp;
                                @{{ trainor.fname + ' ' + trainor.lname }}
                            </div>
                        </div>

                        <table class="table table-responsive table-striped table-hover" id="details_table">
                            <thead>
                                <tr>
                                    <th class="text-center" width="25px">#</th>
                                    <th class="text-center">Module</th>
                                    <th class="text-center">Date From</th>
                                    <th class="text-center">Date To</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in trainor_details" v-bind:key="item.training_history_id">
                                    <td class="text-center">@{{ index+1 }}</td>
                                    <td class="text-center">@{{ item.module }}</td>
                                    <td class="text-center">@{{ item.start_date | dateFormat }}</td>
                                    <td class="text-center">@{{ item.end_date | dateFormat }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="fa fa-times"></i>&nbsp;
                    Close
                </button>
            </div>
        </div>
    </div>
</div>