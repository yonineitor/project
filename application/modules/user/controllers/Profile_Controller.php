<?php
use Core\Controller\Web_Controller;
/**
 * 
 */
class Profile_Controller extends Web_Controller
{
	public function form()
	{
		$this->template->render('view-userprofile-form');
	}
}