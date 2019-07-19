<?php
namespace Core;

class Model extends \CI_Model{
	
	public $table       = '';
	
	public $lastQuery   = '';
	
	private $_data      = null;
	
	private $_data_type = '';
	
	private $_query     = null;
	
	private static $vm  = [];

	/**
	 * @param String $tableName Nombre de la tabla
	 *
	 * Crea el CI_Model  
	 * Inicializa el nombre de la tabla
	 * Inicializa el ActiveRecord para la instancia
	 */
	public function __construct( $tableName = "" )
	{
		#enable autoload CI_Model
		parent::__construct();
		
		$this->table  = $tableName;
		$this->_query = DB();
	}

	/**
	 * @return Model\ModelName
	 *		
	 * Genera una instancia principal
	 * Inicializa el nombre de la tabla con la instancia
	 * Inicializa el ActiveRecord para la instancia principal
	 */
	public static function getInstance()
	{
		$calledClass = get_called_class();
		if( !isset(self::$vm[$calledClass]) )
		{
			self::$vm[$calledClass]        = new $calledClass();
			self::$vm[$calledClass]->table = basename( str_replace("\\","/", strtolower( $calledClass) ) );
			self::$vm[$calledClass]->_query = DB();
		}

		return self::$vm[$calledClass];
	}

	/**
	 * @param $method String
	 * @param $params Array()
	 *
	 * @return mixed
	 *		
	 * Genera una instancia principal
	 * Inicializa el nombre de la tabla con la instancia
	 * Inicializa el ActiveRecord para la instancia principal
	 */
	public static function __callStatic( $method, $params )
	{
		$vm = self::getInstance();
		return $vm->callMethod($method, $params );
	}

	/**
	 * @param String $method
	 * @param Array $params 
	 *
	 * @return mixed
	 *		
	 * Genera una instancia principal
	 * Inicializa el nombre de la tabla con la instancia
	 * Inicializa el ActiveRecord para la instancia principal
	 */
	public function __call( $method, $params )
	{
		return $this->callMethod($method, $params);
	}

	/**
	 * @param Boolean $keepQuery
	 *
	 * @return mixed
	 *		
	 * Genera una instancia principal
	 * Inicializa el nombre de la tabla con la instancia
	 * Inicializa el ActiveRecord para la instancia principal
	 */
	public function _getQueryBuilder( $keepQuery = TRUE )
	{
	
		if($keepQuery)
		{
			//$this->_query->reset_query();
			//$this->_query->cache_delete_all();
			return $this->_query;
		}
		
		return DB();
		//*register new*
		//#flushget query
		/*
		return DB();
		$this->db->table = $this->table;
		$this->db->reset_query();
		$this->db->flush_cache();
		return $this->db;
		*/
	}
	
	/**
	 * @param Array $where si existe una condición
	 *
	 * @return Model\ModelName
	 *
	 * Asigna la instancia a los resultados
	 * Asigna el tipo de datos como ROW para ser leidos
	 * select * from table {$WHERE} order by id DESC LIMIT 1
	 */
	public function _last( $where = null )
	{
		$query = $this->_getQueryBuilder();
		$query->from( $this->table);
		
		if(is_array($where))
		{
			$query->where( $where);
		}
		
		$query->limit(1);
		$query->order_by('id',"DESC");
		
		$this->_data_type = 'ROW';
		$this->setResult(  $query->get()->row_array() );
		
		return $this;
	}

	/**
	 * @param Array $where si existe una condición
	 *
	 * @return Model\ModelName
	 *
	 * obtener total de registros
	 * select count(1) from table {$WHERE};
	 */
	public function _count( $where = null )
	{

		$query = $this->_getQueryBuilder();
		$query->from( $this->table );
		
		if(is_array($where))
		{
			$query->where( $where);
		}
		

		return $query->count_all_results();
	}

	/**
	 * @param Array $filter Si existe una condición
	 * @param Array|String $columns Columnas de la tabla 
	 *
	 * @return Model\ModelName
	 *
	 * Asigna la instancia a los resultados
	 * Asigna el tipo de datos como ROW para ser leidos
	 * select {$columns} from table {$filter};
	 */
	public function _get( $filter = null, $columns = '*' )
	{
		$this->_data_type = 'ROW';

		$query = $this->_getQueryBuilder();
		
		$query->select(  $columns )
			->from( $this->table );
		
		if(is_numeric($filter))
		{
			$query->where(['id' => $filter]);
		}
		else if( is_array($filter) )
		{
			$query->where($filter);
		}
		else if( is_callable($filter) )
		{
			$query = $filter( $query );
		}

		$this->setResult(  $query->get()->row_array() );
		
		return $this;
	}

