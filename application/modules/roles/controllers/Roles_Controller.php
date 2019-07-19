<?php
use Core\Controller\Web_Controller;
use Lib\Permission;
use Lib\Validation;
use Lib\Redirect;
use Lib\Response;
use Model\Roles;
use Model\Roles\Privileges;
use Model\User;
/**
 * 
 */
class Roles_Controller extends Web_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->middleware(['delete'], function( $params){
			$model = Roles::get($params[0]);
			if(!$model->result())
				show_error('Roles not found');

			return Array($model);
		});
		
		if(!priv('priv_setting_roles_manager'))
			Redirect::send('/');

		$this->template->addParam('menuActive','setting_roles');
	}

	public function setting()
	{
		$roles = Roles::getAll()->result();

		$enableUsers = User::getAll(['status' => 1 , 'roles != '=> '' ])->result();
		
		$rolesTotal = [];
		foreach ($enableUsers as $user) {
			$r = explode(',',$user['roles']);
			foreach ($r as $value) {
				$rolesTotal[$value] = (isset($rolesTotal[$value])) ? $rolesTotal[$value] + 1 : 1;
			}
		}

		foreach ($roles as &$value) {
			$value['roles_total'] = isset($rolesTotal[$value['id']]) ? $rolesTotal[$value['id']] : 0; 
		}
		
		$this->template->js('/front/js/roles/roles-setting.js');
		return $this->template->render('view-roles-setting',[
			'roles' => $roles
		]);
	}

	public function form()
	{
		$roleID            = (Int)$this->input->get('role_id');
		$role              = Roles::get($roleID);
		$privilegesChecked = [];
		$roleName          = "";
		if($role->result())
		{
			$privilegesChecked = Privileges::getAll(['role_id' => $roleID])->result('name');
			$roleName 		   = $role->result('title');
		}
		
		$privileges = Permission::getPrivileges();
		
		$this->template->js('/front/js/roles/roles-form.js');
		return $this->template->render('view-roles-form',[
			'privileges' => $privileges,
			'privilegesChecked' => $privilegesChecked,
			'roleName' =>  $roleName,
			'roleID' => $roleID
		]);
	}

	public function submit()
	{
		$valid = new Validation();
		$id = (int)$this->input->post('role_id');
		$valid->setRules('role_name','required|trim|xss_clean|max_length[75]|is_diferent['.$id.',roles.title]');
		
		$privileges = is_array($this->input->post('privileges')) ? $this->input->post('privileges') : [];
		
		if($valid->run()===FALSE)
		{
			return Redirect::setMessage([
				'message' => str_replace(["<p>","</p>"],"",$valid->error_string())
			])->send('roles/form?role_id='.$this->input->post('role_id'));
		}

		$role = Roles::get( (int)$this->input->post('role_id'));
		if($role->result())
		{
			$role->update([
				'title' => $this->input->post('role_name')
			]);
			Privileges::getAll(['role_id' => $role->result('id')])->delete();
		}
		else
		{
			$role = Roles::insert(Array(
				'title' => $this->input->post('role_name')
			));
		}

		foreach ($privileges as $value) {
			Privileges::insert(Array(
				'name' => $value,
				'role_id' => $role->result('id')
			));
		}

		if( $id === 0 )
			$message = _l('Role created');
		else
			$message = _l('Role updated');
		
		return Redirect::setMessage([
			'status' => 1,
			'message' => $message
		])->send('roles/form?role_id='.$role->result('id'));
	}

	public function delete( Roles $role )
	{
		
		$role->delete();

		return Response::json([
			'status' => 1,
			'message' => 'Role was deleted'
		]);
		
	}
}