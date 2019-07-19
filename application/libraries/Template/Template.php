<?php

/**
 * Template
 */
class Template {
    
    /**
     *  @var $CI: instance at framework
     */
    protected $CI;
    
    /**
     *  @var $include_js: JS include on fotter
     */
    protected $include_js             = array();
    
    /**
     *  @var $include_js: CSS include on header
     */
    protected $include_css         = array();
    
    /**
     * @var array
     */
    protected $include_css_external = array();

    /**
     * @var array
     */
    protected $include_js_external = array();

    /**
     *  @var string
     */
    protected $layout_selected;

    /**
     * @var boolean
     */
    protected $load_assets_success;
    
    /**
     * @var string
     */
    protected $layout_title;

    /**
     * @var string
     */
    protected $menu_files;

    /**
     * @var array
     */
    protected $body_attr = array();

    /**
     *  @var $path_js: folder js
     */
    protected $path_js;
    
    /**
     *  @var $path_css: folder css
     */
    protected $path_css;

    /**
     * @var $config
     */
    private $config;

    /**
     * @var $new_id
     */
    private $new_id;

    /**
     * @var $_extra_data_params
     */
    private $_extra_data_params = [];

	function __construct()
    {
        $this->CI =& get_instance();

        $this->config = include_once __DIR__ . '/Config.php';

        if( !function_exists('base_url'))
        {
            $this->CI->load->helper('url');
        }

        if( $this->getConfig('regenerate_assets') === TRUE)
        {   
            $this->new_id   =  '?id='.uniqid();
        }

        $this->bodyAttr( ['cz-shortcut-listen' => 'true' ] );
    }

    /**
     * @param String $name
     * @param Mixed $defaultValue
     * 
     * @return mixed
     */
    public function getConfig( $name = false, $defaultValue = null)
    {
        if($name === false )
        {
            return $this->config;
        }

        if(isset($this->config[$name]))
            return $this->config[$name];
        else
            return $defaultValue;

    }

    public function addParam( $name, $values )
    {
        if(!in_array($name,['_','config'] ) )
        {
            $this->_extra_data_params[$name] = $values;
        }

        return $this;  
    }

    /**
     *->setLayout
     *  
     *  Define view header and footer theme in folder view/themes/
     *
     * @param String $layout_name
     * @param String $layout_title
     * 
     * @return \Template
     */
    function setLayout( $layout_name , $layout_title = '' )
    {
        $this->layout_selected = $layout_name.'_layout';
        $this->layout_title    = ($layout_title != '') ? $layout_title : $layout_name;

    	return $this;
    }
    
    /**
     *->body
     *  
     *  Define content extra on body
     *
     * @param name   array     name file view
     */
    function bodyAttr( $attr )  
    { 
        $this->body_attr = array_merge($this->body_attr, $attr);
        
        return $this;
    } 
    
    /**
     * title() 
     *
     * Set title page
     *
     * @param layout_title \String itle page
     *
     */
    function title( $layout_title )
    {
        $this->layout_title = $layout_title;

        return $this;
    }

