@extends('layouts.app') 

@push('styles')
    <style>
        #__module_image_container {
            background-image: url(/e-learning/public/images/online_training-1280x640.jpg);
            background-repeat: no-repeat;
            background-position: 0 0;
            background-size: cover;
        }
    </style>
@endpush

@section('content')
<v-container>
    <v-layout>
        <v-flex md12 sm12>
            <v-card class="elevation-0" id="__module_image_container">
                <v-container class="pb-0" fluid>
                    <v-layout>
                        <v-flex xs12 align-end flexbox>
                            <span class="headline font-weight-regular white--text">
                                @{{ category.category_name }}
                            </span>
                        </v-flex>
                    </v-layout>
                </v-container>

                <v-container fluid grid-list-lg>
                    <v-layout row wrap>
                        <v-flex xs12 sm6 md4 v-for="(data, index) in trainor_modules" :key="data.index">
                            <v-card color="grey lighten-5" hover style="min-height: 100%; max-height: 100%;">
                                <v-card-title primary-title>
                                    <div>
                                        <h3 class="headline font-weight-medium mb-0">
                                            <v-icon class="mb-1" color="success darken-2">highlight</v-icon>
                                            @{{ data.module }}
                                        </h3>
                                        <div class="grey--text">@{{ data.description }}</div>
                                    </div>
                                </v-card-title>

                                <v-card-actions>
                                    <v-spacer></v-spacer>

                                    <div v-if="data.module_details.length > 0">
                                        <v-menu :close-on-content-click="false" bottom origin="center center" transition="scale-transition" small>

                                            <v-btn slot="activator" dark flat color="green">
                                                Click here!
                                            </v-btn>

                                            <v-list>
                                                <v-list-tile v-for="(module_detail, index) in data.module_details" :key="index">

                                                    <v-icon>keyboard_arrow_right</v-icon>
                                                    <div v-if="module_detail.status == 'on_progress'">
                                                        <v-btn 
                                                            v-if="module_detail.is_enabled" 
                                                            v-on:click="viewPDF(module_detail.module_detail_id, data.file_name, data.category_id)" 
                                                            block 
                                                            flat 
                                                            color="green">
                                                            Ready to open!
                                                        </v-btn>
                                                        <v-btn v-else block flat color="grey">
                                                            Disabled
                                                        </v-btn>
                                                    </div>
                                                    <v-btn v-else-if="module_detail.status == 'waiting'" block flat color="blue darken-2">
                                                        Will be available on @{{ module_detail.start_date | dateFormat }}
                                                    </v-btn>
                                                    <v-btn v-else block flat color="grey">
                                                        Ended at @{{ module_detail.end_date | dateFormat }}
                                                    </v-btn>
                                                </v-list-tile>
                                            </v-list>
                                        </v-menu>
                                    </div>
                                    <v-btn v-else flat color="grey">
                                        Unavailable
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card>
        </v-flex>
    </v-layout>
</v-container>
@endsection
@push('scripts')
<script>
    const user_id = "{{ str_replace_last('trainor_', '', Auth::user()->app_user_id) }}";
    const base_url = "{{ url('/') }}";
    new Vue({
        el: '#app',
        data() {
            return {
                trainor_modules: {!! json_encode($trainor_modules) !!},
                modules: [],
                category : {!! json_encode($category) !!}
            }
        },
        created() {
          //  this.fetchTrainorModules();
           // this.fetchModules();
            this.module_tab = 'red';
        },
        methods: {
            fetchTrainorModules: function() {
                axios.get(`${base_url}/trainor/trainor_modules/get/${user_id}`)
				.then(({data}) => {
                    this.trainor_modules = data.modules;
				})
				.catch((err) => {
					console.log(err.response.data);
				});
            },
            fetchModules: function() {
                axios.get(`${base_url}/admin/get_modules`)
				.then(({data}) => {
                    this.modules = data;
				})
				.catch((err) => {
					console.log(err.response);
				});
            },
            viewPDF: function(module_detail_id, file, category_id) {
                axios.put(`${base_url}/trainor/trigger_module/${module_detail_id}/${user_id}`)
                .then(({data}) => {
                    if (data) {
                        window.location = `${base_url}/pdf_viewer/${file}/${module_detail_id}/${category_id}`;
                    }
                })
                .catch((err) => {
                    console.log(err.response);
                });
            }
        }
    })
</script>
@endpush