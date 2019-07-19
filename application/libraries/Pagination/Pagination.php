<?php
namespace Lib;

/**
 * @example
 *
 *  $config = [
 *			'table' => 'products',
 *			'orderAvailable' => [
 *	    		'id' => 'product.id',
 * 				'name' => 'product.name'
 *	    		],
 * 			'itemsPerPage' => 15,
 *			'page' => 1,
 *			'sort' => [
 * 					'name'
 * 					'type' => 
 * 				],
 *			'filters' => ['id' => 1, 'name' => 'red']
 *		];
 *  
 *  $pagination = new Pagination($config, '*' );
 *  
 *  $response = $pagination->retrieve( function($qb, $pag){
 * 	
 * 		if( $id = $pag->getFilter('id'))
 * 			$qb->where(['product.id' => $id]);
 * 		if( $name = $pag->getFilter('name'))
 * 			$qb->like(['product.name' => $name]);
 * 	
 * 		return $qb;
 * 	});
 *
 */
class Pagination{

	private $CI;
	
	private $_config;

	private $_columns;

	private $_count_query_builder;

	private $_queryBuilder = [
		'count' => null,
		'data' => null
	];

	private $_helperSQL = [
		'count' => '',
		'data' => ''
	];


	/**
	 * @param String $tableName
	 * @param Array $config
	 * 
	 * @example
	 * 		$Pagination = new Pagination($config, ['id','name'] );
	 * 
	 */
	public function __construct( Array $config , $columns = '*' )
	{
		$this->CI       = &get_instance();

		if(!isset($config['filters']) || !is_array($config['filters']) )
		{
			$config['filters'] = isset($_GET['filters']) ? $_GET['filters'] : [];
		}
		if(!isset($config['sort'])  || !is_array($config['sort']) )
		{
			$config['sort'] = isset($_GET['sort']) ? $_GET['sort'] : [];
		}

		$this->_config  = $config;
		$this->_columns = $columns;
	}

	/**
	 * @param function $CallBack
	 * 
	 * @return Array
	 */
	public function retrieve( \Closure $CallBack = null ) 
	{
		$result = [
			'total_count' => 0,
			'result_data' => null
		];

		if($CallBack && is_callable($CallBack))
		{

			if(!$this->_count_query_builder)
			{
				$countQueryBuilder = $this->CI->db;
				$countQueryBuilder->from( $this->getConfig('table') );
			}
			else
			{
				$countQueryBuilder = $this->_count_query_builder;
			}
			
			$responseQB = $CallBack( $countQueryBuilder, $this ,'count' );
			$temporalQB = ($responseQB && get_class($responseQB) === 'CI_DB_pdo_mysql_driver') ? $responseQB : $countQueryBuilder;
			
			if($this->_count_query_builder)
			{
				$result['total_count'] = $temporalQB->get()->row_array();
			}
			else
			{
				$result['total_count'] = $temporalQB->count_all_results();
			}
		}
		else
		{
			$result['total_count'] = $this->CI->db->from( $this->getConfig('table') )->count_all_results();
		}
		
		if($CallBack && is_callable($CallBack))
		{	
			$dataQueryBuilder = $this->CI->db->select( $this->_columns );;
			$dataQueryBuilder->from( $this->getConfig('table') );
			$responseQB = $CallBack( $dataQueryBuilder, $this , 'data' );
			if( $responseQB && get_class($responseQB) === 'CI_DB_pdo_mysql_driver')
			{
				$result['result_data'] = $this->searchData( $responseQB );
			}
			else
			{

				$result['result_data'] = $this->searchData( $dataQueryBuilder );
			}
		}
		else
		{

			$dataQueryBuilder = $this->CI->db->select( $this->_columns );;
			$dataQueryBuilder->from( $this->getConfig('table') );
			$result['result_data'] = $this->searchData( $dataQueryBuilder );
		}

		if($result['total_count'])
		{
			$itemsPerPage    = $this->getConfig('itemsPerPage', 15 );
			if($itemsPerPage>0 && $result['total_count']> 0)
			{
				$pages = $result['total_count'] / $itemsPerPage;
			}
			else
			{
				$pages = 1;
			}
			$result['pages'] = ceil($pages);
		}	
		else
		{
			$result['pages'] = 0;
		}


		return $result;
	}


