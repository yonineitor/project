<?php
namespace Core\Controller;

use Lib\Auth;
use Lib\Redirect;
use Model\Activity;
use Model\User;
/**
 * 
 */
class Web_Controller extends \MX_Controller
{
	public $autoload = [
        'libraries' => ['database', 'Template','form_validation', 'user_agent','session'],
        'helper' => ['security','url']
    ];
    
    public function __construct()
	{
		parent::__construct();
        
        $this->authenticate();
        
    	$this->template->setLayout('admin');
        $this->template->addParam('userSetting', Array(
            'sidebarToggle' => 1,//\Model\Setting::getValue('sidebarToggle', 0 )
        ));

        User::update([
            'last_connection'=> date('Y-m-d H:i:s')
        ], Auth::user('id'));

        Activity::setLang( $this->lang->load( Array('activity') ) );
	}
    
    private function authenticate()
    {
        $session = Auth::getBrowserSession();
        if(!$session)
        {
            Auth::destroy();
            Redirect::send('/login');
            //redirect
        }

    }
    
    public function middleware( $methods , $callback = null )
    {
        global $params;
        
        $methods = is_array($methods) ? $methods : Array($methods);
        
        $method = $this->router->fetch_method();
        if(in_array($method, $methods ) && is_callable($callback) )
        {
            if(!isset($params[0]))
                $params[0] = 0;
            
            $params = $callback( $params );
        }
    }
}