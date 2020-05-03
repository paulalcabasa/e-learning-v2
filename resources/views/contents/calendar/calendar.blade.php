@extends('admin_template') 

@push('styles')
    <style>[v-cloak] { display: none; }</style>
    <script src="{{ url('public/libraries/moment.js') }}"></script>
    <script src="{{ url('public/libraries/full-calendar/fullcalendar.min.js') }}"></script>
    <link rel="stylesheet" href="{{ url('public/libraries/full-calendar/fullcalendar.min.css') }}">
@endpush

@section('content')
    <div v-cloak>
        <section class="content-header">
            <h1>Calendar</h1>
        </section>

        <section class="content container-fluid">
            <div class="box box-danger sub-content shadow-lg">
                <div class="box-header with-border" style="height: 78px;">

                    <div class="row">
                        <div class="col-md-2">
                            <label>Filter by:</label>
                            <div class="input-group">
                                <select name="filter" id="filter" class="form-control" >
                                    <option value="all">ALL</option>
                                    <option value="pdf">MODULE</option>
                                    <option value="exam">EXAM</option>
                                </select>
                                <span class="input-group-addon"><i class="fa fa-chevron-down"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6 pull-right">
                            <h4 class="box-title pull-right">
                                Legend:
                                <div class="label label-primary py-0 ml-3">MODULE</div>
                                <div class="label label-success py-0">EXAM</div>
                                <div class="label label-danger py-0">Ended</div>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    {!! $calendar->calendar() !!}
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    {!! $calendar->script() !!}
    <script>
        $('#calendar_tab').addClass('active bg-red');
        // Vue Instance
        new Vue({
            el: '#app',
            data() {
                return {
                    filter: ''
                }
            },
            mounted() {
                $('#filter').val('{{ isset($_GET["filter"]) ? $_GET["filter"] : "all" }}');
            },
        });

        $(document).ready(() => {
            $('select[name="filter"]').change(() => {
                window.location.href = `{{ url('/admin/calendar/get?filter=') }}${$('select[name="filter"]').val()}`;
            });
        });
    </script>
@endpush