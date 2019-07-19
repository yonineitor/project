<div class="card">
	<div class="card-header pb-0 pt-2">
		<div class="float-right">
			<a class="" href="<?= site_url('roles')?>"> All roles </a>
		</div>
		<?php if(!$_['roleID']):?>
			<label>Create Role</label>
		<?php else: ?>
			<label>Edit Role</label>
		<?php endif;?>
	</div>
	<div class="card-body pt-0">
		<form id="rolesForm" autocomplete="OFF" action="<?= site_url('roles/submit')?>" method="POST">
			<input type="hidden" name="role_id" value="<?= $_['roleID']?>"/>
			
			<!--
			<div class="row pb-3">
				<div class="col-sm-6">
					<label>Role name <span class="text-danger"> * </span></label>
					<input type="text" value="<?= $_['roleName']?>" name="role_name" class="form-control form-control-sm focus-selected" />
				</div>
			</div>
			-->
			<div class="form-group mb-0">
				<label class="col-form-label col-form-label-sm"><?= _l('Role name')?> <span class='text-danger'>*</span></label>
				<input value="<?= $_['roleName']?>" name="role_name" type="text" class="form-control form-control-sm" />
			</div>
			<hr>
			<div class="row mb-2">
				<?php foreach($_['privileges'] as $group => $privileges ) : ?>
					<div class="col-xs-12 col-sm-6 col-md-3 mb-2">
						<div class="custom-control custom-checkbox">
						  	<input value="<?= $group?>" data-subgroup="fill-subgroup-<?= $group?>" type="checkbox" class="custom-control-input fill-group" id="checkBox-<?= $group?>">
						  	<label class="custom-control-label" for="checkBox-<?= $group?>"><b><?= $group?></b></label>
						</div>
						<div class="pl-4">
							<?php 
								$totalChecked = 0;
								foreach($privileges as $keyName => $title ):  
									$setChecked = '';
									if(in_array($keyName, $_['privilegesChecked']))
									{
										$totalChecked++;
										$setChecked =  'checked="true"';
									}
								?>
								<div class="custom-control custom-checkbox">
								  	<input name="privileges[]" <?= $setChecked?> value="<?= $keyName?>" type="checkbox" class="custom-control-input fill-subgroup-<?= $group?>" id="checkBox-<?= $keyName?>">
								  	<label class="custom-control-label" for="checkBox-<?= $keyName?>"><?= $title?> </label>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
					<?php if($totalChecked === count($privileges)) : ?>
						<script type="text/javascript">
							APP.postLoad(function(){
								$('#checkBox-<?= $group?>').attr('checked', true);
							})
						</script>
					<?php endif;?>
				<?php endforeach; ?>
			</div>
			<div class="text-center">
				<button type="submit" class="btn btn-primary">Submit</button>
			</div>
		</form>
	</div>
</div>
