<div class="modal fade" tabindex="-1" role="dialog"  id="modal-user-roles-form">
  	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">User roles</h5>
				<button title="Close" type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form autocomplete="OFF" autocapitalize="off" @submit="updateRoles" >
					<div class="form-group mb-0">
						<label class="col-form-label col-form-label-sm">Is administrator <span class='text-danger'>*</span></label> <br>
						<div class="custom-control custom-radio mr-2 custom-control-inline mb-2" >
						  	<input value="1" type="radio" id="userIsAdminYes" name="userIsAdmin" v-model="formRoles.is_admin" class="custom-control-input">
						  	<label class="custom-control-label" for="userIsAdminYes">Yes</label>
						</div>
						<div class="custom-control custom-radio  custom-control-inline mb-2" >
							<input  value="0" type="radio" id="userIsAdminNo" name="userIsAdmin" v-model="formRoles.is_admin" class="custom-control-input">
							<label class="custom-control-label" for="userIsAdminNo">No</label>
						</div>
					</div>
					<div class="form-group mb-0" v-show="formRoles.is_admin==0">
						<label class="col-form-label col-form-label-sm">Roles </label>
						<div class="row">
							<?php foreach($_['roles'] as $role ) : 
								$roleId    = $role['id'];
								$roleTitle = $role['title'];
								?>
								<div class="col-md-6">
									<div class="custom-control custom-checkbox   mb-1" >
									  	<input v-model="formRoles.roles" value="<?= $roleId?>" type="checkbox" id="userRoles<?= $roleId?>" name="userRoles" class="custom-control-input">
									  	<label class="custom-control-label" for="userRoles<?= $roleId?>"><?= $roleTitle?></label>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="text-right mt-2">
						<button type="submit" :disabled="disableSubmit" class="btn btn-primary "> Update </button>
						<button type="button" class="btn btn-secondary " data-dismiss="modal"> Close </button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>