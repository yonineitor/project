<?php
namespace ThirdParty;

include_once __DIR__ . '/Schema_Library.php';


class Schema_Controller extends \CI_Controller
{		
	function __construct()
	{
		parent::__construct();
		
		$this->load->add_package_path(APPPATH.'/third_party/Schema');	
		$this->load->helper(['url']);
		$this->load->database();
		$this->load->library('session');
		$this->schema_library = new \Schema_Library();

		$this->url_controller  = site_url($this->schema_library->item('url_controller'));
	}

	/**
	 * 
	 */
	public function index(){
		$this->load->view('schema-login',['site' => $this->url_controller]);
	}

	/**
	 * 
	 */
	public function login(){
		
		$can_login = $this->schema_library->login( 
			$this->input->post('user'),  
			$this->input->post('password')
		);

		if ( $can_login  ){
			redirect($this->schema_library->item('url_controller').'/dashboard');
		}else{
			redirect($this->schema_library->item('url_controller') );
		}
	}	

	/**
	 * 
	 */
	public function dashboard(){
		
		if( $var['user'] = $this->schema_library->isLogged()){	

			$var['list_schemas']          = $this->schema_library->getSchemasPending(); 
			$var['list_schemas_migrated'] = $this->schema_library->getSchemasMigrated(); 
			$var['last_modify']           = $this->schema_library->getSchemasLastModify();
			$var['install_files'] 		  = $this->schema_library->getInstallFiles();
			$var['schema_path']			  = $this->schema_library->item('schema_path');
			$var['site']                  = $this->url_controller;
			$var['database']              = $this->db->database;
			$var['username']              = $this->db->username;
			$this->load->view('schema-dashboard', $var);		
		}else{
			redirect($this->schema_library->item('url_controller') );
		}
	}

	/**
	 * @return json
	 */
	public function runmigration(){
		
		if($this->schema_library->isLogged()){
			
			header('content-type application/javascript');

			if ( $response = $this->schema_library->runMigration( $this->input->get('name')  )){
				echo json_encode( [	
					'status' => 1, 
					'schema_log' => $response,
					'message_success' => $this->schema_library->getSuccess() ]
				);	
			}else{
				echo json_encode( [
					'status' => 0, 
					'msg' => $this->schema_library->getError(),
					'message_success' =>  $this->schema_library->getSuccess()
				]);		
			}
		}
		else
		{
			redirect($this->schema_library->item('url_controller') );
		}
	}	

	
	public function logout(){	
		$this->schema_library->logout();
		redirect($this->schema_library->item('url_controller') );
	}

	public function runinstall()
	{
		if($this->schema_library->isLogged()){
			
			header('content-type application/javascript');

			$response = $this->schema_library->runInstallFiles();
			
			$msg      = $this->schema_library->getError();
		
			if ( $msg ){
				echo json_encode( [
					'status' => 0, 
					'message' => $msg
				]);
			}else{
				
				echo json_encode( [	
					'status' => 1, 
					'message' => "Data update (".$response['updateCount'].") Data Insert (".$response['insertCount'].")" ,
				]);
			}
		}
		else
		{
			redirect($this->schema_library->item('url_controller') );
		}
	}

}