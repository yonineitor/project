<?php
namespace Model;

use Core\Model;
use Lib\Auth;
/**
 * 
 */
class Activity extends Model
{	
	const EVENT_TYPES = Array(
		'last_value'  => 'Last value',
		'last_values' => 'Last values',
		'set_values'  => 'Set values',
		'reason'      => 'Reason change',
		'notes'       => 'Notes'
	);

	private $myData = null;

	private $lang  = null;

	private $currentDate = null;

	public static function setLang( $lang )
	{
		$vm = self::getInstance();
		$vm->lang = $lang;
	}

	public function getLastRecord()
	{
		$itemActivity = $this->result();
		$itemActivity['data'] = $this->getData($itemActivity);
		$itemActivity['event'] = isset($this->lang[$itemActivity['event']])  ? $this->lang[$itemActivity['event']] : $itemActivity['event']; 
		
		//$user = \Model\User::get($itemActivity['user_id'],['id','username'])->result();
		//$itemActivity['username'] = $user['username'];
		
		return $itemActivity;
	}

	public static function getRecords( String $group, Int $relationId )
	{
		$valueGroup = $group;
		$valueId    = $relationId;

		$vm = self::retrieve(function($qb) use ($valueGroup, $valueId ){
			
			$qb->select([
				'activity.id',
				'activity.user',
				'activity.relation_id',
				'activity.subrelation_id',
				'activity.activity_group',
				'activity.activity_subgroup',
				'activity.data',
				'activity.created_at',
				'activity.event',
				'activity.updated_at',
			]);

			$qb->where([
				'activity_group' => $valueGroup,
				'relation_id' => $valueId
			]);

			$qb->order_by('activity.created_at DESC ');
		},'ARRAY');

		$result = $vm->result();
		
		foreach ($result as &$item) {
			$item['data'] = $vm->getData($item);
			$item['event'] = isset($vm->lang[$item['event']]) ? $vm->lang[$item['event']] : $item['event'];
		}

		return $result;
	}

	public function getData( Array $item )
	{
		$fieldsValues   = [];
		$typeOutput     = '';
		$commentsOutput = '';
		if($item['data'])
		{
			$elements       = (Array)@json_decode($item['data']);
			$typeOutput     = isset($elements['type']) ? $elements['type'] : '';
			$commentsOutput = isset($elements['comments']) ? $elements['comments'] : '';

			if(is_object($elements['fields']))
			{	
				$fieldsValues = [];
				foreach ($elements['fields'] as $key => $value) {
					$fieldsValues[ucfirst(str_replace("_"," ",$key))] = $value;
				}
			}
		}

		return Array(
			'type' => $typeOutput,
			'fields' => $fieldsValues,
			'comments' => $commentsOutput
		);
	}

	public static function getChanges( Array $dataA, Array $dataB, $outputOldValues =  true )
	{
		$outPutData = Array();
		foreach ($dataA as $key => $value) {

			$valueB = is_null($dataB[$key]) ? '' : $dataB[$key];

			if($valueB != $value )
			{
				$outPutData[$key] = ($outputOldValues) ? $valueB : $value;
			}
		}

		if(count($outPutData) > 0)
			return $outPutData;

		return FALSE;
	}
	
	/**
	 * Data can be contains
	 */
	// 	type (last_value, last_values, reason)
	//	
	public static function setData( String $type , $dataValue = null )
	{
		$vm = self::getInstance();
		
		$encode = Array('type' => $type, 'fields' => [], 'comments' => '' );
		
		if(is_array($dataValue))
		{
			
			$encode['fields'] = $dataValue;
		}
		else
		{
			$encode['comments'] = $dataValue;
		}
		
		$vm->myData = json_encode( $encode );

		return $vm;
	}

	public function _preInsert( $params )
	{
		$params['user']    = $params['user'] ?? Auth::user('username');
		if( !isset($params['created_at']) )
		{
			if(is_null($this->currentDate) )
				$this->currentDate = date('Y-m-d H:i:s');
			
			$params['created_at'] = $this->currentDate;
		}
		
		if($this->myData)
			$params['data'] = $this->myData;

		return $params;
	}

	public function _postInsert( $e )
	{
		$this->myData = null;
	}

	public static function canOpen( String $group, $relation_id, $register = true )
	{
		$dataReturn = Array(
			'first_open' => false,
			'taken_by' => '',
			'ok' => true,
			'past_time' => false,
			'same_user' => false,
			'id' => 0
		);
		$vm = self::getInstance();
		
		$valueGroup = $group;
		$valueId    = $relation_id;

		$vm = self::retrieve(function($qb) use ($valueGroup, $valueId){
			
			$qb->select([
				'activity.id',
				'activity.user',
				'activity.relation_id',
				'activity.subrelation_id',
				'activity.activity_group',
				'activity.created_at',
				'activity.updated_at',
				'activity.event',
			]);

			$qb->where([
				'activity.activity_group' => $valueGroup,
				'activity.relation_id' => $valueId,
				'activity.event' => 'actmessage_open'
			]);

			$qb->order_by('activity.id DESC, activity.updated_at desc');
			
			$qb->limit(1);

		},'ROW');

		$result      = $vm->result();
		$currentDate = new \DateTime();
		if(! $result   )
		{
			if($register)
			{
				$vm->db->insert('activity',[
					'user' => Auth::user('username'),
					'relation_id' => $relation_id,
					'activity_group' => $valueGroup,
					'event' => 'actmessage_open',
					'created_at' => $currentDate->format('Y-m-d H:i:s'),
					'updated_at' => $currentDate->format('Y-m-d H:i:s')
				]);

				$dataReturn['id'] = $vm->db->insert_id();
			}
			$dataReturn['first_open'] = true;
		}
		else
		{

			//add 6 seconds
			$activityTimeSeconds = env('activity_time', 10);
			$updatedAt  = new \DateTime($vm->result('updated_at'));
			$updatedAt->add(new \DateInterval('PT'.$activityTimeSeconds.'S'));
			if ($updatedAt < $currentDate) 
			{	
				if($register)
				{
					$vm->db->insert('activity',[
						'user' => Auth::user('username'),
						'relation_id' => $relation_id,
						'activity_group' => $valueGroup,
						'event' => 'actmessage_open',
						'created_at' => $currentDate->format('Y-m-d H:i:s'),
						'updated_at' => $currentDate->format('Y-m-d H:i:s')
					]);
					$dataReturn['id'] = $vm->db->insert_id();
				}

				$dataReturn['past_time'] = true;
			}
			else if( $vm->result('user') == Auth::user('username') )
			{	
				if($register)
				{
					$vm->db
						->where(['id' => $vm->result('id') ])
						->update('activity',[
							'updated_at' => $currentDate->format('Y-m-d H:i:s')
					]);
					
					$dataReturn['id'] =$vm->result('id');
				}
				
				$dataReturn['same_user'] = true;
			}
			else
			{
				$dataReturn['ok']       = false;
				$dataReturn['taken_by'] = $vm->result('username');
			}
		}
		
		return $dataReturn;
	}
	
}