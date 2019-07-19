<div class="modal fade" tabindex="-1" role="dialog"  id="modal-profile-changepassword">
  	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Change password</h5>
				<button title="Close" type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form autocomplete="OFF" autocapitalize="off" @submit="changePassword" >
					<div class="form-group mb-1">
						<label>Current password <span class="text-danger">*</span> </label>
						<input v-model="formPassword.current_password" type="text" class="form-control form-control-sm input-password" />
					</div>
					<div class="form-group mb-1">
						<label>New password <span class="text-danger">*</span></label>
						<input v-model="formPassword.new_password" type="text" class="form-control form-control-sm input-password" />
					</div>
					<div class="form-group mb-1">
						<label>Confirm new password <span class="text-danger">*</span></label>
						<input v-model="formPassword.confirm_password" type="text" class="form-control form-control-sm input-password" />
					</div>
					<div class="row justify-content-md-center mt-2">
						<div class="col-md-3 ">
							<button type="submit" :disabled="disableSubmit" class="btn btn-primary btn-block"> Save </button>
						</div>
						<div class="col-md-3 ">
							<button type="button" class="btn btn-secondary btn-block" data-dismiss="modal"> Close </button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>