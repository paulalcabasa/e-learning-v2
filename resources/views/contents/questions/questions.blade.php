@extends('contents.sub-modules.sub_module')

@push('styles')
    <style>[v-cloak] { display: none; }</style>
    <style>
        #question_content {
            max-height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
        }
    </style>
@endpush

@section('questions')
<div v-cloak>
    <div class="row">
        <div class="col-md-12">
            <a href="{{ url('/admin/submodule/'. Request::segment(3) .'/questions/create') }}" class="btn btn-sm btn-danger" style="margin-bottom: 10px;">
                <i class="fa fa-plus"></i>
                Add Question
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" id="question_content">
            <div id="scrollable">
                <ul class="list-group">
                    <?php $i = 1 ?>
                    @foreach($questions as $quest)
                        <li class="list-group-item" id="question_id_{{ $quest->question_id }}">
                            <div class="row clearfix">
                                <div class="col-md-12">
                                    <v-btn 
                                        href="javascript:quest.will_delete_modal({{ $quest->question_id }})"
                                        class="btn pull-right"
                                        slot="activator" 
                                        icon>
                                    <v-icon small color="red darken-2">fas fa-trash</v-icon>
                                    </v-btn>
                                    <v-btn 
                                        href="{{ url('/admin/submodule/'.Request::segment(3).'/questions/edit/'.$quest->question_id) }}"
                                        class="btn pull-right"
                                        slot="activator" 
                                        icon>
                                    <v-icon small color="primary">fas fa-edit</v-icon>
                                    </v-btn>

                                    <h4 class="list-group-item-heading" style="margin-bottom: 12px;">
                                        <span class="counter" id="count_{{ $i }}">{{ $i }}</span>. {{ $quest->question }}
                                    </h4>
                                </div>
                            </div>
                            
                            @foreach($quest->choices as $choice)
                                <div class="row" style="font-size:16px;">
                                    <div class="col-md-12">
                                        <div class="row" style="margin-bottom: 5px;">
                                            <div class="col-md-1 clearfix">
                                                <span class="pull-right">
                                                    @if ($choice->is_correct)
                                                        <i class="fa fa-check text-success"></i>    
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="col-md-11">
                                                <span style="margin-right: 10px;">{{ $choice->choice_letter }}.</span> {{ $choice->choice }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </li>
                    <?php $i++ ?>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>new Vue({el: '#app'})</script>
    <script>
        var base_url2 = "{{ url('/') }}";
        $('#sub_module_tab').addClass('active bg-red');
        $('#modules_treeview').addClass('active');
        $('#back').attr('href', base_url2 + '/admin/submodules/{{ $module->module_id }}');
        var quest = null;
        $(function() {
            quest = {
                init() {
                    var counts = $('.counter').length + 1;
                    var count_set = [];
                    var num = 0;
                    for (let i = 1; i < counts; i++) {
                        num += 1;
                        count_set.push(i);
                        $('#count_' + i).text(num);
                    }
                    
                    // $('#scrollable').slimScroll({
                    //     height: 'auto'
                    // });
                },
                will_delete_modal(question_id) {
                    swal({
                        title: "Delete this question and choice?",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                type: 'DELETE',
                                url: base_url2 + '/admin/submodule/{{ Request::segment(3) }}/questions/delete/' + question_id,
                            })
                            .done(function(res) {
                                $('#question_id_' + res.question_id).remove();
                                quest.init();
                                // console.log(res);
                                // toastr.success('Question has been deleted', 'Success!');
                            })
                            .fail(function(err) {
                                console.log(err['responseJSON']);
                            });
                        }
                    });
                }
            }
            quest.init();
        });
    </script>
@endpush