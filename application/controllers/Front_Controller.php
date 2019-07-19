<?php

/**
 * summary
 */
class Front_Controller extends CI_Controller
{	
	/**
	 * @param String $module
	 * @param String $file
	 * 
	 * @return text
	 */
    public function js( $module = '', $file ='' )
    {
    	
		$filePath = APPPATH . 'modules/' . $module. '/front/'.$file;
		if(!file_exists($filePath) || !is_readable($filePath))
		{
			show_error("El archivo js no existe o no tiene permisos de lectura", 404 );
			return false;
		}
		
		$file     = fopen($filePath, "r");
		$fileSize = filesize($filePath);
		
		if($fileSize)
		{
			header('Content-Type: application/javascript');
			header("Content-Length: " . $fileSize );
			echo fread($file, $fileSize );
		}
		return true;
    }
    
    /**
	 * @param String $module
	 * @param String $file
	 * 
	 * @return text
	 */
    public function css( $module = '', $file ='' )
    {
		$filePath = APPPATH . 'modules/' . $module. '/front/'.$file;
		if(!file_exists($filePath) || !is_readable($filePath))
		{
			show_error("El archivo css no existe o no tiene permisos de lectura", 404 );
			return false;
		}

		$file     = fopen($filePath, "r");
		$fileSize = filesize($filePath);
		
		if($fileSize)
		{
			header("Content-Type: text/css");
			header("Content-Length: " . $fileSize );
			echo fread($file, $fileSize );
		}
    }

    /**
	 * @param String $module
	 * @param String $file
	 * 
	 * @return text
	 */
    public function img( $file )
    {
		$filePath = APPPATH . '../assets/img/'.$file;
		if( !is_file($filePath) || !is_readable($filePath)  )
		{
			show_error("Document not found");
		}
		
		$fileInfo = pathinfo( $filePath );
        $finfo    = finfo_open(FILEINFO_MIME_TYPE); // devuelve el tipo mime de su extensión
        $mime     = finfo_file($finfo, $filePath);
       	
        finfo_close($finfo);
        
        header('Expires: 0');
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");
        header('Content-Disposition: inline; filename="'.basename($filePath).'"');
        header( "Content-type: ".$mime);
        readfile( $filePath ); 
    }

    /**
     * @param String $type original|resize
	 * @param String $image_name
	 * 
	 * @return text
	 */
    public function image( $type = 'original', $image_name = '')
    {

    	$filePath = env('image.'.$type.'_path').$image_name;

		if( !is_file($filePath) || !is_readable($filePath)  )
		{
			show_error("Document not found");
		}

		$fileInfo = pathinfo( $filePath );
        $finfo    = finfo_open(FILEINFO_MIME_TYPE); // devuelve el tipo mime de su extensión
        $mime     = finfo_file($finfo, $filePath);
        
        finfo_close($finfo);
        
        header('Expires: 0');
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");
        header('Content-Disposition: inline; filename="'.basename($filePath).'"');
        header( "Content-type: ".$mime);
        readfile( $filePath ); 
    }
}