	public function setSelectCount(\Closure $CallBack = null)
	{
		$this->_count_query_builder = null;
		$countQueryBuilder          = $this->CI->db;
		$countQueryBuilder->from( $this->getConfig('table') );
		$responseQB                 = $CallBack( $countQueryBuilder, $this );
		$this->_count_query_builder = (get_class($responseQB) === 'CI_DB_pdo_mysql_driver') ? $responseQB : $countQueryBuilder;
		return $this;
	}
	/**
	 * @param queryBuilder $queryBuilder
	 *
	 * @return Array(Collection)
	 */
	protected function searchData( $queryBuilder  )
	{

		$orderAvailable = $this->getConfig('orderAvailable', [] ); 
		$itemsPerPage   = $this->getConfig('itemsPerPage', 15 );
		$page           = $this->getConfig('page',1);

		$limit = ($itemsPerPage==0 || $itemsPerPage>1000) ? 15 : $itemsPerPage;
		$start = ( $limit * $page ) - $limit;
		
		if( $itemsPerPage > 0  && $page >= 0)
		{
			$queryBuilder->limit(abs($limit),abs($start) );
		}
		
		//by default ID, DESC
		$sortName = $this->getConfig('sort.name','id' );
		$sortType = $this->getConfig('sort.type','DESC');
		$sortType = strtoupper($sortType);

		if( isset($orderAvailable[$sortName]) )
        {	
        	$sortType = in_array($sortType, ['ASC','DESC'] ) ? $sortType : 'DESC';
			$queryBuilder->order_by($orderAvailable[$sortName].' '.$sortType);
        }
        else
        {
        	$queryBuilder->order_by('id DESC');
        }

        if($records = $queryBuilder->get()->result_array() )
        {
        	return $records;
        }

        return [];
	}


	/**
	 * @param String $name
	 * 
	 * @return @value
	 */
	public function getFilter( $name = '')
	{
		$filters = isset($this->_config['filters']) ? $this->_config['filters'] : [];

		return $this->getConfig( $name, false, $filters );
	}

	/**
	 * @param String @name
	 * @param mixed $value
	 */
	public function setFilter( $name , $value = null)
	{
		//
		$this->_config['filters'][$name] = $value;
	}

	/**
	 * @example
	 *  $pagination = new Pagination( $config );
	 * 	$pagination->setConfig('orderAvailable',['id' => 'name']);
	 * 	$pagination->setConfig('itemsPerPage', 30 );
	 * 	$pagination->setConfig('page', 3 );
	 * 	$pagination->setConfig('sort',['name' => '', 'type' => '']);
	 * 	$pagination->setConfig('filters',['id' => 23]);
	 * 
	 * @param String $key
	 * @param mixed $values
	 *
	 * @return Pagination
	 */
	public function setConfig( String $key , $values = null )
	{
		//Available elements
			//orderAvailable
			//itemsPerPage
			//page
			//sort
			//filters
		$this->_config[$key] = $values;

		return $this;
	}

	/**
	 * Get an item from an array using "dot" notation.
	 * @example
	 *  $pagination = new Pagination(null, 'product');
	 * 	$pagination->getConfig('sort.name','id');
	 *  $pagination->getConfig('sort.name','id');
	 *  $pagination->getConfig('name','id',['sort' => ['name' => 'id'] ]);
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  Array   $config
	 * 
	 * @return mixed
	 */
	public function getConfig( $key, $default = false, $config = null  )
	{
		$nameArrays = explode(".", (String)$key);

		$keyName = $nameArrays[0];
		
		array_shift($nameArrays);

		if(is_null($config))
		{
			$config = $this->_config;
		}

		if( isset($config[$keyName]) )
		{
			if(count($nameArrays))
			{
				return $this->getConfig( implode(".", $nameArrays), $default ,$config[$keyName] );
			}
			else
			{
				return $config[$keyName];
			}
		}
		else
		{
			return $default;
		}
	}
}