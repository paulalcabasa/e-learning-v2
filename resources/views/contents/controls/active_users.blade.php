@extends('admin_template') 

@push('styles')
	<style>[v-cloak] { display: none; }</style>
@endpush

@section('content')
<div v-cloak>
	<section class="content-header">
		<h1 class="sub-editable">
			Active Users
		</h1>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-danger shadow-lg">
					<div class="box-header">
						<div class="row">
							<div class="col-md-12">
								<v-text-field
									class="pull-right"
									style="width: 300px;"
									v-model="search"
									append-icon="search"
									label="Search"
									solo
									hide-details>
								</v-text-field>
							</div>
						</div>
					</div>
					<div class="box-body">
						<v-data-table class="table table-bordered"
							:headers="headers"
							:items="users"
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
									<td>@{{ props.item.app_user_id }}</td>
									<td>@{{ props.item.name }}</td>
									<td>@{{ props.item.email }}</td>
									<td>@{{ props.item.user_type }}</td>
									<td>@{{ props.item.ip_address }}</td>
									<td>
										<span v-if="!props.item.is_active">
											@{{ props.item.updated_at | dateTimeFormat }}
										</span>
									</td>
									<td>
										<span 
										v-if="props.item.is_active" 
										class="label label-success py-1 px-2">
											ONLINE
										</span>
										<span 
										v-else
										class="label label-danger py-1 px-2">
											OFFLINE
										</span>
									</td>
									<td>
										{{-- <v-tooltip v-if="props.item.is_active" left>
											<v-btn
												slot="activator" 
												icon
												v-on:click="logOutUser(props.item.user_id)"
												>
												<v-icon small class="text-red">fas fa-sign-out-alt</v-icon>
											</v-btn>
											<span>Logout user to all devices</span>
										</v-tooltip> --}}
										<v-tooltip left v-if="props.item.is_active">
											<v-btn
												slot="activator" 
												v-on:click="logOutUser(props.item.user_id)"
												>
												Log out &nbsp;
												<v-icon small>fas fa-sign-out-alt</v-icon>
											</v-btn>
											<span>Logout user to all devices</span>
										</v-tooltip>
									</td>
								</tr>
							</template>
						</v-data-table>	
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@push('scripts')
<script>
	const base_url = "{{ url('/') }}";
	$('document').ready(function() {
		$('#active_user_tab').addClass('active bg-red');
		$('#controls_treeview').addClass('active');
	});

	new Vue({
		el: '#app',
		data() {
			return {
				loading: true,
				search: '',
				headers: [
					{ text: 'User ID', value: 'app+user_id' },
					{ text: 'Name', value: 'name' },
					{ text: 'Email', value: 'email' },
					{ text: 'User Type', value: 'user_type' },
					{ text: 'IP Address', value: 'ip_address' },
					{ text: 'Last Activity', value: 'updated_at' },
					{ text: 'Active', value: 'is_active' },
					{ text: '', value: '' }
				],
				users: [],
				rows_per_page_items: [10, 30, 50, {"text":"$vuetify.dataIterator.rowsPerPageAll","value":-1}]
			}
		},
		created() {
			this.fetchUsers();
		},
		mounted() {
			this.scrollTo('#active_user_tab', '.sidebar');
		},
		methods: {
			fetchUsers: function() {
				axios.get(`${base_url}/admin/controls/users/get`)
				.then(({data}) => {
					this.users = data;
					setTimeout(() => {
						this.loading = false
					});
				})
				.catch((err) => {
					console.log(err.response);
				});
			},
			logOutUser: function(user_id) {
				swal({
					title: "Logout user ?",
					text: "This will destroy all of his/her sessions on any browser.",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete)
						axios.post(`${base_url}/admin/controls/logout`, {user_id: user_id})
						.then(({data}) => {
							this.fetchUsers();
							this.message('Successfully logged out!');
						})
						.catch((err) => {
							console.log(err.response);
						});
				});
			}
		}
	});
</script>
@endpush