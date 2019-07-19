<?php
namespace Autoload;

/**
 * My Autoload
 */
class Loader
{
	private $fileLoaded = [];

	public function __construct()
	{
		/**
		 * Model\ClassName
		 */
		spl_autoload_register( array( $this, 'loadModel') );
		/**
		 * Core\ClassName
		 */
		spl_autoload_register( array( $this, 'loadCore') );
		/**
		 * Lib\ClassName
		 */
		spl_autoload_register( array( $this, 'loadLibrary') );
		/**
		 * Pdf\ClassName
		 */
		spl_autoload_register( array( $this, 'loadPdf') );
	}

	/**
	 * 
	 */
	public function loadPdf( $class )
	{
		
		$stringClassName = explode("\\",$class);
		$folder          = $stringClassName[0];

		if( $stringClassName[0] === 'Pdf' && isset($stringClassName[1]) )
		{
			$file  = APPPATH . 'modules' . DIRECTORY_SEPARATOR . strtolower( $stringClassName[1] ) . '/pdf' . DIRECTORY_SEPARATOR; 

			unset($stringClassName[0]);
			if(isset($stringClassName[2]))
			{
				unset($stringClassName[1]);
				$file.= implode("\\", $stringClassName).'.php';
			}
			else
			{
				$file.= $stringClassName[1].'.php';
			}
			
			$this->registerFile( $file );
		}
	}
	
	/**
	 * 
	 */
	public function loadModel( $class )
	{
		
		if ( ! class_exists('\CI_Model', FALSE))
		{
			require_once(BASEPATH.'core'.DIRECTORY_SEPARATOR.'Model.php');
		}

		$stringClassName = explode("\\",$class);
		$folder          = $stringClassName[0];

		if( $stringClassName[0] === 'Model' && isset($stringClassName[1]) )
		{
			$file  = APPPATH . 'modules' . DIRECTORY_SEPARATOR . strtolower( $stringClassName[1] ) . '/models' . DIRECTORY_SEPARATOR; 

			unset($stringClassName[0]);
			if(isset($stringClassName[2]))
			{
				unset($stringClassName[1]);
				$file.= implode("\\", $stringClassName).'.php';
			}
			else
			{
				$file.= $stringClassName[1].'.php';
			}

			#echo "<pre>".print_r($file, 1)."</pre>";

			$this->registerFile( $file );
		}
	}
	
	/**
	 * 
	 */
	public function loadCore( $class )
	{
		$stringClassName = explode("\\",$class);
		$folder          = $stringClassName[0];
		
		if( $stringClassName[0] === 'Core' && isset($stringClassName[1]) )
		{

			$file  = APPPATH . 'core' . DIRECTORY_SEPARATOR; 
			
			unset($stringClassName[0]);
			if(isset($stringClassName[2]))
			{
				//unset($stringClassName[1]);
				$file.= implode("//", $stringClassName).'.php';
			}
			else
			{
				$file.= $stringClassName[1].'.php';
			}
			
			$this->registerFile( $file );
		}
	}

	/**
	 * 
	 */
	public function loadLibrary( $class )
	{
		$stringClassName = explode("\\",$class);
		$folder          = $stringClassName[0];
		
		if( $stringClassName[0] === 'Lib' && isset($stringClassName[1]) )
		{
			$file  = APPPATH . 'libraries' . DIRECTORY_SEPARATOR . $stringClassName[1] . DIRECTORY_SEPARATOR; 
			
			unset($stringClassName[0]);
			if(isset($stringClassName[2]))
			{
				unset($stringClassName[1]);
				$file.= implode("\\", $stringClassName).'.php';
			}
			else
			{
				$file.= $stringClassName[1].'.php';
			}
			
			$this->registerFile( $file );
		}
		
	}

	/**
	 * 
	 */
	private function registerFile( $file )
	{
		if( in_array( $file , $this->fileLoaded ))
		{
			return true;
		}
		
		if(file_exists($file))
		{
			$this->fileLoaded[] = $file;
			//echo "<pre>".print_r(get_included_files(), 1)."</pre>";
			include_once $file;
		}
	}

}
