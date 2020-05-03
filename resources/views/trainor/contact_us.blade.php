@extends('layouts.app')
    
@section('content')
    <v-container>
        <v-layout>
            <v-flex md12 sm12>
                <v-card>
                    <v-card-title primary-title>
                        <div>
                            <h3 class="headline mb-0">
                                <i class="ion ion ion-ios-telephone"></i>
                                Contact Us
                            </h3>
                        </div>
                    </v-card-title>
                    <v-card-title>
                        <v-container fluid grid-list-xl>
                            <v-layout row wrap>
                                <v-flex xs12 sm12 md6>
                                    <v-form ref="form" v-model="valid" lazy-validation>
                                        <v-text-field
                                        label="Full Name"
                                        id="fullname"
                                        value="{{ Auth::user()->name }}"
                                        outline
                                        readonly
                                        ></v-text-field>
                                        <v-text-field
                                        id="email"
                                        value="{{ Auth::user()->email }}"
                                        label="Email"
                                        outline
                                        readonly
                                        ></v-text-field>
                                        <v-text-field
                                        v-model="form.mobile"
                                        v-bind:rules="mobileRules"
                                        label="Mobile Number"
                                        outline
                                        required
                                        ></v-text-field>
                                        <v-text-field
                                        v-model="form.subject"
                                        label="Subject (Optional)"
                                        outline
                                        required
                                        ></v-text-field>
                                        <v-textarea
                                        v-model="form.message"
                                        v-bind:rules="messageRules"
                                        outline
                                        name="input-7-4"
                                        label="Message"
                                        ></v-textarea>
                                        <v-btn 
                                        v-on:click="sendNotification" 
                                        block color="red darken-1" 
                                        v-bind:disabled="!valid"
                                        dark>SEND MESSAGE</v-btn>
                                    </v-form>
                                </v-flex>
                                <v-flex xs12 sm12 md6>
                                    <v-card color="red darken-2" hover dark>
                                        <v-card-title primary-title>
                                            <div>
                                                <h3 class="headline mb-0 font-weight-medium">ISUZU PHILIPPINES CORPORATION</h3>
                                            </div>
                                        </v-card-title>
                                        <v-card-text class="subheading font-weight-regular">
                                            <strong>
                                                <v-icon>ion ion-ios-location</v-icon>&nbsp;
                                                114 Technology Avenue
                                            </strong> <br>
                                            <div class="ml-4">
                                                Phase II, Laguna Technopark <br>
                                                Binan Laguna, 4024 <br>
                                            </div>
                                        </v-card-text>
                                        <v-card-text class="subheading font-weight-regular">
                                            <strong>
                                                <v-icon>ion ion-ios-email</v-icon>&nbsp;
                                                Email
                                            </strong> <br>
                                            <div class="ml-4">
                                                aftersales-training@isuzuphil.com </br>
                                                nap-marquez@isuzuphil.com </br>
                                                roland-ramos@isuzuphil.com </br>
                                                carl-lat@isuzuphil.com </br>
                                                marilou-cabarles@isuzuphil.com
                                            </div>
                                        </v-card-text>
                                        <v-card-text class="subheading font-weight-regular">
                                            <strong>
                                                <v-icon>ion ion-android-phone-portrait</v-icon>&nbsp; 
                                                Mobile
                                            </strong> <br>
                                            <div class="ml-4">
                                                <strong>Globe:</strong> 09560899514 </br>
                                                <strong>Smart:</strong> 09183241224
                                            </div>
                                        </v-card-text>
                                        <v-card-text class="subheading font-weight-regular">
                                            <strong>
                                                <v-icon>ion ion-ios-telephone</v-icon>&nbsp;
                                                Landline
                                            </strong> <br>
                                            <div class="ml-4">
                                                <strong>Manila:</strong> (632) 757-6070 loc. 371 </br>
                                                <strong>Laguna:</strong> (049) 541-0224 loc. 371
                                            </div>
                                        </v-card-text>
                                    </v-card>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-title>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
@endsection

@push('scripts')
    <script>
        const user_id = "{{ str_replace_last('trainor_', '', Auth::user()->app_user_id) }}";
        new Vue({
            el: '#app',
            data() {
                return {
                    valid: true,
                    form: {
                        fullname: '',
                        email: '',
                        mobile: '',
                        subject: '',
                        message: ''
                    },

                    fullNameRules: [
                        v => !!v || 'Name is required',
                    ],
                    emailRules: [
                        v => !!v || 'Email is required',
                        v => /.+@.+/.test(v) || 'Email must be valid'
                    ],
                    mobileRules: [
                        v => !!v || 'Mobile is required',
                        v => (v && v.length <= 11) || 'Mobile Number must be 11 digits',
                        v => (v && !isNaN(v)) || 'Mobile Number must be a number'
                    ],
                    messageRules: [
                        v => !!v || 'Message is required'
                    ],
                }
            },
            created() {
                this.contact_us_tab = 'red';
            },
            methods: {
                sendNotification() {
                    this.form.fullname = document.getElementById('fullname').value;
                    this.form.email = document.getElementById('email').value;
                    axios.post(`${this.base_url}/trainor/send_notification`, this.form)
                    .then(({data}) => {
                        if (data) {
                            this.$refs.form.reset();
                            this.form = {};
                            return swal('Your message is successfully sent', 'Please wait for our response, Thankyou!', 'success');
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                        swal('Ooops!', 'Something went wrong.', 'error', {timer:4000,button:false});
                    });
                }
            }
        })
    </script>
@endpush
