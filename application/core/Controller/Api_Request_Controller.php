<?php
namespace Core\Controller;

use Lib\Response;
use Lib\Fail_Connection;
use Lib\Auth;
/**
 * Api Controller
 */
class Api_Request_Controller extends \MX_Controller
{
    public $autoload = [
        'libraries' => ['database','form_validation','user_agent'],
        'helper' => ['string']
    ];
    
    public $fail_connection; 
    
    public function __construct()
    {
        parent::__construct();
        
        $requiredDevice   = $this->input->server( Auth::getConfig('api_id_name') );
        $credentials      = $this->input->server( Auth::getConfig('api_public_name') );
        
        if(!$credentials || $credentials !== Auth::getConfig('api_public_value') )
            show_error('Credenciales invalidas');
        if(!$requiredDevice)
            show_error('Device ID not found');

        $this->fail_connection = new Fail_Connection($requiredDevice);
    }
    
    public function apiKeyAuth( $failConnection = null )
    {
        if(!$failConnection || !$failConnection instanceof Fail_Connection )
        {
            return false;
        }
        
        $failConnection->authorize('INVALID_CREDENTIALS', 50 );
        $failConnection->authorize('INVALID_HEADER_KEY_NAME', 50);
        /*
        if( $value = $this->input->server('HTTP_X_API_SEAPAL') )
        {
        	/*
            if(Env::get('api.secret_login') !== $value )
            {
                $failConnection->register('INVALID_CREDENTIALS');

                Response::setCode(404)->json([
                    'message' => 'Credenciales invalidas'
                ], TRUE );
            }
            else
            {
                return TRUE;
            }
            
        }
        else
        {
            $failConnection->register('INVALID_HEADER_KEY_NAME');
            
            Response::setCode(404)->json([
                'message' => 'Autenticación Fallida'
            ], TRUE );
        }
        */
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