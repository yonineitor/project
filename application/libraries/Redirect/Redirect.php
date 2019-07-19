<?php 
namespace Lib;

use Lib\Auth\JWT;
/**
 * 
 */
class Redirect
{
	private static $_instance = null;
	
	private $config = null;

	private $ci = null;

	/**
	 * 
	 */
	public static function initialize()
	{
		if(!self::$_instance)
		{
			$ci = &get_instance();

			self::$_instance = new \Lib\Redirect();

			self::$_instance->config    = include_once __DIR__ .'/Config.php';
			//self::$_instance->session = $ci->session;
		}

		return self::$_instance;
	}
	
	public static function setMessage( $data = null )
	{
		$instance = self::initialize();
		
		$params = array_merge([
			'status' => 0, 
			'message' => ''
		], $data );

		try{
			$encodeMsg = JWT::encode($params, $instance->config['token_encrypt_name'] );
		} catch (\Exception $e) {
		  	return false;
		}
		
		setcookie(
			$instance->config['app_cookie_message_name'],
			$encodeMsg,
			time() + 1000 , //time() + 31536000,
			'/'
		);
		
		return $instance;
	}

	/**
	 *
     * @return cookie 
     */
	public static function getMessages()
	{
		$instance = self::initialize();

		if(isset($_COOKIE[$instance->config['app_cookie_message_name']]) && $messageText = $_COOKIE[$instance->config['app_cookie_message_name']] )
		{
			setcookie($instance->config['app_cookie_message_name'],'',0,'/');
			
			try {
				$decodeMsg = (Array)JWT::decode($messageText, $instance->config['token_encrypt_name'] );
			} catch (\Exception $e) {
				return '';
			}
			
			return $decodeMsg;
		}

		return FALSE;
	}

    /**
     * @param String $uri
     * @param String $paramsGet
     * 
     * @return exit
     */
    public static function send($uri = '', $paramsGet = null)
	{
		$instance = self::initialize();

		if($paramsGet and is_array($paramsGet) )
		{
			$uri.="?".http_build_query($paramsGet);
		}

		if(function_exists('redirect'))
		{
			redirect($uri);
		}
		else
		{
			$instance->_redirect($uri);	
		}
	}

	private function _redirect( $uri = '', $method = 'auto', $code = NULL )
	{
		if ( ! preg_match('#^(\w+:)?//#i', $uri))
		{
			$uri = site_url($uri);
		}

		// IIS environment likely? Use 'refresh' for better compatibility
		if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE)
		{
			$method = 'refresh';
		}
		elseif ($method !== 'refresh' && (empty($code) OR ! is_numeric($code)))
		{
			if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1')
			{
				$code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
					? 303	// reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
					: 307;
			}
			else
			{
				$code = 302;
			}
		}

		switch ($method)
		{
			case 'refresh':
				header('Refresh:0;url='.$uri);
				break;
			default:
				header('Location: '.$uri, TRUE, $code);
				break;
		}
		exit;
	}

	public static function validateMethod( $method = '' )
	{
		//$instance = self::initialize();

		if(strtoupper( $_SERVER['REQUEST_METHOD'] ) === strtoupper($method) )
		{
			return TRUE;
		}

		return FALSE;
	}
}