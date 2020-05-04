<div class="modal fade" id="categories_modal" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">	
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    Allowed Categories | @{{ trainor.fname + " " + trainor.lname}}
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                       
                        <table class="table table-responsive table-striped table-hover" id="details_table">
                            <thead>
                                <tr>
                                    <th class="text-center">Category</th>
                                    <th class="text-center">Is Allowed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in allowed_categories" v-bind:key="item.id">
                                    <td class="text-center">@{{ item.category_name }}</td>
                                    <td class="text-center"><input type="checkbox" v-model="item.is_checked" :checked="item.is_checked"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                 
                    Close
                </button>
                <button type="button" class="btn btn-success" @click="saveTrainorCategories">

                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>