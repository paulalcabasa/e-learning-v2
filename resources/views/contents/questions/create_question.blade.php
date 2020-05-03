@extends('contents.sub-modules.sub_module')

@push('styles')
	<style>[v-cloak] { display: none; }</style>
@endpush

@section('questions')
<div v-cloak>
	<div class="box box-danger shadow-lg">
		<div class="box-header with-border">
			<h3 class="box-title">
				<i class="fas fa-pen"></i>
				Create Question
			</h3>
		</div>
		<div class="box-body">
			<div class="container">
				<form action="{{ url('/admin/submodule/' . Request::segment(3) . '/questions/store') }}" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="_method" value="POST">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					
					<input type="hidden" name="sub_module_id" value="{{ Request::segment(3) }}">
					<div class="form-group">
						<label for="module">
							Question
							<span class="text-danger">**</span>
						</label>
						{{-- <input type="text" class="form-control" 
						name="question" id="question" value="{{ old('question') }}"
						placeholder="Type Question here.." required autofocus> --}}

						<textarea 
						class="form-control" 
						name="question" 
						id="question" 
						cols="30" 
						rows="3"
						placeholder="Type Question here.." required
						>{{ old('question') }}</textarea>

						@include('validation.server_side_error', ['field' => 'question'])
					</div>
					
					<!-- Choice container -->
					<div class="row">
						<div class="col-md-1"></div>
						<div class="col-md-8">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon">a.</div>
									<input type="hidden" name="default_a" value="a">
									<input type="text" class="form-control" 
									id="choice_a" name="choice_a" value="{{ old('choice_a') }}"
									placeholder="choice a" required>

									@include('validation.server_side_error', ['field' => 'choice_a'])
								</div>
							</div>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon">b.</div>
									<input type="hidden" name="default_b" value="b">
									<input type="text" class="form-control" 
									id="choice_b" name="choice_b" value="{{ old('choice_b') }}"
									placeholder="choice b" required>

									@include('validation.server_side_error', ['field' => 'choice_b'])
								</div>
							</div>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon">c.</div>
									<input type="hidden" name="default_c" value="c">
									<input type="text" class="form-control" 
									id="choice_c" name="choice_c" value="{{ old('choice_c') }}"
									placeholder="choice c" required>

									@include('validation.server_side_error', ['field' => 'choice_c'])
								</div>
							</div>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon">d.</div>
									<input type="hidden" name="default_d" value="d">
									<input type="text" class="form-control" 
									id="choice_d" name="choice_d" value="{{ old('choice_d') }}"
									placeholder="choice d" required>

									@include('validation.server_side_error', ['field' => 'choice_d'])
								</div>
							</div>
							<hr>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="correct_answer">Correct Answer<span class="text-danger">**</span></label>
										<div class="input-group" style="width: 120px;">
											<select class="form-control text-xs-center" 
											name="correct_answer" 
											id="correct_answer">
												<option value="a">a</option>
												<option value="b">b</option>
												<option value="c">c</option>
												<option value="d">d</option>
											</select>
											<span class="input-group-addon"><i class="fa fa-chevron-down"></i></span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-3"></div>
					</div>
					
					<div class="clearfix">
						<button type="submit" class="btn btn-primary pull-right">Save changes</button>
					</div>

					@if ($errors->any())
						<div class="alert alert-danger">
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
	<script>new Vue({el: '#app'})</script>
	<script>
		$(document).ready(function() {
			document.getElementById('question').focus();
			$('#back').attr('href', "{{ url('/') }}" + '/admin/submodule/{{ Request::segment(3) }}/questions');
			$('#sub_module_tab').addClass('active bg-red');
			$('#modules_treeview').addClass('active');
			$('input[name="question"]').focus();
		});
	</script>
@endpush