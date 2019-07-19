<?php
namespace Model;

use Core\Model;
use Lib\Pagination;
/**
 * 
 */
class User extends Model
{
	const MARITAL_STATUS = Array(
		'Married',
		'Widowed',
		'Separated',
		'Divorced',
		'Single'
	);

	public static function getPagination( $itemsPerPage, $page, $sort = array(), $filters = array()  )
	{

		$sort = [
			'name' => 'last_connection',
			'type' => 'DESC'
		];

		$config = Array(
			'table' => 'user',
			'orderAvailable' => Array(
				'id' => 'user.id',
				'last_connection' => 'user.last_connection'
	    	),
			'itemsPerPage' => (int)$itemsPerPage,
			'page' => (int)$page,
			'sort' => $sort,
			'filters' => $filters
		);

		$pagination = new Pagination( $config );
		$dataPagination = $pagination->retrieve();

		foreach ($dataPagination['result_data'] as &$item) {
			$roles = [];
			
			if($tmpRoles = $item['roles'])
			{
				$roles = Roles::retrieve(function($qb) use ($tmpRoles){
					$qb->where_in( 'id', explode(',',$tmpRoles) );
					return  $qb;
				})->result('title');
			}
		
			$item['roles_array'] = $roles;
		}

		return $dataPagination;
	}
}