	/**
	 * @param Array $filter Si existe una condición
	 * @param Array|String $columns Columnas de la tabla 
	 *
	 * @example
	 *	User::getAll(function( $query ){
	 *			$query->where(['status' => '1']);
	 *		});
	 *	User::getAll(['status' => 1]);
	 *
	 * @return Model\ModelName
	 *
	 * Asigna la instancia a los resultados
	 * Asigna el tipo de datos como ARRAY para ser leidos
	 * select {$columns} from table {$filter};
	 */
	public function _getAll( $filter = null, $columns = '*' )
	{
		$this->_data_type = 'ARRAY';

		$query = $this->_getQueryBuilder();

		$query->select( $columns )->from( $this->table );
		
		if( is_array($filter) )
		{
			$query->where( $filter );
		}
		else if( is_callable($filter))
		{
			$query = $filter($query);
		}
		
		$this->setResult(  $query->get()->result_array() );

		$this->lastQuery = $query->last_query();

		return $this;
	}

	/**
	 * @param function $callBack Si existe una condición
	 * @param Enum(ARRAY|ROW) $resultType Tipo de resultado 
	 *
	 * @example
	 *	User::retrieve(function( $query ){
	 *			$query->select('id,status');
	 *			$query->where(['status' => '1']);
	 *		},'ARRAY');
	 *
	 * @return Model\ModelName
	 *
	 * Asigna la instancia a los resultados
	 * Asigna el tipo de datos especificado con $resultType
	 * genera una consulta avanzada que puede incluir inner joins
	 */
	public function _retrieve( $callBack = null , $resultType = 'ARRAY')
	{
		
		$this->_data_type = in_array($resultType,['ROW','ARRAY']) ? $resultType : 'ARRAY';
		
		$query = $this->_getQueryBuilder();
		$query->from( $this->table );
		if( is_callable($callBack) )
		{
			$queryCallBack = $callBack( $query );
			if($queryCallBack and get_class($queryCallBack) === 'CI_DB_pdo_mysql_driver')
			{
				$query = $queryCallBack;
			}
		}
		
		$result = ($resultType === 'ARRAY') ? $query->get()->result_array() : $query->get()->row_array();

		$this->lastQuery = $this->_getQueryBuilder()->last_query();

		$this->setResult(  $result );

		return $this;
	}

	/**
	 * @param String|Array $keyName Imprime la columna especificada  
	 * @param String $returnDefault En caso de no existir la  columna asigna un valor vacio
	 *
	 * @example
	 *	User::result('id');
	 *	User::result(['id','nombre']);
	 *	User::result('campoindefinido','valordefault0');
	 *
	 * @return Array
	 *
	 * Imprime el arreglo asignado
	 */
	public function _result( $keyName = NULL, $returnDefault = ''  )
	{
		if($keyName===NULL)
		{
			return $this->_data;
		}
		
		if( $this->_data_type === 'ROW' )
		{
			if(is_string($keyName))
			{
				return isset($this->_data[$keyName]) ? $this->_data[$keyName] : $returnDefault;
			}
			else if(is_array($keyName))
			{	
				$data = [];
				foreach ($keyName as $value) {
					$data[$value] = isset($this->_data[$value]) ? $this->_data[$value] : $returnDefault;
				}
				
				if(count($data)) return $data;
			}
		}
		else if($this->_data_type === 'ARRAY')
		{
			$returnData = [];
			
			if(is_string($keyName))
			{
				foreach ($this->_data as $key => $value) {
					$returnData[] = isset($value[$keyName]) ? $value[$keyName] : $returnDefault;
				}
			}
			else if(is_array($keyName))
			{
				foreach ($this->_data as $key => $value) {
					$data = Array();
					foreach ($keyName as $keyData) {
						$data[$keyData] = isset($value[$keyData]) ? $value[$keyData] : $returnDefault;
					}
					$returnData[] = $data;
				}
			}

			return $returnData;
		}
		return $this->_data;
	}
	
