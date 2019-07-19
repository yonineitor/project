<?php 
	$this->template->jsonData('data-user', $_['user']);
	$this->template->jsonData('data-roles', $_['roles']);

	$this->load->view('modal-user-roles-form');
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card animated fadeInDown faster" >
        	<div class="card-header pb-0 pt-2">
        		<div class="float-right">
					<a class="" href="<?= site_url('user');?>"> <i class="fas fa-users"></i> <?= _l('Users');?></a>
				</div>
        		<label>User <span class="badge badge-secondary"><?= $_['user']['id']?></span></label>
        	</div>
            <div class="card-body pt-0 pl-0  pr-0 " v-cloak>
            	<div class="nav-scroller bg-white shadow-sm">
					<nav class="nav nav-underline" v-cloak>
						<a class="nav-link" href="#" @click="changePassword">Change password</a>
						<a class="nav-link" href="#" @click="modalRoles">Roles</a>
						<a class="nav-link" href="#" @click="enableUser" v-show="user.status==0">Enabled</a>
				        <a class="nav-link" href="#" @click="disableUser" v-show="user.status==1">Disabled</a>
				        <a class="nav-link text-danger b-1" href="#" @click="deleteUser" >Delete</a>
					</nav>
				</div>
                <div class="row p-4">
                	<div class="col-md-12">
                		<form autocomplete="OFF" autocapitalize="off" @submit="updateUser($event, 'updateBasic')" >
							<div class="row">
								<div class="col-md-8">
									<div class="form-group mb-0">
										<label class="col-form-label col-form-label-sm">Email <span class='text-danger'>*</span></label>
										<input  v-model="user.email" type="text" class="form-control form-control-sm" />
									</div>
									<div class="form-group mb-0">
										<label class="col-form-label col-form-label-sm">Name <span class='text-danger'>*</span></label>
										<input v-model="user.name" type="text" class="form-control form-control-sm" />
									</div>
									<div class="form-group mb-0">
										<label class="col-form-label col-form-label-sm">Middle Name</label>
										<input  v-model="user.middle_name" type="text" class="form-control form-control-sm" />
									</div>
									<div class="form-group mb-0">
										<label class="col-form-label col-form-label-sm">Last Name <span class='text-danger'>*</span></label>
										<input v-model="user.last_name" type="text" class="form-control form-control-sm" />
									</div>
									<div class="form-group mb-0">
										<label class="col-form-label col-form-label-sm">Date of birth </label>
										<input  v-model="user.date_of_birth" type="text" class="form-control form-control-sm" />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group mb-0">
										<label class="col-form-label col-form-label-sm">Roles <span class='text-danger'></span></label>
										<br>
										<button type="button" v-show="user.is_admin==1" class="btn btn-outline-primary mr-2 mb-2">Administrator</button>
										<button type="button" v-for="roleTitle in getRoles()" class="btn btn-outline-info mr-2 mb-2">{{ roleTitle }}</button>
									</div>
								</div>
							</div>
							<div class="text-center mt-4">
								<button type="submit" :disabled="disableSubmit" class="btn btn-primary "> Update </button>
							</div>
						</form>
                	</div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<br>
<?php $this->load->view('section-user-helper'); ?>