    /**
     *->render [render website]
     *  
     *  Define view default
     *
     * @param view   string     name file view
     * @param parms  array      vars default view
     * @param return boolean    return html or print
     */
    function render( $view, $parms = array(), $return = FALSE )
    {
        
        //get css and js defaults per layout
        $this->_load_assets_layout( $this->layout_selected );

        //get content css
        $css = '';
        foreach ($this->include_css as $value) {
            $css.= $this->_print_css( $value , FALSE );   
        }
        
        //get content js
        $js = '';
        foreach ($this->include_js as $value) { 
            $js.= $this->_print_js( $value , false );   
        }
        
        $view = ( strpos( $view , "." ) === false ) ?  $view : $view.'.php'; 
        
        $body_attr = implode(' ', array_map(
            function ($v, $k) { return sprintf('%s="%s"', $k, $v); },
            $this->body_attr,
            array_keys($this->body_attr)
        ));
        
        $params = [
            'config' => [
                'css'   => $css,
                'js'    => $js,
                'title' => $this->layout_title,
                'view'  => $view,
                'body'  => $body_attr
            ],
            '_'     => $parms
        ];
        
        $params['config']['return_view'] = $return;

        $params = array_merge($this->_extra_data_params, $params  );
        
        $this->CI->load->view_layout( 
            $this->getConfig('layout_template_path'),
            $this->layout_selected, 
            $params, 
            $return 
        );
        
        if( !$return && $this->getConfig('render_template') )
        {
            /**
             * FORCE ENABLE HOOK
             */
            $this->CI->hooks->enabled = TRUE;
            $this->CI->hooks->hooks['post_system'] = function()
            {
                $segments = $this->CI->uri->rsegment_array();
                array_splice($segments, 0 , 2);
                $segments = array_filter($segments);
                $this->CI->db->insert( $this->getConfig('render_template'),  [
                    'date' => date('Y-m-d H:i:s'),
                    'controller' => $this->CI->router->class,
                    'action' => $this->CI->router->method,
                    'time' => $this->CI->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end'),
                    'memory_mb' => round(memory_get_usage() / 1024 / 1024, 2),
                    'params' => implode(',', $segments )
                ]);
            };
        }
        
        if( $this->getConfig('minify_output') )
        {
            /**
             * FORCE ENABLE HOOK
             */
            $this->CI->hooks->enabled = TRUE;
            $this->CI->hooks->hooks['display_override'] = function()
            {
                $buffer = $this->CI->output->get_output();

                $re = '%# Collapse whitespace everywhere but in blacklisted elements.
                    (?>             # Match all whitespans other than single space.
                      [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
                    | \s{2,}        # or two or more consecutive-any-whitespace.
                    ) # Note: The remaining regex consumes no text at all...
                    (?=             # Ensure we are not in a blacklist tag.
                      [^<]*+        # Either zero or more non-"<" {normal*}
                      (?:           # Begin {(special normal*)*} construct
                        <           # or a < starting a non-blacklist tag.
                        (?!/?(?:textarea|pre|script)\b)
                        [^<]*+      # more non-"<" {normal*}
                      )*+           # Finish "unrolling-the-loop"
                      (?:           # Begin alternation group.
                        <           # Either a blacklist start tag.
                        (?>textarea|pre|script)\b
                      | \z          # or end of file.
                      )             # End alternation group.
                    )  # If we made it here, we are not in a blacklist tag.
                    %Six';
                    
                $new_buffer = preg_replace($re, " ", $buffer);
                // We are going to check if processing has working
                if ($new_buffer === null)
                {
                    $new_buffer = $buffer;
                }

                $this->CI->output->set_output($new_buffer);
                $this->CI->output->_display();
            };
        }
        
    }

    /**
     *->render [render website]
     *  
     *  Define view default
     *
     * @param view   string     name file view
     * @param parms  array      vars default view
     * @param return boolean    return html or print
     */
    function render_view($view, $parms = array(), $return = FALSE )
    {
        $params = [
            '_'     => $parms
        ];
        
        $simple_view = ( strpos( $view , "." ) === false ) ?  $view : $view.'.php'; 

        if($return) 
        {   
            $content = $this->CI->load->view(
                $simple_view, 
                $params, 
                TRUE
            );
            
            return $content;
        }   
        else
        {   
            $this->CI->load->view(
                $simple_view, 
                $params, 
                FALSE
            );
        }
    }
    
    /**
     *->json
     *  
     *  Send json output config @ReturnJson in comments
     *  
     * @param data   array|object      send json output
     */ 
    function json( $data , $option_json =  'JSON_UNESCAPED_UNICODE' )
    {
        
        if(!defined('JSON_PRESERVE_ZERO_FRACTION'))
        {
            define('JSON_PRESERVE_ZERO_FRACTION', 1024);
        }
        /*
        $options_availible = [
            'JSON_HEX_QUOT',
            'JSON_HEX_TAG',
            'JSON_HEX_AMP',
            'JSON_HEX_APOS',
            'JSON_NUMERIC_CHECK',
            'JSON_PRETTY_PRINT',
            'JSON_UNESCAPED_SLASHES',
            'JSON_FORCE_OBJECT',
            'JSON_PRESERVE_ZERO_FRACTION', 
            'JSON_UNESCAPED_UNICODE', 
            'JSON_PARTIAL_OUTPUT_ON_ERROR'
        ];
        */
        $this->CI->output
            ->set_status_header(200)
            ->set_content_type('json', 'UTF-8');
        /*
        if(!in_array($option_json, $options_availible))
        {   
            $option_json = 0;
        }
        else if(defined($option_json))
        {
            $option_json =  constant($option_json);
        }
        else if($option_json)
        {
            http_response_code(404);
            echo @json_encode( ['status' => 0, 'msg' => "Option JSON not works {$option_json}" ] ); 
            exit;
        }
        */
        
        $data = array_merge($this->getConfig('return_data', []), $data  );

        return json_encode( $data , JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    }

    /**
     *->json_entitie
     *  
     *  Send json_entitie output config @ReturnJson in comments
     *  
     * @param data   array|object      send json output type html
     */ 
    public function json_entities( $data = null )
    {
        //stripslashes
        return str_replace( '\n',"\\"."\\n",
            htmlentities(
                utf8_encode( json_encode( $data )  ) , 
                ENT_QUOTES | ENT_IGNORE, 'UTF-8' 
            )
        );
    }


    /**
     *->js
     *  
     *  Set JS end document
     *  
     * @param String $url
     */
    function js( $url , $localURL  =  true )
    {

        $url = ($localURL) ? site_url($url) : $url;

        $this->include_js_external[] = $url;
        
        return $this;
    }
    
    /**
     *->css
     *  
     *  Set CSS init document
     *  
     * @param String $url
     */
    function css( $url, $localURL = true )
    {
        $url = ($localURL) ? site_url($url) : $url;

        $this->include_css_external[] = $url;
        return $this;
    }
    
    /**
     *->base64_img
     */
    function base64_img( $file )
    {
        $type     = pathinfo(  $file, PATHINFO_EXTENSION);
        $contents = file_get_contents($file);   
        return 'data:image/' . $type . ';base64,' . base64_encode($contents);
    } 
    
    /**
     *->renderImage
     */
    function render_file( $file , $previewBrowserTypes = ['pdf','jpg','png','jpeg'] )
    {

        $fileInfo = pathinfo( $file );
        
        $finfo    = finfo_open(FILEINFO_MIME_TYPE); // devuelve el tipo mime de su extensión
        $mime     = finfo_file($finfo, $file);
        finfo_close($finfo);

        header('Expires: 0');
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");

        if(in_array($fileInfo['extension'], $previewBrowserTypes))
        {
           
            header('Content-Disposition: inline; filename="'.basename($file).'"');
            header( "Content-type: ".$mime);
            readfile( $file ); 
        }
        else
        {   
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Content-Length: ' . filesize($file));
        }
        
        exit;
    }

    /**
     * ->renderPreview
     */
    public function render_preview( $file )
    {


        $fileInfo = pathinfo( $file );
        
        $finfo    = finfo_open(FILEINFO_MIME_TYPE); // devuelve el tipo mime de su extensión
        $mime     = finfo_file($finfo, $file);
        finfo_close($finfo);

        header('Expires: 0');
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");

        if( $fileInfo['extension'] === 'pdf' )
        {   
            $im = new imagick($file .'[0]');
            $im->setImageFormat('jpg');
            header('Content-Type: image/jpeg');
            echo $im;
        }
        else{
            header( "Content-type: ".$mime);
            readfile( $file ); 
        }

        exit;
    }
    
    /**
     *->download CSV
     */
    public function download_csv( $file_name, $data, $columns = array() , $separate = ',' )
    {
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$file_name);

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');
        if( count($columns) > 0 )
        {   
            fputcsv( $output, $columns, $separate );
        }
        
        foreach ($data as $row) {
            fputcsv($output, $row , $separate );
        }

        fclose($output);
    }

    private function _load_assets_layout( $layout_name )
    {

        $include_js =  $include_css = [];
        
        $layouts = $this->getConfig('layouts');
        
        /**
         * All layouts
         */
        if( isset($layouts['*']) )
        {
            if(isset($layouts['*']['css']) )
            {
                foreach ($layouts['*']['css'] as $key => $value) 
                {
                    $include_css[] = ($value==='external') ? $key : base_url($value);
                }
            }
            
            if(isset($layouts['*']['js']) )
            {
                foreach ($layouts['*']['js'] as $key => $value) 
                {
                    $include_js[] = ($value==='external') ? $key : base_url($value);
                }
            }
        }

        /**
         * specific layout
         */
        if(isset($layouts[$layout_name]))
        {
            if(isset($layouts[$layout_name]['css']) )
            {
                foreach ($layouts[$layout_name]['css'] as $value) 
                {
                    $include_css[] = base_url($value);
                }
            }
            
            if(isset($layouts[$layout_name]['js']) )
            {
                foreach ($layouts[$layout_name]['js'] as $value) 
                {
                    $include_js[] = base_url($value);
                }
            }
        }


        $this->include_css = array_merge($include_css, $this->include_css_external);

        $this->include_js  = array_merge($include_js, $this->include_js_external);

    }
    
    private function _print_js( $url , $print = TRUE )
    {
        $fileURL = $url . $this->new_id;
        
        if($print){
            echo '<script type="text/javascript"  src="'.$fileURL.'"></script>';
        }else{
            return '<script type="text/javascript"  src="'.$fileURL.'"></script>';
        }
    }
    
    private function _print_css( $url , $print = TRUE)
    {
        $fileURL = $url . $this->new_id;

        if($print){
            echo '<link rel="stylesheet" href="'.$fileURL.'" type="text/css" />';
        }else{
            return '<link rel="stylesheet" href="'.$fileURL.'" type="text/css" />';
        }
        
    }

    public function jsonData( $idName , $data = null )
    {
        $data = is_array($data) ? $data : [];
        
        echo '<textarea id="'.$idName.'" style="display:none;">'.json_encode($data).'</textarea>';

        return $this;
    }
    
}
