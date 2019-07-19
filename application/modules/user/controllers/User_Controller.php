<?php
use Core\Controller\Web_Controller;
use Core\Model;
use Model\Roles;
use Model\User;
use Lib\Response;
use Lib\Auth;
use Lib\Redirect;
/**
 * 
 */
class User_Controller extends Web_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->middleware('detail', function( $params ){
			$model = User::get($params[0]);
			if(!$model->result())
				show_error('User not found');

			return Array($model);
		});

		

		$this->template->addParam('menuActive','user_manager');
	}
	
	public function search()
	{
		if(!priv('priv_setting_user_view'))
			Redirect::send('/');
		
		if($this->input->get('format')==='json')
		{
			$itemsPerPage = $this->input->get('per_page');
			$page         = $this->input->get('page');
			
			$paginate = User::getPagination( 
				$itemsPerPage, 
				$page, 
				$this->input->get('sort'), 
				$this->input->get('filters')
			);
			
			return Response::json(Array(
				'status' => 1,
				'paginate' => $paginate
			));
		}
		$this->template->title(_l("Search user"));
		$this->template->js('/front/js/user/user-search.js');
		$this->template->render('view-user-search');
	}

	public function create()
	{

		if(!priv('priv_setting_user_create'))
			Redirect::send('/');
		

		$roles = Roles::getAll();
		$this->template->title(_l("Create user"));
		$this->template->js('/front/js/user/user-create.js');
		$this->template->render('view-user-create',[
			'roles' => $roles->result(),
			'maritalStatus' => User::MARITAL_STATUS
		]);
	}

	public function detail( User $user )
	{

		if(!priv('priv_setting_user_edit'))
			Redirect::send('/');
		
		$roles = Roles::getAll();
		
		$sessions = Auth::getSessions($user->result('id'));
		$this->template->title(_l("User ").$user->result('username') );
		$this->template->js('/front/js/user/user-detail.js');
		$this->template->render('view-user-detail',[
			'user' => $user->result(),
			'roles' => $roles->result(),
			'maritalStatus' => User::MARITAL_STATUS,
			'sessions' => $sessions
		]);
	}
}