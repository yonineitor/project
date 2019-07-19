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
class Recovery_Password_Controller extends Public_Controller
{
	public function form()
	{
		return $this->template->render('view-recoverypassword-form',[
			'username' => $this->input->get('username')
		]);
	}
	
	public function createPassword()
	{
		$user = $this->authToken();
	}

	public function storePassword()
	{
		$user = $this->authToken();

		$this->form_validation
			->set_rules('password','Password','required|min_len[4]')
		;

		if($this->form_validation->run()===FALSE)
		{
			return Redirect::setMessage([
				'message' => $this->form_validation->error_string()
			])->send('/createPassword?'.http_build_query($this->input->get()));
		}
	}

	public function authToken()
	{
		$email 	= $this->input->get('email');
		$token 	= $this->input->get('token');

		$fechaActual = new DateTime();
		$fechaActual->add(new DateInterval('P7D'));
		$fechaActual->getTimestamp();
		$user = User::get(['email' => $email]);

		if(!$user->result())
		{
			return Redirect::setMessage([
				'message' => 'Fail token'
			])->send('/login');
		}
		if( $user->result('token_recovery') !== $token )
		{
			return Redirect::setMessage([
				'message' => 'Fail token recovery'
			])->send('/login');
		}
		if( $user->result('token_time') < $fechaActual->getTimestamp() )
		{
			return Redirect::setMessage([
				'message' => 'Token expired 7 days'
			])->send('/login');
		}

		return $user;
	}
}