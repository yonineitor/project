<?php
use Core\Controller\Web_Controller;
/**
 * 
 */
class Dashboard_Controller extends Web_Controller
{
	public function home()
	{
		$this->template->addParam('menuActive','dashboard');
		return  $this->template->render('view-dashboard-home');
	}

	public function code()
	{
		$this->template->addParam('menuActive','code');
		return  $this->template->render('view-dashboard-code');
	}
}