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