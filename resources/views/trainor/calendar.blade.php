@extends('layouts.app') 

@push('styles')
    <link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <script src="{{ url('public/libraries/jquery.min.js') }}"></script>
    <script src="{{ url('public/admin-lte/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ url('public/libraries/moment.js') }}"></script>
    <script src="{{ url('public/libraries/full-calendar/fullcalendar.min.js') }}"></script>
    <link rel="stylesheet" href="{{ url('public/libraries/full-calendar/fullcalendar.min.css') }}">
@endpush

@section('content')
    <v-container>
        <v-layout>
            <v-flex md12 sm12>
                <v-card>
                    <v-container class="pb-0" fluid>
                        <v-layout>
                            <v-flex xs12 align-end flexbox>
                                <span class="headline font-weight-regular">
                                    <i class="ion ion-ios-calendar-outline"></i>
                                    Calendar
                                </span>
                            </v-flex>
                            <v-spacer></v-spacer>
                            <v-flex xs12 align-right flexbox class="text-lg-right">
                                <span class="title font-weight-light mr-4">
                                    Legend: 
                                </span>
                                {{-- <span class="title font-weight-light mr-3">
                                    <i class="ion ion-stop green--text title"></i>
                                    Module
                                </span> --}}
                                <span class="title font-weight-light mr-3">
                                    <i class="ion ion-stop blue--text title"></i>
                                    Exam
                                </span>
                                <span class="title font-weight-light">
                                    <i class="ion ion-stop red--text title"></i>
                                    Ended
                                </span>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    
                    <v-container fluid grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 sm12 md12 class="elevation-10">
                                {!! $calendar->calendar() !!}
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
@endsection
    
@push('scripts')
    {!! $calendar->script() !!}
    <script>
        const user_id = "{{ str_replace_last('trainor_', '', Auth::user()->app_user_id) }}";
        new Vue({
            el: '#app',
            data() {
                return {
                    
                }
            },
            created() {
                this.calendar_tab = 'red';
            },
            methods: {
                
            }
        })
    </script>
@endpush