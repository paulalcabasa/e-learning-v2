@extends('admin_template')

@push('styles')
	<style>[v-cloak] { display: none; }</style>
	<style>
		#submodule-title {
		    vertical-align: baseline;
		}
	</style>
@endpush

@section('content')
<div v-cloak>
	<section class="content-header">
		<a 
		id="back"
		class="btn btn-sm btn-default pull-right"
		style="margin-right: 5px;">
			<i class="fas fa-chevron-left"></i>
			BACK
		</a>
		<h1 class="sub-editable">
			Questions and Choices
		</h1>
	</section>

	<section class="content container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-6">
						<div class="info-box" style="background-color: #2D3C42">
							<span class="info-box-icon bg-red"><i class="far fa-folder"></i></span>
							<div class="info-box-content white--text">
								<span class="info-box-text grey--text">Module</span>
								<span class="info-box-number">{{ $module->module }}</span>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="info-box" style="background-color: #2D3C42">
							<span class="info-box-icon bg-red"><i class="far fa-list-alt"></i></span>
							<div class="info-box-content white--text">
								<span class="info-box-text grey--text">Sub-Module</span>
								<span class="info-box-number">{{ $submodule->sub_module }}</span>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		</div>
		<!-- Start of Questions -->
		<div class="row">
			<div class="col-md-12">
				<!-- Question Container -->
				@yield('questions')
			</div>
		</div>
	</section>

	<!-- Modal -->
	<div class="modal" id="qa_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<form id="create_qa" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="_method">
						@csrf
						<input type="hidden" name="sub_module_id">
	
						<div class="form-group">
							<label for="module">
								Question
								<span class="text-danger">**</span>
							</label>
							<input type="text" class="form-control" name="question" id="question" required autofocus>
						</div>
	
						<!-- Choice container -->
						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-8">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon">a.</div>
										<input type="hidden" name="default_a" value="a">
										<input type="text" class="form-control" id="choice_a" name="choice_a" placeholder="choice a" required>
									</div>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon">b.</div>
										<input type="hidden" name="default_b" value="b">
										<input type="text" class="form-control" id="choice_b" name="choice_b" placeholder="choice b" required>
									</div>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon">c.</div>
										<input type="hidden" name="default_c" value="c">
										<input type="text" class="form-control" id="choice_c" name="choice_c" placeholder="choice c" required>
									</div>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon">d.</div>
										<input type="hidden" name="default_d" value="d">
										<input type="text" class="form-control" id="choice_d" name="choice_d" placeholder="choice d" required>
									</div>
								</div>
							</div>
							<div class="col-md-3"></div>
						</div>
						
	
						<div class="clearfix">
							<button type="submit" class="btn btn-primary pull-right">Save changes</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>new Vue({el: '#app'})</script>
<script src="{{ url('public/js/crud.js') }}"></script>
<script>
	$('#sub_module_tab').addClass('active bg-red');
	const base_url = "{{ url('/') }}";
	var current_id = window.location.pathname.substring(window.location.pathname.lastIndexOf('/') + 1);
	var parent_module_id = null;
	var sm = null;
	$(function() {
		sm = {
			add_question() {
				crud.set_method('POST');
				$('#qa_modal').modal('show');
			},
			// -------
			back() {
				window.location = base_url + '/admin/submodules/' + parent_module_id;
			}
		};
	});

	$(document).on('submit', '#create_qa', function(event) {
		event.preventDefault();

		var url = base_url + '/admin/submodule/create';
		if (crud.get_method() == 'PUT') {
			url = base_url + '/admin/submodule/update' + $('input[name="sub_module_id"]').val();
		}
		
		$.ajax({
			type: 'POST',
			url: url,
			data: new FormData($(this)[0]),
			contentType: false,
			cache: false,
			processData: false,
		})
		.done(function(res) {
			sm.mod_sub(current_id);
			crud.reset_form('create_qa');
			if (crud.get_method() == 'POST') {
				toastr.success('Successfully saved!');
			}
			else {
				toastr.success(res.sub_module + ' has been updated.', 'Success!');
			}
		})
		.fail(function(err) {
			console.log(err['responseJSON']);
		});
	});
</script>
@endpush