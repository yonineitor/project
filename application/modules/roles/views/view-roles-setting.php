<?php $this->template->jsonData('data-roles',$_['roles']); ?>
<div class="card">
	<div class="card-header pb-0 pt-2">
		<div class="float-right">
			<a class="" href="<?= site_url('roles/form')?>"><i class="fas fa-user-tag"></i> Create role</a>
		</div>
		<label>Manage Roles</label>
	</div>
	<div class="card-body">
		<table class="table table-hover table-sm">
			<thead class="text-muted " >
				<tr>
					<th style="width:50px;"></th>
					<th > <?= _l('Role name')?></th>
					<th ><?= _l('Total users')?></th>
				</tr>
			</thead>
			<tbody v-cloak>
				<tr v-for="item in roles">
					<td> 
						<a href="#" @click="deleteRole(item,$event)"  class="text-danger" title="Delete" > <i class="far fa-trash-alt"></i> </a>
					</td>
					<td ><a class="" title="Edit" :href="helper.site_url('roles/form?role_id='+item.id)">{{ item.title }}</a></td>
					<td>{{ item.roles_total}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>