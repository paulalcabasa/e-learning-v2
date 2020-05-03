@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
<script src="{{ url('public/js/app.js') }}"></script>
<link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic" rel="stylesheet">
<link rel="stylesheet" href="{{ url('public/admin-lte/bower_components/Ionicons/css/ionicons.min.css') }}">	
<link rel="stylesheet" href="{{ url('public/admin-lte/dist/css/AdminLTE.min.css') }}">
<link rel="stylesheet" href="{{ url('public/admin-lte/dist/css/skins/skin-red.min.css') }}">
@endpush

@section('content')
<v-container>
    <v-layout>
        <v-flex md12 sm12>
            <div class="pad margin no-print">
                <div class="callout callout-danger" style="margin-bottom: 0!important;">
                    <h4>
                        <i class="fa fa-exclamation-circle"></i> 
                        Warning! 
                    </h4>
                    You are not yet <strong>allowed</strong> to access this page. Please contact IPC Administrator. <br> Thank you!
                    <button v-on:click="goBack('http://ecommerce5/ipc_central/index.php')" class="btn btn-xs btn-default">
                        <i class="fa fa-chevron-left"></i>
                        Go Back
                    </button>
                </div>
            </div>
        </v-flex>
    </v-layout>
</v-container>
@endsection

@push('scripts')
<script src="{{ url('public/admin-lte/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ url('public/admin-lte/dist/js/adminlte.min.js') }}"></script>
<script>
    const APP_URL = window.location.origin
    
    new Vue({
        el: '#app',
        methods: {
            goBack: function(url) {
                window.location.href = url;
            }
        }
    })
</script>
@endpush
