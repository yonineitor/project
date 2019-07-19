<?php
use Core\Controller\Web_Controller;
use Model\Activity;
use Lib\Response;
/**
 * 
 */
class Activity_Controller extends Web_Controller
{
	/**
	 * get
	 */
	public function print( String $activityType, Int $id = 0 )
	{
		$records = Activity::getRecords( $activityType, $id );
		
		return Response::json(Array(
			'status' => 1,
			'activities' => $records
		));
	}
	
}