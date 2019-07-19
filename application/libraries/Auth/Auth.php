<?php
namespace Lib;

use Lib\Auth\JWT;
use Core\Model;
/**
 * 	Auth::start(654987465216ABC);
 *	Auth::createCredentials( $user, '' )
 */
class Auth
{
	private static $_instance = null;

	private $config = Array();

	private $device_id = -1;
	//auth_type
	private $available_types = [
		1 => 'Android',
		2 => 'Browser'
	];

	private $auth_type = -1;
	
	private $auth_model;
	
	private $sessions = null;

	public $msg_error = '';
	
	public static function id()
	{
		$instance = self::getInstance();

		if(!$instance->sessions)
		{
			return FALSE;
		}

		$dataSession = (Array)$instance->sessions;
		
		if(!isset($dataSession['auth_id']))
			return false;

		return $dataSession['auth_id'];
	}

	public static function user( $key  = null )
	{
		$instance = self::getInstance();

		if(!$instance->sessions)
		{
			return FALSE;
		}

		$dataUser = (Array)$instance->sessions['user'];
		unset($dataUser['password']);
		
		if(is_null($key))
		{
			return $dataUser;
		}
		else if( isset($dataUser[$key]))
		{
			return $dataUser[$key];
		}
		else
		{
			return '';
		}
	} 


	public static function start( $deviceID, $authType = 2 )
	{
		$instance = self::getInstance();
		
		$instance->device_id = $deviceID;
		$instance->auth_type = $authType;

		return $instance;
	}

	private static function getInstance()
	{
		if(!self::$_instance)
		{
			self::$_instance             = new Auth();
			self::$_instance->auth_model = new Model('auth');
			self::$_instance->config     = include_once __DIR__ .'/Config.php';
		}

		return self::$_instance;
	}
	
	public static function getConfig( $name = '' )
	{
		$instance = self::getInstance();
		
		if($name && isset($instance->config[$name]))
		{
			return $instance->config[$name];
		}
		else if($name)
		{
			show_error("Field {$name} not found");
		}
		
		return $instance->config;
	}
	
	/**
	 * Credentials From Cookie
	 *	@param \CallBack @callback 
	 *
	 *	@return Array | Null
	 */
	public static function getBrowserSession( $callBack = null )
	{
		$instance = self::getInstance();
		
		if(!isset($_COOKIE[$instance->config['cookie_name']])) {
            return FALSE;
        }
		$dataConnection     = $instance->getPayload( $_COOKIE[$instance->config['cookie_name']] );
		
		if(!$dataConnection)
		{
			return FALSE;
		}
		if(isset($dataConnection['password']))
			unset($dataConnection['password']);

		$instance->sessions = $dataConnection;

        if( $callBack && is_callable($callBack))
        {
        	$callBack( $dataConnection );
		}
        
        return $dataConnection;
	}

	/**
	 * Credentials From Device
	 *	@param \Intput $input
	 *  @param \CallBack $callBack
	 *
	 *	@return Array | Null
	 */
	public static function getDeviceSession( $input = null, $callBack = null )
	{
		$instance = self::getInstance();
		
		$credentials = $input->server($instance->getConfig('api_credentials_name'));

		$dataConnection = $instance->getPayload( $credentials);
		if(isset($dataConnection['password']))
			unset($dataConnection['password']);
		
		$instance->sessions = $dataConnection;
		
        if( $callBack && is_callable($callBack))
        {
        	return $callBack( $dataConnection );
        }
        else
        {
        	return $dataConnection;
        }
	}

	public function getPayload( $keyCredentials, $ip_address = '' )
	{
		$instance = self::getInstance();

		if(!$keyCredentials || !$instance->device_id)
		{	
			$instance->msg_error = 'Dispositivo invalido';
			return FALSE;
		}

		$instance->auth_model->retrieve(function( $qb ) use ($keyCredentials) {
			$qb->select('payload, auth_type, updated_at, device_id, id');
			$qb->where('credentials', "BINARY '".trim($keyCredentials)."'" , FALSE );	
			$qb->where('disabled', 0 );
			$qb->order_by('updated_at DESC');
			$qb->limit(1);

			return $qb;
		},'ROW');

		if(!$instance->auth_model->result())
		{
			$instance->msg_error = 'Las credenciales enviadas no se encontrarón o estan desabilitadas' ;
			return FALSE;
		}
		//valid device id if is android
		if( 
			$instance->auth_model->result('auth_type') == 1 && 
			$instance->auth_model->result('device_id') != $instance->device_id )
		{
			$this->msg_error = 'Dispositivo invalido';
			return FALSE;
		}

		//Acceso concedido
		$dataUpdate = Array('updated_at' => date('Y-m-d H:i:s')); 
		if($ip_address)
		{
			$dataUpdate['ip'] = $ip_address;
		}

		$instance->auth_model->update( $dataUpdate );
		$payLoad = $instance->auth_model->result('payload');
		
		$data = (Array)JWT::decode( $payLoad, $keyCredentials );
		if(!isset($data['user']) )
		{
			$this->msg_error = 'Payload incompleto, acceda de nuevo';
			$instance->destroy( $instance->auth_model->result('id') );
			return FALSE;
		}
		
		return Array(
			'user' => (Array)$data['user'],
			'auth_id' => $this->auth_model->result('id')
		);
	}

