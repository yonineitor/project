<?php
namespace Lib;

/**
 * Return data to user 
 */
class Response
{
	private static $_instance;

	private $dataDefault = [
		'status' => 0
	];
	
	private $codeResponse = 0;
	/**
	* @option: JSON_HEX_QUOT
	* @option: JSON_HEX_TAG
	* @option: JSON_HEX_AMP
	* @option: JSON_HEX_APOS
	* @option: JSON_NUMERIC_CHECK
	* @option: JSON_PRETTY_PRINT
	* @option: JSON_UNESCAPED_SLASHES
	* @option: JSON_FORCE_OBJECT
	* @option: JSON_PRESERVE_ZERO_FRACTION 
	* @option: JSON_UNESCAPED_UNICODE 
	* @option: JSON_PARTIAL_OUTPUT_ON_ERROR
	 */
	private $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

	public static function getInstance()
	{
		if(!self::$_instance)
		{
			self::$_instance = new \Lib\Response();
		}

		return self::$_instance;
	}

	public static function setDataDefault( $data )
	{
		$instance = self::getInstance();

		$instance->dataDefault = $data;
		
		return $instance;
	}

	/**
	 * @param {Numeric} $code
	 * 
	 * @return \Response
	 */
	public static function setCode( $code = 200 )
	{
		$instance = self::getInstance();
		
		$instance->codeResponse = $code;

		return $instance;
	}

	/**
	 * @param {JSON_OPTIONS} $options
	 * 
	 * @return \Response
	 */
	public static function setOptions( $options )
	{
		$instance = self::getInstance();

		$instance->options = $options;

		return $instance;
	}

	/**
	 * @param {MIXED} $data
	 * @param {BOOL} $exit
	 * 
	 * @return PlainText
	 */
	public static function json( $data = null, $exit = false )
	{
		$instance = self::getInstance();

		$dataDefault = is_array($instance->dataDefault) ? $instance->dataDefault : [];

		if(is_array($data) )
		{
			$dataResponse = array_merge($dataDefault, $data );
		}
		else if(is_string($data)  )
		{
			$dataResponse = array_merge($dataDefault, [$data => ''] );
		}
		else if(is_object($data) )
		{
			$dataResponse = array_merge($dataDefault, (Array)$data );
		}
		else
		{
			$dataResponse = $instance->dataDefault;
		}

		if(isset($_GET['return_post']))
		{
			$dataResponse['post'] = $_POST;
		}
		if(isset($_GET['return_files']))
		{
			$dataResponse['files'] = $_FILES;
		}
			
		http_response_code($instance->codeResponse);
        
        header("Content-Type: application/json; charset=utf-8");
        
		echo json_encode( $dataResponse, $instance->options );
		
		if($exit === TRUE )
		{
			exit;
		}
	}

	/**
	 * @param {Array|String} $methods
	 */
	public static function method( $methods )
	{
		$methodData = is_array( $methods ) ? $methods : Array( strtoupper($methods) );
		$instance   = self::getInstance();
		
        if( !in_array( strtoupper( $_SERVER['REQUEST_METHOD'] ) ,  $methodData ) )
        {
            $instance->setCode(404)->json([
                'message' => 'Solicitud no encontrada',
                'method' => $_SERVER['REQUEST_METHOD']
            ], TRUE );
        }
        
        return TRUE;
	}
}
