<?php
namespace Core\Controller;

use Lib\Auth;
/**
 * 
 */
class Public_Controller extends \MX_Controller
{
	public $autoload = [
        'libraries' => ['database', 'Template','form_validation', 'user_agent'],
        'helper' => ['security','url']
    ];
    
    public function __construct()
	{
		parent::__construct();
    	
        $userLogged = Auth::getBrowserSession();
        
        if( $userLogged )
            redirect('/dashboard');
        
        $this->lang->load('labels');
    	$this->template->setLayout('public');
	}
}