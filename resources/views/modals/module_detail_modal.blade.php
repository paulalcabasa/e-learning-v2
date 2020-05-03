<div class="modal" id="module_detail_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal_title"></h4>
			</div>
			<div class="modal-body">
				<form id="create_module_detail" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="_method">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">

					<input type="hidden" name="module_detail_id">
					
					<div class="form-group">
						<div class="row">
							<div class="col-md-12">
								<label for="dealer_id">Select Dealer<span class="text-danger">**</span></label>
								<select 
								class="form-control"
								name="dealer_id" id="dealer_id" required></select>
							</div>
							<div class="col-md-6">
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="module_id">Module</label>
						<select 
						class="form-control"
						name="module_id" id="module_id" required></select>
					</div>

					<div class="form-group">
						<div class="row">
							<div class="col-md-6">
								<label for="start_date">Start Date</label>
								<input type="date" class="form-control"
								name="start_date" id="start_date" required>

								@include('validation.client_side_error', ['field_id' => 'start_date'])
							</div>
							<div class="col-md-6">
								<label for="end_date">End Date</label>
								<input type="date" class="form-control"
								name="end_date" id="end_date" required>

								@include('validation.client_side_error', ['field_id' => 'end_date'])
							</div>
						</div>
					</div>

					<div class="clearfix">
						<button type="submit" class="btn btn-primary pull-right">Save changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>