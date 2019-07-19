<?php
namespace Model\Roles;

use Core\Model;
use Lib\Auth;
use Lib\Permission;
/**
 * 
 */
class Privileges extends Model
{
	
	private $currentPrivileges = null;
	
	public static function authorize( String $privName )
	{
		$vm = self::getInstance();
			
		if( Auth::user('is_admin') )
			return true;
		
		if(is_null($vm->currentPrivileges))
			$vm->currentPrivileges = $vm->userPrivileges();	
		
		return in_array( $privName, $vm->currentPrivileges );
	}

	public static function userPrivileges()
	{
		$vm      = self::getInstance();

		if(!is_null($vm->currentPrivileges))
			return $vm->currentPrivileges;

		
		$roles   = Auth::user('roles');
		
		if(!$roles)
			return $vm->currentPrivileges = [];
		
		$vm->retrieve( function( $qb ) use ($roles){
			$qb->select('DISTINCT name', false);
			$qb->where_in('role_id', explode(',', $roles));
			return $qb;
		});
		
		return $vm->currentPrivileges = $vm->result('name');
		
	}
}