	/**
	 * @param Array $params Parametros a insertar  
	 *
	 * @example
	 *	User::result([
	 *		'nombre' => 'hello',
	 *		'last_name' => 'world'
	 *	]);
	 *
	 * @return Model\ModelName
	 *
	 * Inserta un registro
	 */
	public function _insert( $params )
	{
		$query = $this->_getQueryBuilder();
		
		if( method_exists($this, "_preInsert") )
		{
			$params = $this->_preInsert( $params );
		}
		
		if(!is_array($params) || count($params)===0 )
		{
			die("No se enviarón parametros para crear el registro");
		}

		$query->insert( $this->table , $params );
		
		$element = $this::get( $query->insert_id() );
		
		if( method_exists($this, "_postInsert") )
		{
			$this->_postInsert( $element );
		}
		
		return $element;
	}

	/**
	 * @param Array $params Parametros a insertar  
	 * @param Boolean|Int $id Id especifico no requerido  
	 *
	 * @example
	 *	User::update([
	 *		'nombre' => 'hello',
	 *		'last_name' => 'world'
	 *	], 58 );
	 *
	 * @return Model\ModelName
	 *
	 * Actualiza los registros de una consulta previa
	 * en caso de no existir un $id
	 */
	public function _update( Array $params = [] , $id = false )
	{
		$query = $this->_getQueryBuilder();
		
		if( method_exists($this, "_preUpdate") )
		{
			$params = $this->_preUpdate( $params );
		}

		if(!is_array($params) || count($params)===0 )
		{
			die("No se enviarón parametros para actualizar el registro");
		}

		$query->set($params);
		
		$this->refreshData( $params );

		$query = $this->queryWhere( $query, $id );
		
		//force query results
		if($query === false )
		{
			return 0;
		}

		$update = $query->update( $this->table );

		if( method_exists($this, "_postUpdate") )
		{
			$this->_postUpdate( $this );
		}

		return $update;
	}

	/**
	 * @param Boolean|Int $id Id especifico no requerido  
	 *
	 * @example
	 *	User::delete(10);
	 *  $user = User::getAll();
	 *  $user->delete();
	 *
	 * @return Model\ModelName
	 *
	 * Elimina los registros de una consulta previa
	 * en caso de no existir un $id
	 */
	public function _delete( $id = false )
	{
		$query  = $this->_getQueryBuilder();
		$delete = 0;

		if( $query = $this->queryWhere( $query, $id ) )
		{
			if( method_exists($this, "_preDelete") )
			{
				$this->_preDelete( $this );
			}
			
			$postDeleteData = $this->result();
			
			$delete = $query->delete( $this->table );
			
			if( method_exists($this, "_postDelete") )
			{
				$this->_postDelete($postDeleteData);
			}
		}

		return $delete;
	}
	
	private function queryWhere( $query, $id = FALSE )
	{
		$data     = $this->result();
		
		if(is_null($data) && !$id)
			return false;
		
		if($id)
		{
			return $query->where(['id' => $id ]);
		}
		else if(  $this->_data_type === 'ROW' && isset($data['id']) )
		{
			return $query->where(['id' => (int)$data['id']]);
		}
		else if(  $this->_data_type === 'ARRAY' && count($data)>0 )
		{
			foreach ($data as $value) {
				$query->or_where( ['id' => $value['id']] );
			}
			
			return $query;
		}
		
		return false;
	}

	private function refreshData( $valueData = null )
	{
		$data = $this->result();

		if(  $this->_data_type === 'ROW' )
		{
			$this->_data = array_merge( $data , $valueData );
		}
		else if(  $this->_data_type === 'ARRAY' && count($data)>0 )
		{
			foreach ($data as $key => $value) {
				$this->_data[$key] = array_merge($value, $valueData );
			}
		}
	}

	private function setResult( $result = null  )
	{
		$this->_data = $result;
	}

	private function callMethod( $method,  $params )
	{
		$m = "_$method";
		if( method_exists($this, $m ) )
		{
			return call_user_func_array( Array( $this, $m ),  $params );
		}
		else
		{
			die("El metodo invocado no existe [$method]");
		}
	}

	public function getLastQuery()
	{
		return $this->lastQuery;
	}

}