	public static function refreshUser( $data = null )
	{
		if(!$data || !isset($data['id']) ){
			//not user selected
			return false;
		}

		unset($data['password']);
		$instance = self::getInstance();
		
		$instance->auth_model->getAll([
			'user_id' => $data['id'],
			'disabled' => 0 
		],'id, credentials, payload');

		$authSessions = $instance->auth_model->result();
		if($authSessions)
		{

			foreach ($authSessions as $auth ) 
			{
				$oldPayload = (Array)JWT::decode( $auth['payload'], $auth['credentials'] );
				$userData   = array_merge( $oldPayload, Array('user' => $data) );
				$newPayload = JWT::encode($userData, $auth['credentials'] );
				
				$instance->auth_model->update(Array(
					'payload' => $newPayload,
					'updated_at' => date('Y-m-d H:i:s')
				), $auth['id'] );
			}
		}

		return Array(
			'authSessions' => count($authSessions)
		);
	}

	public static function destroyByUser( $userID )
	{
		$instance = self::getInstance();
		
		$instance->auth_model->getAll([
			'user_id' => $userID
		], 'id,auth_type');

		$authenticates = $instance->auth_model->result();
		if(!$authenticates)
		{
			return FALSE;
		}
		
		//Update all records
		$instance->auth_model->update([
			'disabled' => 1,
			'updated_at' => date('Y-m-d H:i:s')
		]);
	}

	public static function destroy( $auth_id = null )
	{
		$instance = self::getInstance();
		
		$auth_id = ($auth_id) ? intval($auth_id) : $instance->id();

		$instance->auth_model->get( $auth_id, 'id,auth_type');
		
		if(!$instance->auth_model->result())
		{
			return FALSE;
		}

		$instance->auth_model->update([
			'disabled' => 1,
			'updated_at' => date('Y-m-d H:i:s')
		]);

		$authID   = $instance->auth_model->result('id');
		$authType = $instance->auth_model->result('auth_type');
		
		//if is browser
		if( $instance->id()==$authID && $authType == 2)
		{
			setcookie($instance->config['cookie_name'], 
				'', 
				time(), 
				"/" 
			);
		}

		return TRUE;
	}

	public static function createCredentials( $userData, $data = null, $agent = null )
	{
		$instance = self::getInstance();
		
		if(!isset($userData['user']['id'])){
			return FALSE;
		}
		else if(!$instance->device_id)
		{
			return FALSE;
		}

		$ipAddress = @file_get_contents("http://ipecho.net/plain");

		if(!$ipAddress && $data['ip_address'])
			$ipAddress = $data['ip_address'];
		else if(!$ipAddress)
			$ipAddress = $instance->getIpAddress();	
		
		$userData['location'] = $instance->getLocationData($ipAddress);

		//browser - version
		if(isset($data['device_version']))
			$deviceVersion = $data['device_version'];
		else
			$deviceVersion = $instance->getDeviceVersion( $agent );
		//platform - os
		if(isset($data['device_name']))
			$deviceName = $data['device_name'];
		else
			$deviceName = $instance->getDeviceName( $agent );

		if(isset($data['firebase_token']))
			$fireBaseToken = $data['firebase_token'];
		else
			$fireBaseToken = '';

		$currentDate = new \DateTime();
		$tokenCredentials = $currentDate->format('YmdHis').'_'.$instance->createToken(225);
		
		$payload = JWT::encode($userData, $tokenCredentials);
		
		$instance->auth_model->insert([
			'disabled'		 => 0,
			'auth_type'      => $instance->auth_type,
			'credentials'    => $tokenCredentials,
			'user_id'        => $userData['user']['id'],
			'device_name'    => $deviceName,
			'device_version' => $deviceVersion,
			'device_id'      => $instance->device_id,
			'ip'             => $ipAddress,
			'firebase_token' => $fireBaseToken,
			'payload'        => $payload,
			'created_at'     => $currentDate->format('Y-m-d H:i:s'),
			'updated_at'     => $currentDate->format('Y-m-d H:i:s'),
		]);

		return $tokenCredentials;
	}

