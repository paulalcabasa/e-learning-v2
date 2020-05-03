@extends('admin_template')

@push('styles')
<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style>
    [v-cloak] { display: none; }
    .table > tbody > tr > td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div v-cloak>
    <section class="content-header">
        <h1>
            Create Module Schedule
		</h1>
    </section>
    <section class="content">
        <div class="row">
			<div class="col-md-12">
                <div class="box box-danger shadow-lg">
                    <div class="box-header with-border">
                        <v-menu offset-y>
                        <v-btn small class="bg-red"
                            slot="activator"
                            dark>
                            @{{ module.module  }}
                            <v-icon class="text-sm" style="margin-left: 10px;">fas fa-caret-down</v-icon>
                        </v-btn>
                        <v-list>
                            <v-list-tile
                                v-for="(module, index) in modules"
                                :key="index"
                                
                                v-on:click="pickedModule(module.module, module.module_id)">

                                <v-list-tile-title>
                                    <v-icon small>fas fa-caret-right</v-icon>
                                    @{{ module.module }}
                                </v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                        </v-menu>
                        <v-btn 
                            href="{{ url("/admin/module_schedules") }}"
                            class="pull-right bg-red"
                            small>
                            <v-icon class="text-sm" style="margin-right: 5px;">fas fa-arrow-left</v-icon>
                            Back
                        </v-btn>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="50" class="text-center">#</th>
                                    <th class="text-center">Dealer</th>
                                    <th class="text-center">Branch</th>
                                    <th class="text-center" width="250">
                                        Start date
                                        <v-switch 
                                            color="green"
                                            label="copy all cells"
                                            v-model="checked_start_date"
                                            v-on:change="startDateMorphed">
                                        </v-switch>
                                    </th>
                                    <th class="text-center" width="250">
                                        End date
                                        <v-switch 
                                            color="green"
                                            label="copy all cells"
                                            v-model="checked_end_date" 
                                            v-on:change="endDateMorphed">
                                        </v-switch>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(dealer, index) in dealers">
                                    <td class="text-center">
                                        @{{ index + 1 }}
                                    </td>
                                    <td class="text-center">@{{ dealer.dealer_name }}</td>
                                    <td class="text-center">@{{ dealer.branch }}</td>

                                    <td class="text-center">
                                        <input type="date" class="form-control form-danger" 
                                        v-model="dealer.start_date" id="start_date">
                                    </td>
                                    <td class="text-center">
                                        <input type="date" class="form-control" 
                                        v-model="dealer.end_date" id="end_date" v-on:change="endDateChange">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-md-12">
                                <v-btn v-on:click="onSave" class="pull-right" small color="success" v-bind:disabled="disableToSave">
                                    Save Changes
                                </v-btn>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="{{ url('public/admin-lte/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('public/admin-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script>
    $('#module_schedule_tab').addClass('active bg-red');
    $('#exams_treeview').addClass('active');
    const base_url = "{{ url('/') }}";

    new Vue({
        el: '#app',
        data: function() {
            return {
                loading: true,
                dealers: [],
                modules: [],
                module: {
                    module_id: '',
                    module: 'Select Module'
                },
                checked_start_date: false,
                checked_end_date: false,
                disableToSave: false
            }
        },
        mounted: function() {
            this.getModules();
            this.getDealersModule();
        },
        methods: {
            getDealersModule: function() {
				axios.get(`${base_url}/admin/dealers/get`)
				.then(({data}) => {
					this.dealers = data.dealers;
					this.dealers.forEach(el => {
						el.start_date = '';
						el.end_date = '';
					});

					setTimeout(() => {
                        this.loading = false
                        // $('table').DataTable();
					})
				})
				.catch((err) => {
					console.log(err.response);
				});
            },
            getModules: function() {
				axios.get(`${base_url}/admin/modules/get`)
				.then(({data}) => {
					this.modules = data.modules;
				})
				.catch((err) => {
					console.log(err.response);
				});
            },
            onSave: function() {
                if (!this.module.module_id) {
                    swal("Select module!", "", "error");
                    return;
                }

                const data = [];
                this.dealers.forEach((el, index) => { // Filtering not empty dates
                    if (el.start_date && el.end_date) {
                        data.push(el)
                    }
                });
                
                let postData = {};
				data.forEach(el => {
					el._token = '{{ csrf_token() }}';
					el._method = 'POST';
                });

                // Request to be sent merged
                postData = {
                    dealer_schedules: data,
                    module_schedule: {
                        module_id: this.module.module_id,
                        created_by: "{{ title_case(session('full_name')) }}" //--> Change it to real user id
                    }
                }

				swal({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    buttons: {
                        cancel: true,
                        confirm: 'Proceed'
                    },
                    closeOnClickOutside: false
                })
                .then((res) => {
                    if (res)
                        return axios.post(`${base_url}/admin/module_details/post`, postData)
                            .then(({data}) => {
                                swal('New Schedule!', '', 'success', {timer:4000,button:false});
                                this.getDealersModule(this.module_schedule_id);
                            })
                            .catch((err) => {
                                console.log(err.response);
                                swal('Ooops!', 'Something went wrong.', 'error', {timer:4000,button:false});
                            });
                });
			},
            pickedModule: function(module, module_id) {
                this.module.module_id = module_id;
                this.module.module = module;
            },
            startDateMorphed: function() {
                if (this.checked_start_date) {
                    this.dealers.forEach(el => {
                        el.start_date = this.dealers[0].start_date;
                    });
                }
            },
            endDateMorphed: function() {
                if (this.checked_end_date) {
                    this.dealers.forEach(el => {
                        el.end_date = this.dealers[0].end_date;
                    });
                }
            },
            endDateChange(event) {
                var start_date = new Date(document.querySelector('#start_date').value);
                var end_date = '';
                if (start_date) {
                    end_date = new Date(event.target.value);
                    
                    if (start_date > end_date) {
                        this.disableToSave = true;
                        console.log('End date cannot be lower than start date');
                        swal('Ooops!', 'End date must ahead or equal to start date', 'error');
                    }
                    else {
                        this.disableToSave = false;
                    }
                }
            }
        }
    })
</script>
@endpush