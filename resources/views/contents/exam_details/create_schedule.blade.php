@extends('admin_template')

@push('styles')
<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style>
    [v-cloak] { display: none; }
</style>
@endpush

@section('content')
<div v-cloak>
    <section class="content-header">
        <h1>
            <i class="fas fa-calendar-alt"></i>
            Create Examination Schedule
		</h1>
    </section>
    <section class="content">
        <div class="row">
			<div class="col-md-12">
                <div class="box box-danger shadow-lg">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-12">
                
                                <form class="form-inline">
                                    <div class="form-group">
                                        <v-menu offset-y
                                                transition="slide-y-transition"
                                                bottom>
                                            <v-btn small class="bg-red"
                                                style="width: 100%;"
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
                                    </div>
                                    <div class="form-group">
                                        <v-btn 
                                            style="width: 300px;"
                                            v-on:click="pickedSubModules"
                                            small 
                                            class="bg-red"
                                            dark>
                                            Select Submodules | total item(s): @{{ total_items }}
                                        </v-btn>
                                    </div>
                                    <div class="form-group">
                                        <label class="ml-2">Timer</label>
                                        <input 
                                        type="number" 
                                        v-model.number="timer" 
                                        pattern="/^(0|[1-9][0-9]*)$/"
                                        class="form-control" 
                                        placeholder="minutes">
                                    </div>
                                    <div class="form-group">
                                        <label class="ml-2">Passing Score</label>
                                        <input 
                                        type="number" 
                                        v-model="passing_score" 
                                        pattern="/^(0|[1-9][0-9]*)$/"
                                        class="form-control" 
                                        placeholder="score">
                                    </div>
                                    <v-btn 
                                        href="{{ url("/admin/exam_schedules") }}"
                                        class="pull-right bg-red"
                                        small>
                                        <v-icon class="text-sm" style="margin-right: 5px;">fas fa-arrow-left</v-icon>
                                        Back
                                    </v-btn>
                                </form>
                                    
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="50" class="text-center">#</th>
                                    <th>Dealer</th>
                                    <th>Branch</th>
                                    <th width="250">
                                        Start date
                                        <v-switch 
                                            color="green"
                                            label="copy all cells"
                                            v-model="checked_start_date"
                                            v-on:change="startDateMorphed">
                                        </v-switch>
                                    </th>
                                    <th width="250">
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
                                    <td><strong>@{{ dealer.dealer_name }}</strong></td>
                                    <td>@{{ dealer.branch }}</td>

                                    <td class="text-xs-center">
                                        <input type="date" class="form-control" 
                                        v-model="dealer.start_date" id="start_date">
                                    </td>
                                    <td class="text-xs-center">
                                        <input type="date" class="form-control" 
                                        v-model="dealer.end_date" v-on:change="endDateChange">
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

    <div v-if="isModalShown">
        <div class="modal" style="display: block">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button v-on:click="isModalShown = false" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            Pick submodules and items
                        </h4>
                    </div>
                    
                    <div class="modal-body vue-modal">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center" width="50px">#</th>
                                    <th>Submodules</th>
                                    <th>Question(s)</th>
                                    <th class="text-center" width="50px">Quantity</th>
                                    <th class="text-center" width="50px">Include</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(submodule, index) in submodules">
                                    <td class="text-center">@{{ index + 1 }}</td>
                                    <td class="text-primary">@{{ submodule.sub_module }}</td>
                                    <td class="text-primary text-center">@{{ submodule.questions }}</td>
                                    <td>
                                        <v-text-field
                                        style="margin-top: 0px; margin-bottom: -5px;"
                                        v-if="submodule.isSelected" 
                                        v-on:change="countPickedSubmodules"
                                        v-model.number="submodule.items"
                                        :rules="[rules.required, rules.numbers]"
                                        ></v-text-field>
                                    </td>
                                    <td class="text-center">
                                        <v-checkbox 
                                        v-model="submodule.isSelected" color="green"
                                        hide-details></v-checkbox>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <v-btn v-on:click="isModalShown = false" class="pull-right" small color="red darken-1" dark>
                            Save Changes
                        </v-btn>
                        <v-chip color="green" text-color="white" class="pull-right">
                            <v-avatar class="green darken-4">@{{ total_items }}</v-avatar>
                            Total Items
                        </v-chip>
                        <div v-if="submodules == ''">
                            <v-alert
                                :value="true"
                                type="error">
                            Oops! Sorry, you haven't added submodules yet here.
                            </v-alert>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ url('public/admin-lte/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('public/admin-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script>
    $('#exam_schedule_tab').addClass('active bg-red');
    $('#exams_treeview').addClass('active');
    const base_url = "{{ url('/') }}";

    new Vue({
        el: '#app',
        data: function() {
            return {
                isModalShown: false,
				isHidden: true,
                loading: true,
                dealers: [],
                modules: [],
                module: {
                    module_id: '',
                    module: 'Select Module'
                },
                submodules: [],
                checked_start_date: false,
                checked_end_date: false,
                total_items: '0',
                timer: null,
                passing_score: null,

                // Rules
                rules: {
                    required: value => !!value || 'Required.',
                    numbers: value => {
                        const pattern = /^(0|[1-9][0-9]*)$/;
                        return pattern.test(value) || 'Numbers Only.';
                    }
                },

                disableToSave: false
            }
        },
        watch: {
            timer: function() {
                const pattern = /^(0|[1-9][0-9]*)$/;
                if (!pattern.test(this.timer)) this.timer = null;
            },
            passing_score: function() {
                const pattern = /^(0|[1-9][0-9]*)$/;
                if (!pattern.test(this.passing_score)) this.passing_score = null;
            }
        },
        created: function() {
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
                if (!this.module.module_id) return swal("Select module!", "", "error");
                if (!this.submodules.length > 0) return swal("Pick submodules!", "", "error");
                if (!this.timer) return swal("Please provide timer!", "", "error");
                if (!this.passing_score) return swal("Please provide Passing Score!", "", "error");

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
                    exam_details: data,
                    exam_schedule: {
                        module_id: this.module.module_id,
                        status: 'on_going',
                        timer: this.timer,
                        passing_score: this.passing_score,
                        created_by: "{{ title_case(session('full_name')) }}"
                    },
                    question_details: this.submodules
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
                        return axios.post(`${base_url}/admin/exam_schedules/post`, postData)
                            .then(({data}) => {
                                toastr.success('', 'Successfully saved!');
                                this.getDealersModule();
                                
                                // Created
                                this.module.module = 'Select Module';
                                this.submodules = [];
                                this.timer = null;
                                this.passing_score = null;
                                this.checked_start_date = false;
                                this.checked_end_date = false;
                                this.total_items = 0;
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
                this.submodules = [];
            },
            pickedSubModules: function() {
                if (!this.module.module_id) return swal("Select module!", "", "error");

                if (this.submodules.length > 0) return this.isModalShown = true;
                
                axios.get(`${base_url}/admin/submodules/get/${this.module.module_id}`)
                .then(({data}) => {
                    this.submodules = data.submodules;
                    this.isModalShown = true;
                })
                .catch((err) => {
                    console.log(err.response);
                });
            },
            countPickedSubmodules: function() {
                var sum = 0;
                this.submodules.forEach(el => {
                    if (el.isSelected) sum += parseFloat(el.items);
                });
                
                if (!isNaN(sum)) return this.total_items = sum;
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