	public static function storeCredentials( $credentials )
	{
		$instance = self::getInstance();

		setcookie($instance->config['cookie_name'], 
			$credentials, 
			time() + (86400 * 30), 
			"/"
		);

		return TRUE;
	}

	private function getIpAddress()
	{
	    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	    {
	      	$ip = $_SERVER['HTTP_CLIENT_IP'];
	    }
	    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	    {
	      	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    }
	    else
	    {
	      	$ip = $_SERVER['REMOTE_ADDR'];
	    }

	    return $ip;
	}

	private function getDeviceVersion( $agent = null )
	{
		if(is_null($agent))
		{
			$deviceVersion = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unidentified User Agent';
		}
		else if ($agent->is_browser())
        {   
           	$deviceVersion = $agent->browser().' '.$agent->version();
        }
        elseif ($agent->is_robot())
        {
           	$deviceVersion = $agent->robot();
        }
        elseif ($agent->is_mobile())
        {   
            $deviceVersion = $agent->mobile();
        }

        return $deviceVersion;
	}

	private function getDeviceName( $agent = null )
	{
		if( is_null($agent) && isset($_SERVER['HTTP_USER_AGENT']) )
		{	
			$os_platform = 'Unknow Platform';

			$os_array =   array(
                '/windows nt 6.2/i'     =>  'Windows 8',
                '/windows nt 6.1/i'     =>  'Windows 7',
                '/windows nt 6.0/i'     =>  'Windows Vista',
                '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                '/windows nt 5.1/i'     =>  'Windows XP',
                '/windows xp/i'         =>  'Windows XP',
                '/windows nt 5.0/i'     =>  'Windows 2000',
                '/windows me/i'         =>  'Windows ME',
                '/win98/i'              =>  'Windows 98',
                '/win95/i'              =>  'Windows 95',
                '/win16/i'              =>  'Windows 3.11',
                '/macintosh|mac os x/i' =>  'Mac OS X',
                '/mac_powerpc/i'        =>  'Mac OS 9',
                '/linux/i'              =>  'Linux',
                '/ubuntu/i'             =>  'Ubuntu',
                '/iphone/i'             =>  'iPhone',
                '/ipod/i'               =>  'iPod',
                '/ipad/i'               =>  'iPad',
                '/android/i'            =>  'Android',
                '/blackberry/i'         =>  'BlackBerry',
                '/webos/i'              =>  'Mobile'
            );
			
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			foreach ($os_array as $regex => $value) { 
				if (preg_match($regex, $user_agent)) {
					$os_platform = $value;
					break;
				}
		    }

		    return $os_platform;
		}
		else
		{
			return $agent->platform();
		}
	}

	private function createToken( $length  = 120 )
    {
    	$pool = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ*-%#";

		return substr(
			str_shuffle(
				str_repeat(
					$pool, ceil($length / strlen($pool))
				)
			), 0, $length );
	}

	private function getLocationData( String $ip )
	{
		$access_key = env('ipstack.access_key','1e618c626bda52534b098579a4929d30');
		
		// Initialize CURL:
		$ch = curl_init('http://api.ipstack.com/'.$ip.'?access_key='.$access_key.'');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 5000);
		//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
		//curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
		
		$json = curl_exec($ch);
		$curl_errno = curl_errno($ch);
		$curl_error = curl_error($ch);

		curl_close($ch);

		if($curl_errno)
		{
			return [];
		}
		else
			return @(Array)json_decode($json, true);
	}

	public static function decodeData( String $payload, String $credentials )
	{
		return (Array)JWT::decode( $payload , $credentials );
	}

	public static function getSessions($user_id)
	{
		$auth  = new Model('auth');
		
		$userID = $user_id;

		$sessions = $auth->retrieve(function( $qb ) use ($userID){

			$qb->where(Array(
				'disabled' => 0,
				'user_id' => $userID
			));
			$qb->order_by('updated_at DESC');
		})->result();
		
		foreach ($sessions as &$item) {
			
			$payload = self::decodeData($item['payload'],$item['credentials']);
			
			if(!isset($payload['location']))
			{
                $item['location'] = Array('region_name' => '','city' =>'');
			}
			else
			{
				$item['location'] = (Array)$payload['location'];
			}
		}
		
		return $sessions;
	}
}