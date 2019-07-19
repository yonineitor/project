<?php 
	$this->template->jsonData('data-user',$_['user']);
	$this->load->view('modal-profile-changepassword');
?>

<div class="d-flex align-items-center p-3 my-3 text-white-50 bg-info rounded shadow-sm">
    <div class="lh-100">
        <h6 class="mb-0 text-white lh-100"><?= _l('My profile')?></h6>
        <small><?= $_['user']['username']?></small>
    </div>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="card">
			<div class="card-body pt-0 pl-2" v-cloak >
				<form @submit="submit" autocomplete="off"  action="#" method="POST"  >
					<div class="form-group mb-0">
						<label class="col-form-label col-form-label-sm">Roles </label>
						<div>
							<?php if($_['user']['is_admin']==1):?>
								<span class="badge badge-primary">Administrator</span>
							<?php else: ?>
								<?php foreach ($_['user']['roles_array'] as $role) {
									echo '<span class="badge badge-info mr-2">'.$role.'</span>';
								}?>
							<?php endif;?>
						</div>
					</div>
					
					
					<div class="form-group mb-0">
						<label class="col-form-label col-form-label-sm"><?= _l('Email')?> <span class="text-danger">*</span></label>
						<input v-model="form.email" type="text" class="focus-selected form-control form-control-sm" />
					</div>
					<div class="form-group mb-0">
						<label class="col-form-label col-form-label-sm"><?= _l('Name')?> <span class="text-danger">*</span></label>
						<input v-model="form.name" autofill="off"  autocomplete="off" type="text" class="form-control form-control-sm focus-selected" />
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm"><?= _l('Last name')?> <span class="text-danger">*</span></label>
								<input v-model="form.last_name" type="text" class="focus-selected form-control form-control-sm" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group mb-0">
								<label class="col-form-label col-form-label-sm"><?= _l('Middle name')?></label>
								<input v-model="form.middle_name"  type="text" class="focus-selected form-control form-control-sm" />
							</div>
						</div>
					</div>
					
					<div class="text-center mt-3">
						<button type="submit" class="btn btn-primary" >Update</button>
						<button type="button" data-toggle="modal" data-target="#modal-profile-changepassword" class="btn btn-outline-success" >Change password</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="card">
			<div class="card-body">
				<?php 
                    $lastSession = [];
                    foreach( $_['sessions'] as $auth ) : 
                            if(isset($lastSession[$auth['device_id']]))
                                continue;
                            $lastSession[$auth['device_id']] = true;
                        ?>
                        <div class="mb-2 border-bottom">
                            <div class="float-right">
                                <?php if( count($auth['location']) > 0): ?>
                                    <span><?= $auth['location']['city']?>, <?= $auth['location']['region_name']?></span>
                                <?php endif;?>
                            </div>
                            <h6>Device <?= $auth['device_name']?></h6>
                            <p class="text-muted">Last connection <?= $auth['updated_at']?></p>
                        </div>
                <?php endforeach;?>
			</div>
		</div>
	</div>
</div>