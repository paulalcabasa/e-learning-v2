@extends('layouts.app')

@section('content')
<v-container>
    <v-layout>
        <v-flex md12 sm12>
            <v-card>
                <v-container class="pb-0" fluid>
                    <v-layout>
                        <v-flex xs12 align-end flexbox>
                            <v-icon class="mr-1" style="font-size: 20px; margin-bottom: 2px;">fas fa-users</v-icon>
                            <span class="headline">Trainees</span>
                        </v-flex>
                    </v-layout>
                </v-container>

                <v-container fluid grid-list-md>
                    <v-layout row wrap>
                        <v-flex xs12 sm12 sm12 md12 lg12>
                            <v-card class="elevation-10">
                            
                                <v-layout row wrap>
                                    <v-flex xs12 sm6 md6 lg8>
                                        <v-btn
                                            v-on:click="openCreateDialog"
                                            small 
                                            color="primary" 
                                            dark>
                                            Register Trainee
                                        </v-btn>
                                        <v-btn
                                            href="{{ url('api/download-consent-form') }}"
                                            color="primary" 
                                            small 
                                            dark>
                                            Print Consent Form
                                        </v-btn>
                                    </v-flex>
                                    <v-flex xs12 sm6 md6 lg4>
                                        <v-text-field
                                            class="mr-4 ml-4"
                                            v-model="search"
                                            append-icon="search"
                                            label="Search"
                                            hide-details>
                                        </v-text-field>
                                    </v-flex>
                                </v-layout>
                                
                                <v-data-table class="table table-bordered"
                                    :headers="headers"
                                    :items="trainees"
                                    :search="search"
                                    :disable-initial-sort="true"
                                    :rows-per-page-items="rows_per_page_items"
                                    :loading="loading"
                                    class="elevation-1">
        
                                    <template slot="headerCell" slot-scope="props">
                                        <v-tooltip bottom>
                                            <span slot="activator">
                                                @{{ props.header.text }}
                                            </span>
                                            <span>
                                                @{{ props.header.text }}
                                            </span>
                                        </v-tooltip>
                                    </template>
                                    
                                    <template slot="items" slot-scope="props">
                                        <tr>
                                            <td>@{{ props.item.trainee_id }}</td>
                                            <td>
                                                @{{ props.item.lname }}, @{{ props.item.fname }} @{{ props.item.mname }}
                                            </td>
                                            <td>
                                                @{{ props.item.email }}
                                            </td>
                                            <td>@{{ props.item.trainor }}</td>
                                            <td>@{{ props.item.created_at | dateTimeFormat }}</td>
                                            <td>
                                                <v-menu
                                                transition="slide-y-transition"
                                                min-width="200"
                                                bottom
                                                >
                                                    <v-btn
                                                        slot="activator"
                                                        color="primary"
                                                        dark
                                                        small
                                                    >
                                                        ACTION
                                                    </v-btn>
                                                    <v-list>
                                                        <v-list-tile v-on:click="openUpdateDialog(props.item.trainee_id)">
                                                            <v-list-tile-title>
                                                                <v-icon small color="primary">ion ion-edit</v-icon>&nbsp;
                                                                Edit
                                                            </v-list-tile-title>
                                                        </v-list-tile>
                                                        <v-list-tile v-on:click="willDeleteTrainee(props.item.trainee_id, props.index)">
                                                            <v-list-tile-title>
                                                                <v-icon small color="primary">ion ion-trash-a</v-icon>&nbsp;
                                                                Delete
                                                            </v-list-tile-title>
                                                        </v-list-tile>
                                                        <v-list-tile v-on:click="resetPassword(props.item.trainee_id)">
                                                            <v-list-tile-title>
                                                                <v-icon small color="primary">ion ion-refresh</v-icon>&nbsp;
                                                                Reset Password
                                                            </v-list-tile-title>
                                                        </v-list-tile>
                                                    </v-list>
                                                </v-menu>
                                            </td>
                                        </tr>
                                    </template>
                                </v-data-table>
                            </v-card>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card>
        </v-flex>

        <!-- Form Dialog -->
        <v-dialog v-model="dialog" persistent max-width="500px">
            <v-card>
                <v-form 
                    ref="form" 
                    v-model="valid"
                >
                    <v-card-title>
                        <span class="headline">@{{ trainee.trainee_id ? 'Update Trainee' : 'Register Trainee' }}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container grid-list-xs>
                            <v-layout wrap>
                                <v-flex xs12 sm6 md12>
                                    <label class="grey--text">Firstname</label>
                                    <v-text-field 
                                    v-model="trainee.fname"
                                    solo
                                    :error-messages="errors.fname"
                                    autofocus
                                    required
                                    :rules="[rules.required]"
                                    ></v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md6>
                                    <label class="grey--text">Middle Name</label>
                                    <v-text-field 
                                    v-model="trainee.mname"
                                    solo
                                    hint="This field is optional"
                                    :error-messages="errors.mname"
                                    ></v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md6>
                                    <label class="grey--text">Lastname</label>
                                    <v-text-field 
                                    v-model="trainee.lname"
                                    solo
                                    :error-messages="errors.lname"
                                    required
                                    :rules="[rules.required]"
                                    ></v-text-field>
                                </v-flex>
                                <v-flex xs12>
                                    <label class="grey--text">Email</label>
                                    <v-text-field 
                                    v-model="trainee.email"
                                    solo
                                    :error-messages="errors.email"
                                    required
                                    :rules="[rules.required, rules.email]"
                                    ></v-text-field>
                                </v-flex>
                                <v-flex xs12 v-if="!trainee.trainee_id">
                                    {{-- <v-alert
                                        :value="true"
                                        color="primary"
                                        outline
                                        >
                                        <strong class="text-center">Consent Disclosure Statement for Isuzu E-Learning System</strong> 
                                        <br><br>
                                        <p>
                                            I hereby give my consent to Isuzu Philippines Corporation (IPC), to the collection, transmission, distribution, retention, and destruction of my personal information in full compliance with the Data Privacy Act of 2012 of the Republic of the Philippines.

                                            Please check on the boxes below if you agree or disagree in giving your consent to the collection and processing of your personal information for Isuzu E-Learning System as stated above.
                                        </p>
                                    </v-alert> --}}
                                    <v-alert
                                        :value="true"
                                        icon="mdi-shield-lock-outline"
                                        prominent
                                        text
                                        type="info"
                                    >
                                        <h4 class="text-md-center">
                                            Consent Disclosure Statement for <br/> Isuzu E-Learning System
                                        </h4>
                                        <br>
                                        <p class="start">I hereby give my consent to Isuzu Philippines Corporation (IPC), to the collection, transmission, distribution, retention, and destruction of my personal information in full compliance with the Data Privacy Act of 2012 of the Republic of the Philippines.</p>

                                        <p>Please check on the boxes below if you agree or disagree in giving your consent to the collection and processing of your personal information for Isuzu E-Learning System as stated above.</p>

                                        <v-layout>
                                            <v-flex class="text-md-right" v-if="valid">
                                                <v-btn color="success" v-on:click="consentPrivacyWatcher(true)">
                                                    <v-icon small>check</v-icon>&nbsp;
                                                    Agree
                                                </v-btn>
                                            </v-flex>
                                            <v-flex class="text-md-left">
                                                <v-btn color="danger" v-on:click="consentPrivacyWatcher(false)">
                                                    <v-icon small>close</v-icon>
                                                    Disagree
                                                </v-btn>
                                            </v-flex>
                                        </v-layout>
                                    </v-alert>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat @click.native="dialog = false">Close</v-btn>
                        <v-btn color="success darken-1 white--text" v-on:click="saveChanges" :disabled="!consentPrivacyStatus && !trainee.trainee_id">Save</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>

        <!-- Delete Dialog -->
        <v-dialog v-model="delete_dialog" max-width="290">
            <v-card>
                <v-card-title class="headline font-weight-thin">Delete this trainee?</v-card-title>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="green darken-1" flat v-on:click="delete_dialog = false">No</v-btn>
                <v-btn color="red darken-1" flat v-on:click="deleteTrainee">Yes</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- End Dialog -->
    </v-layout>
