@extends('layouts.app')

@section('content')
<v-container fluid>
    <v-layout>
        <v-flex md12 sm12>
            <v-container fill-height fluid>
                <v-layout fill-height>
                    <v-flex xs12 align-end flexbox>
                        <iframe 
                            v-bind:src="pdf_location" 
                            width="100%" height="700px" 
                            class="iframe"
                            allowfullscreen webkitallowfullscreen>
                        </iframe>
                        <v-btn v-on:click="doneReadingPDF" color="green" dark>
                            Done reading Module &nbsp;
                            <v-icon style="font-size: 19px;">fa fa-arrow-circle-right</v-icon>
                        </v-btn>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-flex>
    </v-layout>

    <!-- Dialog -->
    <v-dialog
        v-model="dialog"
        hide-overlay
        persistent
        width="300"
    >
        <v-card
            color="dark"
            dark
        >
            <v-card-text>
                PDF loading from the server
                <v-progress-linear
                    indeterminate
                    color="red darken-1"
                    class="mb-0"
                ></v-progress-linear>
            </v-card-text>
        </v-card>
    </v-dialog>

    <v-btn
        fab
        href="{{ Auth::user()->user_type == 'trainor' ? url('/trainor/modules') : url('/trainee/modules') }}"
        fixed
        dark
        bottom
        right
        color="red darken-1"
    >
        <v-icon>close</v-icon>
    </v-btn>
    
</v-container>
@endsection

@push('scripts')
<script>
    const TRAINOR_ID = "{{ str_replace_last('trainor_', '', Auth::user()->app_user_id) }}";
    const FILE = '{{ Request::segment(2) }}';
    const MODULE_DETAIL_ID = '{{ Request::segment(3) }}';

    new Vue({
        el: '#app',
        data() {
            return {
                dialog: false,
                pdf_location: '',
            }
        },
        watch: {
            dialog (val) {
                if (!val) return

                setTimeout(() => (this.dialog = false), 4000)
            }
        },
        mounted() {
            this.getPDF();
            this.module_tab = 'red';
        },
        methods: {
            getPDF() {
                this.dialog = true  
                let location = `{{ url("public/storage/ViewerJS/?zoom=1.75#../`+ FILE +`") }}`
                this.pdf_location = location
                setTimeout(function() {
                    $('.iframe').contents().find('#download').hide()
                    $('.iframe').contents().find('.about').remove()
                }, 1000);
            },
            doneReadingPDF() {
                swal({
                    title: "Done Reading PDF?",
                    icon: "warning",
                    buttons: true,
                })
                .then((res) => {
                    if (res) {
                        axios.put(`${this.base_url}/trainor/done_reading_pdf/${MODULE_DETAIL_ID}`)
                        .then(({data}) => {
                            swal('Alright!', '', 'success')
                            .then((res) => {
                                if (res) {
                                    window.location.href = "{{ route('trainor') }}";
                                }
                            });
                        })
                        .catch((error) => {
                            console.log(error.response);
                        });
                    }
                });
            }
        }
    })
</script>
@endpush
