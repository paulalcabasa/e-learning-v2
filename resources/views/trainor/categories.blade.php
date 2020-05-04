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
                                Select the category of your training
                            </span>
                        </v-flex>
                    </v-layout>
                </v-container>

                <v-container fluid grid-list-lg>
                    <v-layout row wrap>
                        <v-flex xs12 sm6 md4 v-for="(data, index) in categories" :key="data.index">
                            <v-card color="grey lighten-5" hover style="min-height: 100%; max-height: 100%;">
                                <v-card-title primary-title>
                                    <div>
                                        <h3 class="headline font-weight-medium mb-0">
                                            <v-icon class="mb-1" color="success darken-2">highlight</v-icon>
                                            @{{ data.category_name }}
                                        </h3>
                                        <div class="grey--text">@{{ data.description }}</div>
                                    </div>
                                </v-card-title>

                                <v-card-actions>
                                    <v-spacer></v-spacer>

                                    <div>
                                        <v-menu :close-on-content-click="false" bottom origin="center center" transition="scale-transition" small>

                                            <v-btn slot="activator" dark flat color="green" :href="'modules/' + data.id">
                                                Click here!
                                            </v-btn>

                                         \
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
    const base_url = "{{ url('/') }}";
    new Vue({
        el: '#app',
        data() {
            return {
                categories: {!! json_encode($trainorCategories)!!},
            }
        },
        created() {
            
          
            this.category_tab = 'red';
        },
        methods: {
        
           
          
        }
    })
</script>
@endpush