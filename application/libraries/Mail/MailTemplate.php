<?php
error_reporting(E_ALL);
ini_set('display_errors', 1 );

require 'PHPMailer/class.phpmailer.php';
require 'PHPMailer/PHPMailerAutoload.php';

class MailTemplate  {
    
    public $error;

    private $mail = null;   
    
    private $email_contacts = [];

    public function __construct( $config  ){
    	
        $this->mail = new PHPMailer;
        //
        $this->mail->isSMTP();    
        
        $this->mail->SMTPDebug  = 0;
        
        $this->mail->Host       = $config['smtp_host'];
        $this->mail->Port       = $config['smtp_port'];
        $this->mail->Username   = $config['smtp_user'];
        $this->mail->Password   = $config['smtp_pass'];
        
        $this->mail->CharSet    = 'UTF-8';
        $this->mail->SMTPSecure = 'ssl';
        $this->mail->SMTPAuth   = true;
       
        $this->mail->setFrom($config['smtp_user'] , 'Greenshieldtech');
    }
    
    public function setFrom( $email , $name  = '' ){
        $this->mail->setFrom($email ,  $name );
    }
    

    public function setSubject( $subject ){
    	$this->mail->Subject = $subject;
    } 	

    public function addAddress( $email ){
    	$this->mail->addAddress( $email );	
    } 	

    public function setContentHTML( $content ){
    	$this->mail->isHTML(true);
        $this->mail->msgHTML( $content );
    }
    public function setContentTEXT( $content ){
        $this->mail->isHTML(false); 
        $this->mail->Body = $content;
    }
    
    public function send( $content , $type = 'HTML'){
        try {
    	    $send = $this->mail->send();
            
            $this->mail->clearAddresses();
            
            return $send;
        }catch(\phpmailerException $e){
            $this->error = $e->errorMessage();
            return FALSE;
        }
    }
    
    public function addEmbeddedImage( $file_img, $source  = '' ) {
        
        return $this->mail->AddEmbeddedImage( $file_img ,  
                $source, 
                basename( $file_img ) , 
                'base64',  
                'image/png'  );
    }

    public function addAttachmentImage( $path, $name, $typeFile = 'image/png' )
    {
        $name = basename($name);

        return $this->mail->AddAttachment( $path , 
            $name, 
            'base64', 
            $typeFile );
    }
}
    
$config       = include 'config.php';
$MailTemplate = new MailTemplate( $config );

//$MailTemplate->setFrom('jonathanq@greenshieldtech.com','Accounts greenshieldtech');
//$MailTemplate->addAddress('yonice.perez@gmail.com');
//$MailTemplate->setSubject('Subject');
//$MailTemplate->setContentTEXT("Content \n break line");
//$MailTemplate->send();
