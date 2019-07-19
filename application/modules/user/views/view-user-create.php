<div class="row justify-content-center">
	<div class="col-md-8">
		<div class="card">
			<div class="card-header pb-0 pt-2">
				<div class="float-right">
					<a class="" href="<?= site_url('user');?>"> <i class="fas fa-users"></i> <?= _l('Users');?></a>
				</div>
        		<label><?= _l('Register new user')?></label>
        	</div>
			<div class="card-body pt-0 ">
				<form @submit="onSubmit" autocomplete="off"  action="#" method="POST"  >
					<div class="row ">
						<div class="col-md-8">
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm"><?= _l('Username')?> <span class='text-danger'>*</span></label>
								<input v-model="form.username"  autofill="off" autocomplete="off"  type="text" class="form-control form-control-sm" />
							</div>
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm"><?= _l('Password')?> <span class='text-danger'>*</span></label>
								<input  v-model="form.password" type="text" class="form-control form-control-sm input-password" />
							</div>
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm"><?= _l('Email')?> <span class='text-danger'>*</span></label>
								<input  v-model="form.email" type="text" class="form-control form-control-sm" />
							</div>
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm"><?= _l('Name')?> <span class='text-danger'>*</span></label>
								<input v-model="form.name" type="text" class="form-control form-control-sm" />
							</div>
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm"><?= _l('Last name')?><span class='text-danger'>*</span></label>
								<input  v-model="form.last_name" type="text" class="form-control form-control-sm" />
							</div>
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm"><?= _l('Middle name')?></label>
								<input v-model="form.middle_name" type="text" class="form-control form-control-sm" />
							</div>
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm"><?= _l('Date of birth')?></label>
								<input  v-model="form.date_of_birth" type="text" class="form-control form-control-sm" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm">Is administrator <span class='text-danger'>*</span></label> <br>
								<div class="custom-control custom-radio mr-2 custom-control-inline mb-2" >
								  	<input value="1" type="radio" id="userIsAdminYes" name="userIsAdmin" v-model="form.is_admin" class="custom-control-input">
								  	<label class="custom-control-label" for="userIsAdminYes">Yes</label>
								</div>
								<div class="custom-control custom-radio  custom-control-inline mb-2" >
									<input  value="0" type="radio" id="userIsAdminNo" name="userIsAdmin" v-model="form.is_admin" class="custom-control-input">
									<label class="custom-control-label" for="userIsAdminNo">No</label>
								</div>
							</div>
							<div class="form-group mb-0" v-show="form.is_admin==0">
								<label class="col-form-label col-form-label-sm">Roles </label>
								<div>
									<?php if(!count($_['roles'])) : ?>
										<h5 class="text-danger"><?= _('No roles created!')?></h5>
										<a href="<?= site_url('roles/form');?>"><?= _('Register roles')?></a>
									<?php else: ?>
									<?php foreach($_['roles'] as $role ) : 
										$roleId    = $role['id'];
										$roleTitle = $role['title'];
										?>
										<div class="custom-control custom-checkbox   mb-1" >
										  	<input v-model="form.roles" value="<?= $roleId?>" type="checkbox" id="userRoles<?= $roleId?>" name="userRoles" class="custom-control-input">
										  	<label class="custom-control-label" for="userRoles<?= $roleId?>"><?= $roleTitle?></label>
										</div>
									<?php endforeach; endif; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="text-center mt-2">
						<button type="submit" class="btn btn-primary" :disabled="disableSubmit" ><?= _l('Register user')?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>