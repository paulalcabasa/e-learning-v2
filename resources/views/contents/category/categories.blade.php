@extends('admin_template') 

@push('styles')
	<style>[v-cloak] { display: none; }</style>
	<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endpush 

@section('content')
<div v-cloak>
	<section class="content-header">
		<h1>
			Categories
			<small>Optional description</small>
		</h1>
	</section>

	<section class="content container-fluid">
		<div class="box box-danger sub-content shadow-lg">
			<div class="box-header with-border clearfix">
				<a @click="add" data-toggle="modal" class="btn btn-sm btn-danger">
					<i class="fa fa-plus"></i>
					Add New Category
				</a>
			</div>
			<div class="box-body">
				<table id="datatable" class="table table-responsive table-striped table-hover">
					<thead>
						<tr>
							<th>Category</th>
							<th>Status</th>
                            <th></th>
						</tr>
					</thead>
					<tbody id="module-tbody">
                        <tr v-for="(row,index) in categories">
                            <td>@{{ row.category_name }}</td>
                            <td>
                            	<div :class="row.status == 'active' ? 'label label-success' : 'label label-danger'" style="padding: 5px 7px;">
									<i :class="row.status == 'active' ? 'fa fa-check-circle' : 'fa fa-times-circle'"  style="font-size: 14px;"></i>&nbsp;
									@{{ row.status }}
								</div>
                            </td>
                            <td>
                                <button @click="adminModal(row)" class="btn btn-xs btn-primary">
                                    <i class="fas fa-users"></i>
                                </button>
                                <button @click="edit(row)" class="btn btn-xs btn-success">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
				</table>
			</div>
		</div>
	</section>

    <div class="modal"  id="category_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Category Information</h4>
				</div>
				<div class="modal-body">
					<form id="create_module" method="POST" enctype="multipart/form-data">
					<!-- 	<input type="hidden" name="_method">
						@csrf
						<input type="hidden" name="module_id"> -->

                        <input type="hidden" v-model="category.category_id">

						<div class="form-group">
							<label for="module">
								Category Name 
								<span class="text-danger">**</span>
							</label>
							<input type="text" class="form-control" v-model="category.category_name" autofocus>
						</div>

                        <div class="form-group" v-show="action == 'edit'">
							<label for="module">
								Status
								<span class="text-danger">**</span>
							</label>
							<select class="form-control" v-model="category.status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
						</div>
	
						<div class="clearfix">
							<button type="button" @click="saveCategory" class="btn btn-primary pull-right">Save</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

    <div class="modal"  id="admin_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">@{{ category.category_name }} Administrators</h4>
				</div>
				<div class="modal-body">
                    <table class="table table-responsive table-striped table-hover" id="categoryAdmins">
                        <thead>
                            <tr>
                                <th class="text-center">Name</th>
                                <th class="text-center">Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in categoryAdmins" v-bind:key="item.id">
                                <td class="text-center">@{{ item.admin_name }}</td>
                                <td class="text-center"><input type="checkbox" v-model="item.admin_flag" :checked="item.admin_flag"/></td>
                            </tr>
                        </tbody>
                    </table>
				</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click="saveAdmins">Save</button>
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
<script>

    const base_url = "{{ url('/') }}";
	$('#categories_tab').addClass('active bg-red');
	$('#modules_treeview').addClass('active');

    var app = new Vue({
        el: '#app',
        data() {
            return {
                categories : [],
                category : {
                    category_id : '',
                    category_name : '',
                    status : ''
                },
                action : '',
                apiUrl : '',
                categoryAdmins : []
            }
        },
        mounted(){
            this.loadCategories();
        },
        created() {
            
        },
        methods: {
            loadCategories(){
                axios.get('category/get').then(res => {
                    this.categories = res.data;
                }).then( () => {
                    $("#datatable").DataTable();
                }).catch(err => {
                    toastr.error(err, 'Error!');
                }); 
            },
            saveCategory(){
                var self = this;
                axios.post(self.apiUrl,{
                    category : self.category
                }).then(res => {
                    self.loadCategories();
                    toastr.success(res.data.message, 'Success!');
                    if(self.action == "add"){
                        self.resetCategory();
                    }  
                }).catch(err => {
                    toastr.error(err, 'Error!');
                });               
            },
            edit(row){
                this.category.category_id = row.id;
                this.category.category_name = row.category_name;
                this.category.status = row.status;
                this.action = 'edit';
                this.apiUrl = base_url + '/admin/category/update';
                $("#category_modal").modal('show');
            },
            add(){
                this.action = 'add';
                this.resetCategory();
                this.apiUrl = base_url + '/admin/category/add';
                $("#category_modal").modal('show');
            },
            resetCategory(){
                this.category.category_id = '';
                this.category.category_name = '';
                this.category.status = '';
            },
            adminModal(row){
                axios.get('category/admin/get/' + row.id)
                    .then(res => {
                        this.categoryAdmins  = res.data;
                    }).catch(err => {

                    });
                this.category.category_id = row.id;
                this.category.category_name = row.category_name;
                this.category.status = row.status;
                $("#admin_modal").modal('show');
            },
            saveAdmins(){
                axios.post('category/admin/save',{
                    category_id : this.category.category_id,
                    categoryAdmins : this.categoryAdmins
                }).then(res => {
                    toastr.success(res.data.message, 'Success!');
                }).catch(err => {
                    toastr.error(err, 'Error!');
                });
                
                
            }
        }
    })
</script>
@endpush