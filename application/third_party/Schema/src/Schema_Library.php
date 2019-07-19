<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

include_once __DIR__ . "/Spyc.php";

class Schema_Library
{
	protected $CI;
		
	private $_error      = '';
	
	private $_access     = FALSE;
	
	private $_success    = Array();
	
	private $_index_keys = Array();
	
	private $_dataTypes   = Array();
	
	protected $config_schema = null; 

	public $currentColumns = null; 
	
	public function __construct(){

		$this->CI =& get_instance();
		
		$this->config_schema = include_once  __DIR__ . '/../Config.php';

		$this->_dataTypes = include_once __DIR__ . '/DataTypes.php';
		
		$this->CI->load->database();
		
		$this->CI->load->dbforge();		
		
		$this->CI->db->db_debug = FALSE;
						
		$schema_table = $this->item('schema_table');

		if($schema_table == ''){
			$this->_error = 'Schema table not asigned';
		}else if ( ! $this->CI->db->table_exists($schema_table)){	
			$this->CI->dbforge->add_field(array(				
				'name'           => array('type' => 'varchar', 'constraint' => 100),
				'user'           => array('type' => 'varchar', 'constraint' => 100),
				'last_modify'    => array('type' => 'int', 'constraint' => 11),
				'date'           => array('type' => 'datetime' ),
				'serialize_data' => array('type' => 'text')
			));	
			$this->CI->dbforge->create_table( $schema_table );
		}
	}

	/**
	 * Run version
	 * 
	 * @return complete 
	 */
	public function runMigration( $name  ){
		
		$schemas = $this->getSchemas();
		if(!isset($schemas[$name])){
			$this->_error = 'File not found, check folder schemas';
			return false;
		} else{		
			return $this->_run_file( $schemas[$name], $name);
		}

	}



	/**
	 * Path schema
	 * 
	 * @return complete 
	 */		
	public function getPathSchema(){
		return $this->item('schema_path');
	}	


