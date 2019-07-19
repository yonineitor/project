<?php
use Core\Controller\Web_Controller;
use Model\User;
use Model\Roles;
use Model\Activity;
use Lib\Auth;
use Lib\Validation;
use Lib\Response;
/**
 * 
 */
class Profile_Controller extends Web_Controller
{
	public function form()
	{
		$user  = Auth::user();
		$roles = [];
		if($tmpRoles = $user['roles'])
		{
			$roles = Roles::retrieve(function($qb) use ($tmpRoles){
				$qb->where_in( 'id', explode(',',$tmpRoles) );
				return  $qb;
			})->result('title');
		}
		
		$user['roles_array'] = $roles;

		$sessions = Auth::getSessions($user['id']);

		$this->template->title(_l("My profile"));
		$this->template->addParam('menuActive','profile');
		$this->template->js('/front/js/profile/profile-form.js');
		$this->template->render('view-profile-form',[
			'user' => $user,
			'sessions' => $sessions
		]);
	}

	public function update()
	{
		$validation = new Validation();

		$validation->setRules('name','required|trim');
		$validation->setRules('last_name','required|trim');
		$validation->setRules('middle_name','trim');
		$validation->setRules('email','required|trim|valid_email');
		$validation->setRules('pin','required|trim|min_length[4]');

		if( $validation->run() === FALSE )
		{
			return Response::json([
				'message' => $validation->error_string()
			]);
		}

		$dataUpdate = Array(
			'name'        => $this->input->post('name'),
			'last_name'   => $this->input->post('last_name'),
			'middle_name' => $this->input->post('middle_name'),
			'email'       => $this->input->post('email'),
			'pin'         => $this->input->post('pin'),
		);

		$user = User::get( Auth::user('id') );
		if( $dataChanges = Activity::getChanges($dataUpdate, $user->result(), false ) )
		{
			$user->update($dataChanges);
			Auth::refreshUser( $user->result() );
			if(isset($dataChanges['pin']))
				$dataChanges['pin'] = 'Value changed ****';

			Activity::setData('set_values', $dataChanges)->insert(Array(
				'activity_group' => 'user',
				'relation_id' => $user->result('id'),
				'event' => 'actmessage_user_updateprofile'
			));
		}

		return Response::json([
			'status' => 1,
			'message' => 'User update'
		]);
	}

	public function updatePassword()
	{
		$validation = new Validation();

		$validation->setRules('current_password','required');
		$validation->setRules('new_password','required|min_length[6]');
		$validation->setRules('confirm_password','required|matches[new_password]');
		
		$user = User::get( Auth::user('id') );

		if( $validation->run() === FALSE )
		{
			return Response::json([
				'message' => $validation->error_string()
			]);
		}
		else if(!password_verify( $this->input->post('current_password') ,  $user->result('password')   ) )
		{
			return Response::json([
				'message' => 'the password does not match the user'
			]);
		}

		$user->update([
			'password' => password_hash($this->input->post('new_password'), PASSWORD_BCRYPT)
		]);

		Activity::insert(Array(
			'activity_group' => 'user',
			'relation_id' => $user->result('id'),
			'event' => 'actmessage_user_profilepassword'
		));

		return Response::json([
			'status' => 1,
			'message' => 'User update'
		]);
	}

	public function logOut()
	{
		
		Auth::destroy();

		redirect('/');
	}
}