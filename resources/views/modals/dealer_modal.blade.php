<div class="modal" id="dealer_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal_title"></h4>
			</div>
			<div class="modal-body">
				<form id="create_dealer" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="_method">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="dealer_id">

					<div class="form-group">
						<label for="module">
							Dealer Name
						<span class="text-danger">**</span>
						</label>
						<input type="text" class="form-control" 
						v-model="dealer.dealer_name"
						name="dealer_name" id="dealer_name" 
						required autofocus>
					</div>

					<div class="form-group">
						<label for="module">Branch</label>
						<input type="text" class="form-control" 
						v-model="dealer.branch"
						name="branch" id="branch" 
						required autofocus>
					</div>

					<div class="clearfix">
						<button type="submit" class="btn btn-primary pull-right">Save changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>