<?php
namespace Core\Controller;

use Lib\Response;
use Lib\Fail_Connection;
use Lib\Auth;

/**a
 * Api Controller
 */
class Api_Controller extends \MX_Controller
{
    public $autoload = [
        'libraries' => ['database','form_validation', 'user_agent'],
        'helper' => ['security','url']
    ];
    
    public $current_user = null;

    /**
     * summary
     */
    public function __construct()
    {
        parent::__construct();
        
        //set to load
        $id = $this->input->server(Auth::getConfig('api_id_name'));
        $credentials = $this->input->server(Auth::getConfig('api_credentials_name'));
        
        $auth = Auth::start($id, 2 );
        
        if( !$auth->getDeviceSession( $this->input ) )
        {
            Response::json([
                'message' => 'Acceso invalido',
                'ERROR_DEMO' => $auth->msg_error
            ], TRUE );
        }
    }
    
    public function middleware( $methods , $callback = null )
    {
        global $params;
        
        $methods = is_array($methods) ? $methods : Array($methods);
        
        $method = $this->router->fetch_method();
        if(in_array($method, $methods ) && is_callable($callback) )
        {
            $params = $callback( $params );
        }
    }
}