</v-container>
@endsection

@push('scripts')
<script>
    const TRAINOR_ID = "{{ str_replace_last('trainor_', '', Auth::user()->app_user_id) }}";
    const base_url = "{{ url('/') }}";

    new Vue({
        el: '#app',
        data() {
            return {
                valid: false,
                consentPrivacyStatus: false,
                updateForm: false,
                delete_dialog: false,
                dialog: false,
                loading: true,
                search: '',
				headers: [
					{ text: 'ID#', value: 'trainee_id' },
					{ text: 'Trainee', value: 'trainee' },
					{ text: 'Email', value: 'email' },
					{ text: 'Trainor', value: 'trainor' },
					{ text: 'Registered at', value: 'created_at' },
					{ text: '', value: '', sortable: false }
				],
                trainees: [],
                rows_per_page_items: [10, 30, 50, {"text":"$vuetify.dataIterator.rowsPerPageAll","value":-1}],
                trainee: {},
                deleteItem: {
                    id: '',
                    index: ''
                },
                errors: {},
                rules: {
					required: value => !!value || 'This field is required.',
					counter: value => value.length <= 20 || 'Max 20 characters',
					email: value => {
						const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
						return pattern.test(value) || 'Invalid e-mail.'
					}
				}
            }
        },
        watch: {
            dialog() {
                this.$refs.form.reset();
            }
        },
        created() {
            this.fetchTrainees();
            this.trainee_tab = 'red';
        },
        methods: {
            resetPassword(id) {
                var user_type = 'trainee';
				swal({
					title: 'Reset Password?',
					text: 'An email will be sent to the end user.',
					icon: 'warning',
					dangerMode: true,
                    closeOnClickOutside: false,
					buttons: {
						cancel: {
							text: "Cancel",
							value: null,
							visible: true,
							closeModal: true,
						},
						confirm: {
							text: "Proceed",
							value: true,
							visible: true,
							closeModal: false,
						},
					}
				})
				.then((res) => {
					if (res)
						axios.put(`${this.base_url}/trainor/reset_password/${id}/${user_type}`)
						.then(({data}) => {
							if (data) 
								swal({
									title: 'Success!',
									text: 'Notification has been sent to end user.',
									icon: 'success',
									button: false,
									timer: 4000
								});
						})
						.catch((error) => {
							console.log(error.response);
                            swal({
                                title: 'Ooops!',
                                text: 'Something went wrong. Please try again.',
                                icon: 'error',
                                button: false,
                                timer: 4000
                            });
						});
				});
			},
            fetchTrainees: function() {
                axios.get(`${base_url}/trainor/trainee_list/get/${TRAINOR_ID}`)
				.then(({data}) => {
                    this.trainees = data

                    setTimeout(() => {
						this.loading = false
					}, 1000)
				})
				.catch((err) => {
                    swal({
                        title: "Ooops!",
                        text: "Something went wrong. Please try again.",
                        icon: "error",
                        button: false,
                        timer: 4000,
                    })
					console.log(err.response);
				});
            },
            openUpdateDialog: function(trainee_id) {
                this.errors = {};
                this.updateForm = true;
                this.dialog = true;

                axios.get(`${base_url}/trainor/trainee/get/${trainee_id}`)
				.then(({data}) => {
                    this.trainee = data
				})
				.catch((err) => {
                    console.log(err.response);
                    swal({
                        title: "Ooops!",
                        text: "Something went wrong. Please try again.",
                        icon: "error",
                        button: false,
                        timer: 4000,
                    })
				});
            },
            openCreateDialog: function() {
                this.errors = {};
                this.updateForm = false;
                this.dialog = true;
                this.trainee = {
                    trainor_id: TRAINOR_ID
                }
            },
            saveChanges: function() {
                if (this.updateForm) {
                    axios.put(`${base_url}/trainor/trainee/put/${this.trainee.trainee_id}`, this.trainee)
                    .then(({data}) => {
                        this.fetchTrainees();
                        this.message('Successfully saved!');
                        this.dialog = false;
                        this.trainee = {
                            trainor_id: TRAINOR_ID
                        }
                    })
                    .catch((err) => {
                        console.log(err.response);
                        this.errors = err.response.data.errors
                        swal({
                            title: "Ooops!",
                            text: "Something went wrong. Please try again.",
                            icon: "error",
                            button: false,
                            timer: 4000,
                        })
                    });
                }
                else {
                    axios.post(`${base_url}/trainor/trainee/post`, this.trainee)
                    .then(({data}) => {
                        this.fetchTrainees();
                        this.message('Successfully saved!');
                        this.consentPrivacyStatus = false
                        this.snackbar = true;
                        this.trainee = {
                            trainor_id: TRAINOR_ID
                        }
                    })
                    .catch((err) => {
                        console.log(err.response);
                        this.errors = err.response.data.errors
                        swal({
                            title: "Ooops!",
                            text: "Something went wrong. Please try again.",
                            icon: "error",
                            button: false,
                            timer: 4000,
                        })
                    });
                }
            },
            willDeleteTrainee: function(trainee_id, index) {
                this.delete_dialog = true;
                this.deleteItem = {
                    id: trainee_id,
                    index: index
                }
            },
            deleteTrainee: function() {
                axios.delete(`${base_url}/trainor/trainee/delete/${this.deleteItem.id}`)
                .then(({data}) => {
                    this.trainees.splice(this.deleteItem.index, 1);
                    this.message('Successfully saved!');
                    this.delete_dialog = false;
                })
                .catch((err) => {
                    console.log(err.response);
                    swal({
                        title: "Ooops!",
                        text: "Something went wrong. Please try again.",
                        icon: "error",
                        button: false,
                        timer: 4000,
                    })
                });
            },

            consentPrivacyWatcher (action) {
                if (action === false) {
                    this.valid = false
                    this.errors = {};
                    this.updateForm = false;
                    this.dialog = false;
                    this.consentPrivacyStatus = false
                }
                else {
                    const formStatus = this.$refs.form.validate()

                    if (formStatus) {
                        this.valid = true
                        this.consentPrivacyStatus = true
                    }
                }
            }
        }
    })
</script>
@endpush
