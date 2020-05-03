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
				Edit Question
			</h3>
		</div>
		<div class="box-body">
			<div class="container">
				<form action="{{ url('/admin/submodule/'.Request::segment(3).'/questions/update/'.$question->question_id) }}" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="_method" value="PUT">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					
					<input type="hidden" name="sub_module_id" value="{{ Request::segment(3) }}">
					<input type="hidden" name="question_id" value="{{ $question->question_id }}">

					<div class="form-group">
						<label for="module">
							Question
							<span class="text-danger">**</span>
						</label>
						{{-- <input type="text" class="form-control" 
						name="question" id="question" value="{{ $question->question or old('question') }}"
						placeholder="Type Question here.." required autofocus> --}}

						<textarea 
						class="form-control" 
						name="question" 
						id="question" 
						cols="30" 
						rows="3"
						placeholder="Type Question here.." required autofocus
						>{{ $question->question or old('question') }}</textarea>

						@include('validation.server_side_error', ['field' => 'question'])
					</div>
					
					<!-- Choice container -->
					<div class="row">
						<div class="col-md-1"></div>
						<div class="col-md-8">
							<div class="form-group">
								<input type="hidden" name="choice_id_a" value="{{ $question->choices[0]['choice_id'] }}">
								<div class="input-group">
									<div class="input-group-addon">a.</div>
									<input type="hidden" name="default_a" value="a">
									<input type="text" class="form-control" 
									id="choice_a" name="choice_a" value="{{ $question->choices[0]->choice or old('choice_a') }}"
									placeholder="choice a" required>

									@include('validation.server_side_error', ['field' => 'choice_a'])
								</div>
							</div>
							<div class="form-group">
								<input type="hidden" name="choice_id_b" value="{{ $question->choices[1]['choice_id'] }}">
								<div class="input-group">
									<div class="input-group-addon">b.</div>
									<input type="hidden" name="default_b" value="b">
									<input type="text" class="form-control" 
									id="choice_b" name="choice_b" value="{{ $question->choices[1]->choice or old('choice_b') }}"
									placeholder="choice b" required>

									@include('validation.server_side_error', ['field' => 'choice_b'])
								</div>
							</div>
							<div class="form-group">
								<input type="hidden" name="choice_id_c" value="{{ $question->choices[2]['choice_id'] }}">
								<div class="input-group">
									<div class="input-group-addon">c.</div>
									<input type="hidden" name="default_c" value="c">
									<input type="text" class="form-control" 
									id="choice_c" name="choice_c" value="{{ $question->choices[2]->choice or old('choice_c') }}"
									placeholder="choice c" required>

									@include('validation.server_side_error', ['field' => 'choice_c'])
								</div>
							</div>
							<div class="form-group">
								<input type="hidden" name="choice_id_d" value="{{ $question->choices[3]['choice_id'] }}">
								<div class="input-group">
									<div class="input-group-addon">d.</div>
									<input type="hidden" name="default_d" value="d">
									<input type="text" class="form-control" 
									id="choice_d" name="choice_d" value="{{ $question->choices[3]->choice or old('choice_d') }}"
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
											<select class="form-control text-xs-center" name="correct_answer" id="correct_answer">
												<option value="a" {{ $question->choices[0]['is_correct'] ? 'selected' : '' }}>a</option>
												<option value="b" {{ $question->choices[1]['is_correct'] ? 'selected' : '' }}>b</option>
												<option value="c" {{ $question->choices[2]['is_correct'] ? 'selected' : '' }}>c</option>
												<option value="d" {{ $question->choices[3]['is_correct'] ? 'selected' : '' }}>d</option>
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