	/**
     * Login
     *
     * I can acces to config schema
     * 
     * @return boolean
     */
	public function login( $usu, $pass){
		
		$sessionVar = $this->item('schema_session_var') ? $this->item('schema_session_var') : 'session_schema';
		$dataUsers  = $this->item('schema_session_users');
		//die( PR( $this->config_schema) );
		if( isset( $dataUsers[$usu] ) ) {
		
			if($dataUsers[$usu] === $pass) {	
			
				$data[$sessionVar]	    = $usu;
 				$this->CI->session->set_userdata($data);
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
     * Logout
     *
     * Close session var
     * 
     * @return void
     */	
	public function logout(){
		$sessionVar   	   = $this->item('schema_session_var') ? $this->item('schema_session_var') : 'session_schema';
		
		if($sessionVar!=''){
			$data[$sessionVar] = '';
			$this->CI->session->set_userdata($data);	
		}
	}

	/**
     * Exist login
     *
     * Get user if exist login
     * 
     * @return string (user)
     */
	public function isLogged(){
		$sessionVar = $this->item('schema_session_var') ? $this->item('schema_session_var')  : 'session_schema';
		$dataUsers  = $this->item('schema_session_users');

		if(isset($this->CI->session->userdata[$sessionVar])){

			$usu = $this->CI->session->userdata[$sessionVar];
			
			if( isset( $dataUsers[$usu] ) ){
				return $usu;
			}
		}
		return FALSE;
	}

	/**
	 * 
	 */
	public function getSchemaInfo( $name )
	{	
		$schema = $this->CI->db
			->select('serialize_data')
			->where(['name' => $name])
			->get( $this->item('schema_table') )
			->row_array();

		if($schema)
		{
			return unserialize($schema['serialize_data']);
		}
		else
		{
			return [];
		}
		
	}

	/**
	 * Get schemas db
	 *
	 *  Mi list tables migrated
	 *
	 * @return string
	 */	
	public function getSchemasMigrated(){
		$this->CI->db->order_by('date','desc');
				
		return $this->CI->db
			->get( $this->item('schema_table') )
			->result();
	}	

	/**
     * get Pending schemass
     *
     * List pending schemas use in the view schema
     * 
     * @return array [version][namefile]
     */	
	public function getSchemasPending(){
		$list_schemas 	 		= $this->getSchemas();
		$list_scehmas_migrated 	= $this->getSchemasMigrated();
		$pending_schemas = array();

		foreach ($list_schemas as $name => $file) {
			if(!$this->_get_schema_exec($name)){
				$pending_schemas[$name] = $file;
			}
		}	
		return $pending_schemas;
	}

	/**
     * Find schema database
     *
     * Get all list off schema
     * 
     * @return array [version][namefile]
     */	
	public function getSchemas(){
		$schemas = array();

		// Load all *_*.yml files in the migrations path
		foreach (glob($this->item('schema_path').'migration/*.yml') as $file)
		{
			$name = basename($file, '.yml');
			$schemas[$name] = $file; 	
		}		

		ksort($schemas);
		return $schemas;
	}

	/**
     * Find schema database
     *
     * Get all list off schema
     * 
     * @return array [version][namefile]
     */	
	public function getInstallFiles(){
		$fileInstall = array();

		// Load all *_*.yml files in the migrations path
		foreach (glob($this->item('schema_path').'install/*.php') as $file)
		{
			$name = basename($file, '.php');
			$fileInstall[$name] = $file;
		}		

		ksort($fileInstall);
		return $fileInstall;
	}


	/**
     * Find schema database
     *
     * Get all list off schema
     * 
     * @return array [version][namefile]
     */	
	public function runInstallFiles(){
		
		$files       = $this->getInstallFiles();

		$returnData  = [];
		$updateCount = $insertCount = 0;

		foreach ($files as $name => $filePath ) {
			$dbSetup = @include_once( $filePath );
			
			if(!is_array($dbSetup) || count($dbSetup)===0)
			{
				$this->_error = "1.0 Error no contains var dbSetup for file {$name}.php";
				return false;
			}
			if( !$returnData[$name] = $this->_run_install( @$dbSetup, $name ) )
			{
				return false;
			}
			
			$updateCount= $updateCount+$returnData[$name]['updateCount'];
			$insertCount= $insertCount+$returnData[$name]['insertCount'];
		}

		return Array(
			'updateCount' => $updateCount,
			'insertCount' => $insertCount,
			'data' => $returnData
		);
	}

	private function _run_install( $dbSetup = Array() , $name = '')
	{
		
		if(!$dbSetup)
		{
			$this->_error = "File not coutains var [dbSetup]. <b>".$name."</b>";
			return false;
		}
		if(!is_array($dbSetup))
		{
			$this->_error = "var [dbSetup] must be an Array. <b>".$name."</b>";
			return false;
		}

		$table = key($dbSetup);

		$dataSearch        = isset($dbSetup[$table]['searchBy']) ? $dbSetup[$table]['searchBy'] : false;
		$dataPreventUpdate = isset($dbSetup[$table]['notUpdate']) ? $dbSetup[$table]['notUpdate'] : false;
		$dataInsert        = isset($dbSetup[$table]['insert']) ? $dbSetup[$table]['insert'] : false;
		
		if(!$dataInsert)
		{
			$this->_error = "var [dbSetup] must be an assoc key with name [insert]. <b>".$name."</b>";
			return false;
		}
		if(!is_array($dataInsert))
		{
			$this->_error = "var [dbSetup][".$table."][insert] must be an array. <b>".$name."</b>";
			return false;
		}
		if(!$this->CI->db->table_exists($table))
		{
			$this->_error = "Migration table [".$table."] not exist on database. <b>".$name."</b>";
			return false;
		}

		$updateCount = $insertCount = 0; 
		foreach ($dataInsert as $key => $insertRecords) {
			
			$updateRecords    = $insertRecords;
			$dataResultUpdate = null;
			
			if(count($dataSearch) > 0)
			{
				$query = $this->CI->db;
				$query->from($table);
				foreach ($dataSearch as $columnSearch ) 
				{
					if(!isset($updateRecords[$columnSearch]))
					{
						$this->_error = "Column not found for prevent [".$table.".".$columnSearch."]. <b>".$name."</b>";
						return false;
					}

					$query->where($columnSearch, $updateRecords[$columnSearch] );

					//clear record values
					unset($updateRecords[$columnSearch]);
				}

				$getQuery = $query->get();
				if($this->_error = $this->_error_db()){
					return false;
				}
				$dataResultUpdate = $getQuery->result_array();
			}

			
			//update
			if($dataResultUpdate and count($dataResultUpdate))
			{
				if($dataPreventUpdate and is_array($dataPreventUpdate))
				{
					foreach ($dataPreventUpdate as $columnNotChange) {
						unset($updateRecords[$columnNotChange]);
					}
				}

				foreach ($dataResultUpdate as $data ) {
					$this->CI->db->where('id', $data['id'] )->update($table, $updateRecords );
					$updateCount++;
				}
			}
			else
			{

				foreach ($insertRecords as $column => $value) {
					if (!$this->CI->db->field_exists($column, $table ))
					{
						$this->_error = "Migration column [".$column."] not exist on table [".$table."]. <b>".$name."</b>";
						return false;
					}
				}
				

				if( !$this->CI->db->insert($table, $insertRecords ) )
				{
					if($this->_error = $this->_error_db()){
						return false;
					}
					$this->_error = "Migration table [".$table."] not exist on database. <b>".$name."</b>";
					return false;
				}
				$insertCount++;
			}

		}

		return Array(
			'updateCount' => $updateCount,
			'insertCount' => $insertCount
		);
	}

	/**
     * Find schema database
     *
     * Get all list off schema
     * 
     * @return array [version][namefile]
     */	
	public function getSchemasLastModify(){
		$schemas = array();
		
		// Load all *_*.yml files in the migrations path
		foreach (glob($this->item('schema_path').'migration/*.yml') as $file)
		{	
			$name           = basename($file, '.yml');
			$schemas[$name] = filemtime($file); 	
		}		

		ksort($schemas);
		return $schemas;
	}


	/**
	 * Get message erros
	 *
	 *  exist message errors?
	 *
	 * @return string
	 */
	public function getError(){

		return $this->_error;
	}

	/**
	 * Get message success
	 *
	 * All message correct
	 *
	 * @return string
	 */
	public function getSuccess(){
		$html = '';
		foreach ($this->_success as $value) {
			$html.= "<p>".$value."</p>";
		}
		return $html;
	}
	
	private function _run_file( $file , $nameSchema ){
		
		$tables = Spyc::YAMLLoad($file);

		$lastDataTables    = $this->getSchemaInfo( $nameSchema );

		$list_tables_bd    = $this->CI->db->list_tables();
		
		$defaultNullValues = $this->item('default_null_values');
		
		foreach ($tables as $tableName =>  $columns ) {
			
			if(!is_array($columns))
			{	
				$this->_error = "Schema YAML DB it contains any columns from table <b>".$tableName."</b>";
				return false;
			}

			if($this->item('sort_first_migration_columns') === TRUE)
			{
				ksort($columns);	
			}
			
			$modifyTable = in_array( $tableName, $list_tables_bd );
						
			if($modifyTable){
				$list_fields  = $this->CI->db->list_fields( $tableName ); 
			}else{		
				$list_fields = [];
			}
			
			/**
			 * set null vars attrs
			 */
			$indexKeys = $modifyColumns = $addColumns = $primaryKeys = null;
			
			$this->currentColumns = $columns;
			
			foreach ($columns as $columnName => $attr) 
			{
				if( !isset($attr['type']) )
				{
					$this->_error = "Type attr is required";
					return false;
				}
				else if( !is_string($attr['type']) )
				{
					$this->_error = "Type attr is not posible parser, check column (".$columnName.")";
					return false;
				}

				$wordTypes = explode("(", $attr['type'] );

				if( !in_array( strtoupper($wordTypes[0]), $this->_dataTypes ) ) 
				{
					$this->_error = "Data Type (".$wordTypes[0].") are not valid on column (".$columnName.")";
					return false;
				}
				
				if( in_array( strtoupper($attr['type']),['BLOB', 'TEXT', 'GEOMETRY' , 'JSON'] ) )
				{
					$columns[$columnName]['default'] = null;
				}

				if( isset($attr['primary']) && $attr['primary'] === TRUE )
				{
					//if is primary, it can not be a null
					$columns[$columnName]['null'] = false;
					$primaryKeys[]                = $columnName;
					
					$this->CI->dbforge->add_key( $columnName, TRUE );
					
					if( isset($attr['auto_increment']) )
						$columns[$columnName]['auto_increment'] = boolval($attr['auto_increment']);
				}
				else if( isset($attr['index']) && $attr['index'] === TRUE )
				{
					$indexKeys[] = $columnName;
					$this->CI->dbforge->add_key( $columnName, FALSE );
				}
				else if( $defaultNullValues && !isset($attr['null']) )
				{
					$columns[$columnName]['null'] = true;
				}

				if(in_array($columnName, $list_fields ))
				{
					$modifyColumns[$columnName] = $columns[$columnName];
				}
				else
				{
					$addColumns[$columnName] = $columns[$columnName];
				}
			}

			if(!$modifyTable){
				$this->CI->dbforge->add_field( $columns );
				$this->CI->dbforge->create_table( $tableName );
				
				if($this->_error = $this->_error_db()){
					return false;	
				}
			}else{
				
				if(is_array($modifyColumns) && count($modifyColumns))
					$this->CI->dbforge->modify_column( $tableName, $modifyColumns );
				
				if(is_array($addColumns) && count($addColumns))
					$this->CI->dbforge->add_column( $tableName, $addColumns );

				if($this->_error = $this->_error_db()){
					return false;	
				}

				if(is_array($primaryKeys) && count($primaryKeys)){
					if(!$this->_add_primary_keys($tableName, $primaryKeys)){
						return false;	
					}
				}
			}
			
			if(is_array($indexKeys) && count($indexKeys)>0 && !$this->_add_index_keys( $tableName, $indexKeys ))
			{
				return false;
			}
			
			//Set New values
			$tables[$tableName] = $columns;
		}

		$this->compareMessage( $lastDataTables , $tables );
		
		$schema_log['user']           = $this->isLogged();
		$schema_log['date']           = date('Y-m-d H:i:s');
		$schema_log['name']           = $nameSchema;
		$schema_log['last_modify']    = filemtime($file);
		$schema_log['serialize_data'] = serialize($tables);
		
		if($this->_get_schema_exec($nameSchema)){	
			$this->CI->db->update($this->item('schema_table'), $schema_log, array('name' => $nameSchema) );
		}else{
			$this->CI->db->insert($this->item('schema_table'),$schema_log);
		}	

		return $schema_log;
	}
	
	private function _get_schema_exec( $name ){
		return $this->CI->db
					->get_where( $this->item('schema_table') , 
							[ 'name' => trim($name) ] )->row();
	}	

	private function _error_db(){
		$msg_error = $this->CI->db->error();
		if($msg_error['message']!=''){
			return $msg_error['message'];
		}	
		return false;
	}

	private function _add_primary_keys( $table, $keys){
		$sql 	= " ALTER TABLE ".$table." DROP PRIMARY KEY, ADD PRIMARY KEY(".implode(",",$keys).") ";
		$result = $this->CI->db->query($sql);	
		if($this->_error = $this->_error_db()){
			return false;
		}	
		return true;
	}

	private function _add_index_keys($table, $keys )
	{			
		$current_index_keys = $this->_get_index_keys( $table );
			
		$sqls = [];
		foreach ($keys as $value) {	

			$name = $this->CI->db->escape_identifiers('i_'.$table.'_'.$value);
				
			if(in_array(str_replace("`","", $name), $current_index_keys ))
			{		
				continue;
			}
			$sql  = 'CREATE INDEX '.$name
				.' ON '.$this->CI->db->escape_identifiers($table)
				.' ('.$value.');';

			$result = $this->CI->db->query($sql);			
			if($this->_error = $this->_error_db()){
				return false;
			}	
			else{
				$this->_success[] = 'Index key was added: '.str_replace("`","", $name);
			}
		}
		return true;
		
	} 

	private function _get_index_keys( $table )
	{	
		$current_index_keys = [];
		$sql   = "SHOW INDEX FROM ".$table;
		$query = $this->CI->db->query($sql);
		foreach ($query->result() as $row)
		{
		  	$current_index_keys[] = $row->Key_name;
		}	
		return $current_index_keys;
	}

	public function item( $name )
	{
		if( isset($this->config_schema[$name]))
		{
			return $this->config_schema[$name];
		}
		else
		{
			return '';
		}
	}

	private function compareMessage( $lastDataTables, $tables )
	{	
		foreach ($tables as $tableName => $columns ) {
			if(!isset($lastDataTables[$tableName]))
			{
				$this->_success[] = "<b>{$tableName}</b> Table Created";
				continue;
			}

			$lastDataTable = $lastDataTables[$tableName];
				
			foreach ($columns as $columnName =>  $attrs) {
				if(!isset($lastDataTables[$tableName][$columnName]))
				{
					$this->_success[] = "<b>{$tableName} . {$columnName}</b> Column Created";
					continue;
				}

				$lastDataColumn = $lastDataTable[$columnName];

				foreach ($attrs as $attrName => $value) {

					if( !in_array($attrName, array_keys($lastDataColumn) ))
					{
						$this->_success[] = "<b>{$tableName} . {$columnName} . {$attrName}</b> Attribute Created";
						continue;
					}
					
					if( $lastDataColumn[$attrName] != $value )
					{
						$this->_success[] = "<b>{$tableName} . {$columnName} . {$attrName}</b> Attribute modified";
					}
					
				}
			}
		}
	}

}	