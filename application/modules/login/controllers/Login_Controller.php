<?php
use Core\Controller\Public_Controller;
use Model\Activity;
use Model\User;
use Lib\Response;
use Lib\Auth;
use Lib\Redirect;
/**
 * 
 */
class Login_Controller extends Public_Controller
{
	public function form()
	{
		return $this->template->render('view-login-form',[
			'username' => $this->input->get('username')
		]);
	}
	
	public function authenticate()
	{
		$this->form_validation
			->set_rules('username','Nick Name','trim|required')
			->set_rules('password','Password','required')
		;

		if($this->form_validation->run()===FALSE)
		{
			return Redirect::setMessage([
				'message' => $this->form_validation->error_string()
			])->send('/login');
		}

		$user = User::get(['username' => $this->input->post('username')]);

		if(!$user->result())
		{
			return Redirect::setMessage([
				'message' => 'User not found 1'
			])->send('/login');
		}
		else if(!password_verify( $this->input->post('password') ,  $user->result('password')   ) )
		{
			return Redirect::setMessage([
				'message' => 'The password entered does not match the user'
			])->send('/login', ['username' => $this->input->post('username') ]);
		}
		else if( $user->result('status') != 1 )
		{
			return Redirect::setMessage([
				'message' => 'User disabled'
			])->send('/login');	
		}

		//browser random token
		//$createGUID = uniqid();
		$guid = $this->input->post('guid');
		if(!$guid)
		{
			return Redirect::setMessage([
				'message' => 'Contact provider IT [guid undefined]'
			])->send('/login');	
		}
		
		$auth = Auth::start($guid);
		
		$userData = [
			'user' => $user->result()
		];

		$credentials = $auth->createCredentials($userData, null, $this->agent );
		if(!$credentials)
		{
			return Redirect::setMessage([
				'message' => 'Bad authentication, try again'
			])->send('/dashboard', ['username' => $this->input->post('username') ]);
		}
			
		$auth->storeCredentials( $credentials );
		
		return Redirect::setMessage([
			'status' => 1,
			'message' => 'Welcome'
		])->send('/dashboard');
	}
}