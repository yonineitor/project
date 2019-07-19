<div class="modal fade" tabindex="-1" role="dialog"  id="modal-user-basic-form">
  	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">User basic</h5>
				<button title="Close" type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form autocomplete="OFF" autocapitalize="off" @submit="updateUser($event, 'updateBasic')" >
					<div class="row">
						<div class="col-md-6">
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm">Email <span class='text-danger'>*</span></label>
								<input  v-model="form.email" type="text" class="form-control form-control-sm" />
							</div>
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm">Name <span class='text-danger'>*</span></label>
								<input v-model="form.name" type="text" class="form-control form-control-sm" />
							</div>
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm">Middle Name</label>
								<input  v-model="form.middle_name" type="text" class="form-control form-control-sm" />
							</div>
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm">Last Name <span class='text-danger'>*</span></label>
								<input v-model="form.last_name" type="text" class="form-control form-control-sm" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm">Date of birth <span class='text-danger'>*</span></label>
								<input  v-model="form.date_of_birth" type="text" class="form-control form-control-sm" />
							</div>
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm">Gender <span class='text-danger'>*</span></label> <br>
								<div class="custom-control custom-radio mr-2 custom-control-inline mb-2" >
								  	<input v-model="form.gender" value="Male" type="radio" id="userGenderMale" name="userGender" class="custom-control-input">
								  	<label class="custom-control-label" for="userGenderMale">Male</label>
								</div>
								<div class="custom-control custom-radio  custom-control-inline mb-2" >
									<input  v-model="form.gender" value="Female" type="radio" id="userGenderFemale" name="userGender" class="custom-control-input">
									<label class="custom-control-label" for="userGenderFemale">Female</label>
								</div>
							</div>
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm">Marital status <span class='text-danger'>*</span></label>
								<div>
									<select v-model="form.marital_status" class="form-control form-control-sm">
										<?php foreach($_['maritalStatus'] as $value) : ?>
											<option value="<?= $value?>"><?= $value?></option>
										<?php endforeach;?>
									</select>
								</div>
							</div>
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm">Phone <span class='text-danger'>*</span></label>
								<input v-model="form.phone"  type="text" class="form-control form-control-sm" />
							</div>
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