<?php
use Core\Controller\Web_Controller;
use Model\Roles;
use Model\User;
use Model\Activity;
use Lib\Validation;
use Lib\Response;
use Lib\Auth;
/**
 * 
 */
class User_Post_Controller extends Web_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->middleware(['disabled','enabled','updateRoles','updateBasic','changePassword','delete'], function($params){

			$model = User::get($params[0]);
			if(!$model->result())
				show_error('User not found');

			return Array($model);
		});
	}

	public function insert()
	{

		if(!priv('priv_setting_user_create'))
			return Response::json([
				'message' => _l('Bad role permissions')
			]);
		

		$valid = new Validation();
		$valid->setRules('username','trim|strtolower|required|is_unique[user.username]|regex_match[/^[a-z0-9]+$/]');
		$valid->setRules('email','trim|required|valid_email');
		$valid->setRules('name','trim|required');
		$valid->setRules('middle_name','trim');
		$valid->setRules('last_name','trim|required');
		$valid->setRules('password','trim|required|min_length[6]');
		$valid->setRules('is_admin','trim|required|in_list[0,1]');
		$valid->setRules('date_of_birth','trim');
		

		if($valid->run() === FALSE )
		{
			return Response::json([
				'message' => $valid->error_string()
			]);
		}
		if($this->input->post('is_admin'))
		{
			$roles = '';
		}
		else
		{
			$roles = is_array($this->input->post('roles')) ? implode(',',$this->input->post('roles')) : ''; 	
		}
		
		$inputData = Array(
			'username'           => $this->input->post('username'),
			'email'               => $this->input->post('email'),
			'name'                => $this->input->post('name'),
			'middle_name'         => $this->input->post('middle_name'),
			'last_name'           => $this->input->post('last_name'),
			'password'            => password_hash( $this->input->post('password'), PASSWORD_BCRYPT ),
			'is_admin'            => $this->input->post('is_admin'),
			'date_of_birth'       => $this->input->post('date_of_birth'),
			'roles'				  => $roles
		);
		

		$activityData = $inputData;
		$activityData['is_admin'] = ($activityData['is_admin']) ? 'Yes' : 'No';
		unset($activityData['username']);
		unset($activityData['password']);
		if(!$activityData['roles'])
			unset($activityData['roles']);
		else
		{	
			$rolesAux = [];
			foreach (explode(',',$activityData['roles']) as $roleId) {
				$rolesAux[] = Roles::get($roleId)->result('title');
			}
			$activityData['roles'] = implode(', ',$rolesAux);
		}

		$user = User::insert($inputData);
		
		Activity::setData('set_values', $activityData )->insert(Array(
			'activity_group' => 'user',
			'relation_id' => $user->result('id'),
			'event' => 'actmessage_user_created'
		));

		return Response::json([
			'status' => 1,
			'message' => 'User created',
			'user_id' => $user->result('id')
		]); 
	}

	public function disabled(User $user)
	{

		if(!priv('priv_setting_user_edit'))
			return Response::json([
				'message' => _l('Bad role permissions')
			]);
		

		if( $user->result('status') == 0 )
			return Response::json(Array(
				'message' => 'Status invalid'
			));

		$user->update(Array(
			'status' => 0
		));

		Auth::refreshUser( $user->result() );

		Activity::insert(Array(
			'activity_group' => 'user',
			'relation_id' => $user->result('id'),
			'event' => 'actmessage_user_disabled'
		));

		return Response::json(Array(
			'status' => 1,
			'message' => 'User was disabled'
		));
	}

	public function enabled(User $user)
	{
		if(!priv('priv_setting_user_edit'))
			return Response::json([
				'message' => _l('Bad role permissions')
			]);

		if( $user->result('status') == 1 )
			return Response::json(Array(
				'message' => 'Status invalid'
			));

		$user->update(Array(
			'status' => 1
		));
		
		Auth::refreshUser( $user->result() );

		Activity::insert(Array(
			'activity_group' => 'user',
			'relation_id' => $user->result('id'),
			'event' => 'actmessage_user_enabled'
		));
		
		return Response::json(Array(
			'status' => 1,
			'message' => 'User was enabled'
		));
	}

	public function updateRoles(User $user )
	{
		if(!priv('priv_setting_user_edit'))
			return Response::json([
				'message' => _l('Bad role permissions')
			]);
		$valid = new Validation();
		$valid->setRules('is_admin','trim|required|in_list[0,1]');

		if($valid->run() === FALSE )
		{
			return Response::json([
				'message' => $valid->error_string()
			]);
		}

		if($this->input->post('is_admin'))
		{
			$roles = '';
		}
		else
		{
			$roles = is_array($this->input->post('roles')) ? implode(',',$this->input->post('roles')) : '';
		}

		$dataUpdate = Array(
			'is_admin' => $this->input->post('is_admin'),
			'roles'    => $roles,
		);

		if(Activity::getChanges($dataUpdate, $user->result() , FALSE ))
		{
			$activityData  = $dataUpdate;
			$activityData['is_admin'] = $activityData['is_admin'] ? 'Yes' : 'No';
			if($activityData['roles'])
			{
				$rolesAux = [];
				foreach (explode(',',$activityData['roles']) as $roleId) {
					$rolesAux[] = Roles::get($roleId)->result('title');
				}
				$activityData['roles'] = implode(', ',$rolesAux);
			}
			
			Activity::setData('set_values', $activityData )->insert(Array(
				'activity_group' => 'user',
				'relation_id' => $user->result('id'),
				'event' => 'actmessage_user_roleschanged'
			));
			
			$user->update($dataUpdate);
			Auth::refreshUser( $user->result() );
		}
		
		return Response::json([
			'status' => 1,
			'message' => 'Roles updated',
			'user' => $user->result()
		]); 
	}

	public function updateBasic( User $user )
	{
		if(!priv('priv_setting_user_edit'))
			return Response::json([
				'message' => _l('Bad role permissions')
			]);
		$valid = new Validation();

		$valid->setRules('email','trim|required|valid_email');
		$valid->setRules('name','trim|required');
		$valid->setRules('middle_name','trim');
		$valid->setRules('last_name','trim|required');
		$valid->setRules('password','trim|required|min_length[6]');
		$valid->setRules('date_of_birth','trim|required');
		$valid->setRules('gender','trim|required|in_list[Male,Female]');
		$valid->setRules('marital_status','trim|required|in_list['.implode(',',User::MARITAL_STATUS).']');
		$valid->setRules('phone','trim|required');

		if($valid->run() === FALSE )
		{
			return Response::json([
				'message' => $valid->error_string()
			]);
		}

		$dataUpdate = Array(
			'email' => $this->input->post('email'),
			'name' => $this->input->post('name'),
			'middle_name' => $this->input->post('middle_name'),
			'last_name' => $this->input->post('last_name'),
			'password' => $this->input->post('password'),
			'date_of_birth' => $this->input->post('date_of_birth'),
			'gender' => $this->input->post('gender'),
			'marital_status' => $this->input->post('marital_status'),
			'phone' => $this->input->post('phone'),
		);

		if($changesData = Activity::getChanges($dataUpdate, $user->result() ,FALSE ))
		{
			$user->update($dataUpdate);
			Auth::refreshUser( $user->result() );
			Activity::setData('set_values', $changesData)->insert(Array(
				'activity_group' => 'user',
				'relation_id' => $user->result('id'),
				'event' => 'actmessage_user_updatebasic'
			));
		}
		

		return Response::json([
			'status' => 1,
			'message' => 'User updated',
			'user' => $user->result()
		]); 
	}

	public function changePassword( User $user )
	{
		if(!priv('priv_setting_user_edit'))
			return Response::json([
				'message' => _l('Bad role permissions')
			]);

		$valid = new Validation();

		$valid->setRules('password','trim|required|min_length[6]');

		if($valid->run() === FALSE )
		{
			return Response::json([
				'message' => $valid->error_string()
			]);
		}

		$user->update(Array(
			'password' => password_hash( $this->input->post('password'), PASSWORD_BCRYPT ),
		));

		Activity::insert(Array(
			'activity_group' => 'user',
			'relation_id' => $user->result('id'),
			'event' => 'actmessage_user_changepassword'
		));

		return Response::json(Array(
			'status' => 1,
			'message' => 'Password was changed'
		));
	}

	public function delete( User $user )
	{
		if(!priv('priv_setting_user_delete'))
			return Response::json([
				'message' => _l('Bad role permissions')
			]);
		if($user->result('username') === \Lib\Auth::user('username') )
		{
			return Response::json(Array(
				'message' => 'Error to remove user'
			));
		}

		//$user->delete();
		Response::setData(['message' => "aaa". \Lib\Auth::user('username') ]);
		return Response::json(Array(
			'status' => 1,
			'message' => 'User was deleted'
		));
	}
}