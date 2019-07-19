<?php
$configSchema = [];
/*
|--------------------------------------------------------------------------
| String: URL Config
|--------------------------------------------------------------------------
|
|	base_url('schema');
|	Can be a controller name
|
*/	
$configSchema['url_controller'] = 'migration';
	

/*
|--------------------------------------------------------------------------
| String: Schema table
|--------------------------------------------------------------------------
|
|	Schema table to save the modification date
|
*/
$configSchema['schema_table'] = 'ci_schema';	

/*
|--------------------------------------------------------------------------
| String: Schema session var
|--------------------------------------------------------------------------
|
|	Session Name to login dashboard
|	
*/
$configSchema['schema_session_var'] = 'session_schema';

/*
|--------------------------------------------------------------------------
| Array: Schema users controller
|--------------------------------------------------------------------------
| 
|	user => password
| 	you have acces to use enviroment visual to execute migration
|	
*/	
$configSchema['schema_session_users'] = Array(
	'admin' => 'secret',
	//'local' => 'newpassword'
);

/*
|--------------------------------------------------------------------------
| String: Schemas Path
|--------------------------------------------------------------------------
|
|	Path to your migrations folder.
|	Typically, it will be within your application path.
|	Also, *writing permission is required* within the migrations path.
|
*/
$configSchema['schema_path'] = APPPATH.'database/';

/*
|--------------------------------------------------------------------------
| Boolean: Default null values for all columns
|--------------------------------------------------------------------------
|
|	If ´null´ attribute is empty
| 
*/
$configSchema['default_null_values'] = TRUE;


/*
|--------------------------------------------------------------------------
| Boolean: KSORT columns
|--------------------------------------------------------------------------
|
| 
*/
$configSchema['sort_first_migration_columns'] = FALSE;


/**
 * Don't remove return array
 */
return $configSchema;