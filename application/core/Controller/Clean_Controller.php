<?php
namespace Core\Controller;

/**
 * 
 */
class Clean_Controller extends \MX_Controller
{
	public $autoload = [
        'libraries' => ['database', 'Template','form_validation', 'user_agent'],
        'helper' => ['security','url']
    ];
    
    public function __construct()
	{
		parent::__construct();
    	
        $this->lang->load('labels');
    	$this->template->setLayout('demp');
	}
}