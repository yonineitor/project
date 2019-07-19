<?php
namespace Lib;
/**
 * 
 */
class Validation extends \CI_Form_Validation
{
	public function __construct( $rules = Array() )
	{
		parent::__construct($rules);
		//args
		$this->set_message('in_table',  "Es requerido especificar el campo {field}");
		$this->set_message('is_diferent',  "The field {field} must be unique");
		$this->set_message('valid_phone',  "The field {field} is not a valid phone number");
		$this->set_message('valid_date_max', 'The {field} field can not be greater than the current date');
		$this->set_message('valid_date_min', 'The {field} field can not be less than the current date');
		$this->set_message('valid_date', 'The {field} field is not a valid date');
		$this->set_message('exist_date', 'Field {field} is not valid date');
        $this->set_message('parse_float', 'Field {field} is not valid number');
        $this->set_message('pin_verify',   'The PIN you have entered does not match your current one');
        $this->set_message('numeric_float',   'The field {field} must be number');
	}
    
	public function setRules( $keyPost, $rules, $keyName = '' )
	{
		if($keyName === '')
		{
			$keyName = ucwords( str_replace("_"," ",$keyPost) );
		}
		
        $this->set_rules( $keyPost, $keyName, $rules );
	   
        return $this;
    }

    /**
     * is_diferent[10,roles.title]
     */
	public function is_diferent( $str, $data )
	{
		$exp = explode(",",$data);
		$id = $exp[0];
		if(!isset($exp[1]))
		{
			return TRUE;
		}
		$field = $exp[1];
		sscanf($field, '%[^.].%[^.]', $table, $campo);
		//
		$this->CI->db->select('id');
        $this->CI->db->from($table);
		$this->CI->db->where([ $campo => trim($str) ]);
		$this->CI->db->where(['id != '=> $id]);

        $r = $this->CI->db->get()->row_array();

        if( $r && $r['id'] != $id )
        {   
        	return FALSE;
        }
        else
        {
        	return TRUE;
        }
	}

	public function in_table(  $str, $field  )
	{
		sscanf($field, '%[^.].%[^.]', $table, $field);
            
        if( ($this->CI->db->select('id')->limit(1)->get_where($table, array($field => $str))->num_rows() === 0) )
        {   
            
            return FALSE;
        }
        else
        {
            return TRUE;
        }
	}

	public function valid_phone( $value )
	{
		$value = trim($value);
	    if (preg_match('/^\(?[0-9]{3}\)?[-. ]?[0-9]{3}[-. ]?[0-9]{4}$/', $value))
        {
        	//valid phone number
        	//ignore ( )
        	//ignore -
            return preg_replace('/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/', 
            	'($1) $2-$3', 
            	$value);
        }
        else
        {
            return FALSE;
        }
	}

	public function valid_date( $str, $format = '' )
	{
		$date = @date_parse( $str ); 
        
        if ($date["error_count"] == 0 && checkdate($date["month"], $date["day"], $date["year"]))
            return TRUE;
        else
        {   
            return FALSE;
        }
	}

	public function valid_date_max( $str)
    {
        $date = @date_parse( $str ); 
        
        if ($date["error_count"] == 0 && checkdate($date["month"], $date["day"], $date["year"]))
        {
            $today         = (int)date('Ymd');
            $date_selected = (int)date('Ymd',strtotime($str));
            
            if($date_selected > $today )
            {   
               
                return FALSE;
            }
        }
        
        return TRUE;
    }

    public function valid_date_min( $str)
    {
        $date = @date_parse( $str ); 
        
        if ($date["error_count"] == 0 && checkdate($date["month"], $date["day"], $date["year"]))
        {
            $today         = (int)date('Ymd');
            $date_selected = (int)date('Ymd',strtotime($str));
            
            if($date_selected < $today )
            {   
                return FALSE;
            }
        }
        
        return TRUE;
    }
    
    public function numeric_float($str, $data = '')
    {
        if( is_null($str))
        {
            return null;
        }
        
        $value = trim($str);

        if($value!='' && !is_numeric($value))
        {
            return FALSE;
        }

        if($value==='')
            return null;

        //parse float
        return sprintf("%01.2f" , $value );
    }
    
    public function numeric_integer($str, $data = '')
    {

        $value = trim($str);
        
        if( $value!='' && !is_numeric($value) )
        {
            return FALSE;
        }
        
        //parse integer
        return (int)$value;
    }

    function pin_verify( $str )
    {
        if( $str && $str === \Lib\Auth::user('pin') ) 
        {   
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
}