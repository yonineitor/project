
<div class="card animated fadeInDown faster">
	<div class="card-header pb-0 pt-2">
		<div class="float-right">
    		<a href="<?= site_url('user/create')?>" > <i class="fas fa-user-plus"></i> <?= _l('Create user')?></a>
    	</div>
    	<label>Users</label>
	</div>
	<div class="card-body p-2" v-cloak >
		<div class="pb-2 text-center">
			<div class="btn-group">
				<button class="btn btn-sm btn-outline-secondary mr-2">  <?= _l('Filter')?> </button>
			</div>
		</div>
		<table class="table table-sm table-hover mt-1 " >
			<thead class="text-muted " >
				<tr>
					<th style="width: 160px;"> <?= _l('Last connection')?></th>
					<th ><?= _l('Username')?></th>
					<th ><?= _l('Full name')?></th>
					<th><?= _l('Roles')?></th>
					<th><?= _l('Status')?></th>
				</tr>
			</thead>
			<tbody  >
				<tr v-for="item in userCtrl.items">
					<td>
						<span v-show="!item.last_connection" class="text-danger">Never logged in</span>
						<span v-show="item.last_connection" class="text-success">{{ project.elapseTime(item.last_connection) }}</span>
					</td>
					<td><a :href="project.site_url('user/'+item.id)">{{ item.username }}</a></td>
					<td>{{ item.name }} {{ item.middle_name }} {{ item.last_name}}</td>
					<td> 
						<span class="badge badge-info mr-1" v-for="rol in item.roles_array">{{ rol}}</span>
						<span class="badge badge-primary" v-show="item.is_admin==1">Administrator</span>
					</td>
					<td>
						<span class="text-success" v-show="item.status==1">Enabled</span>
						<span class="text-danger" v-show="item.status==0">Disabled</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>