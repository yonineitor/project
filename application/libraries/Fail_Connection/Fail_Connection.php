<?php
namespace Lib;

use Lib\Response;
use Lib\Env;

/**
 * summary
 */
class Fail_Connection extends \CI_Model
{
	private $config = null;

    private $_key_names = [];
    
	public function __construct( $device = '' )
	{
		parent::__construct();
		
        $this->config = include_once __DIR__ . '/Config.php';

        if(!$device)
        {
            $ci =& get_instance();
        
            $ci->load->library('user_agent');

            if ($ci->agent->is_browser())
            {   
                $device = $ci->agent->browser().' '.$ci->agent->version();
            }
            elseif ($ci->agent->is_robot())
            {   
                $device = $ci->agent->robot();
            }
            elseif ($ci->agent->is_mobile())
            {   
                $device = $ci->agent->mobile();
            }
            else
            {   
                $device = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unidentified User Agent';
            }
        }
        
        $this->_key_names['domain'] =  $_SERVER['SERVER_NAME'];
        $this->_key_names['login']  =  $_SERVER['REMOTE_ADDR'];
        $this->_key_names['device'] =  $device;
        
		//$this->removeOldFails()
	}

    /**
     * @param $key
     * @param $value
     * 
     * @return Fail_Connection
     */
    public function addKeyName( $key, $value  )
    {
        $this->_key_names[$key] = $value;
        
        return $this;
    }

    /**
     * 
     * @return Fail_Connection
     */
    public function getKeyName()
    {
        return implode("||", $this->_key_names );
    }

	/**
	 * @param {Integer} $attempts
	 * 
	 * @return Protecction
	 */
	public function setAttempts( $attempts =  5 )
	{
		$this->config['attempts'] = $attempts;

		return $this;
	}

	/**
	 * @param {Integer} $minutes
	 * 
	 * @return Protecction
	 */
	public function setSeconds( $seconds =  60 )
	{
		$this->config['second_to_try_again'] = $seconds;

		return $this;
	}

    /**
     * @param {Fail_Connection|null} $connection
     * @param {Integer} $attempts
     * 
     * @return boolean
     */
    public function authorize( $invalidType = '', $attempts = 0 )
    {
        $connection = $this->db->from('fail_connection')->where([
            'key_name' => $this->getKeyName(),
            'invalid_type' => $invalidType
        ])->get()->row_array();
        
        if(!$connection)
        {
            return TRUE;
        }

        $attemptsValue = ( $attempts > 0 ) ? $attempts : $this->config['attempts'];

        if( $connection['attempts'] > $attemptsValue )
        {
            $attemptsDate = new \DateTime($connection['updated_at']);
            
            $currentDate  = new \DateTime("now");
            
            $attemptsDate->add(new \DateInterval('PT' . $this->config['second_to_try_again'] . 'S'));
            
            if( $currentDate <= $attemptsDate )
            {
                $message = sprintf('Ha intentado acceder varias veces acceder, por favor espere  %s segundos para intentar de nuevo', $currentDate->diff($attemptsDate)->s );
                
                Response::setCode(404)->json([
                    'status' => 0,
                    'message' => $message,
                    'interval' => [
                        'minutes' => $currentDate->diff($attemptsDate)->i,
                        'seconds' => $currentDate->diff($attemptsDate)->s
                    ]
                ], TRUE );
            }
            else
            {
                $this->removeFail( $invalidType );

                return TRUE;
            }
        }
        
        return TRUE;
    }

   	/**
     * @return Redirect | true
     */
    public function register( $invalid_type = '')
    {
    	$connection = $this->db->from('fail_connection')->where([
            'key_name' => $this->getKeyName(),
            'invalid_type' => $invalid_type
        ])->get()->row_array();
        
        if(!$connection)
        {
            $this->db->insert('fail_connection', [
                'invalid_type' => $invalid_type,
                'key_name' => $this->getKeyName(),
                'updated_at' => date('Y-m-d H:i:s'),
                'attempts' => 1
            ] );

            return true;
        }

        $connection['attempts']++;
        
        $this->db->where( ['id' => $connection['id'] ] )->update('fail_connection', [
            'attempts' => $connection['attempts'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return TRUE;
    }

    /**
     * @return Integer
     */
    public function removeOldFails()
    {
    	$currentTime = new \DateTime('now');
    	$currentTime->add(new \DateInterval('PT' . $this->config['second_to_try_again'] . 'M'));

        return $this->db->delete('fail_connection', [
			'DATE_FORMAT(%Y%m%d%H%i%s) <= ' =>  $currentTime->format('YmdHis')
        ]);
    }

    /**
     * @return Integer
     */
    public function removeFail( $invalidType = '' )
    {
        return $this->db->delete('fail_connection', [
			'key_name' => $this->getKeyName(),
            'invalid_type' => $invalidType
        ]);
    }
}