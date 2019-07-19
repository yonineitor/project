<?php
namespace Lib;
/**
 * 
 */
class Permission
{
	private static $vm = null;

	private $privileges = [];

	const MESSAGE_FAIL = "You don't have enough privileges to"; 

	public function __construct()
	{
		$this->privileges = include_once __DIR__ .'/Values.php';
		
	}

	public static function getInstance()
	{
		if(!self::$vm)
			self::$vm = new Permission();

		return self::$vm;
	}
	
	public static function getPrivileges( String $group = '')
	{
		$vm = self::getInstance();

		if($group)
			return $vm->privileges[$group];


		return $vm->privileges;
	}
	
	public static function getTitle( String $name )
	{
		$vm = self::getInstance();
		
		foreach ($vm->privileges as $key => $value) {
			if(isset($value[$name]))
				return self::MESSAGE_FAIL.' '.$value[$name];
		}

		return false;
	}

	public static function getAllPrivileges()
	{
		$vm   = self::getInstance();
		$priv = [];
		foreach ($vm->privileges as $key => $value) {
			$priv = array_merge( $priv, array_keys($value)); 
		}
		
		return $priv;
	}
}