<?php
namespace Lib;

/**
 * 
 */
class Mail
{
	private $mail         = null;
	
	public $errorMessage = '';

	public function __construct( $debug = 0){

		require __DIR__ . '/PHPMailer/class.phpmailer.php';
		require __DIR__ . '/PHPMailer/PHPMailerAutoload.php';

        $this->mail = new \PHPMailer;
        //
        $this->mail->isSMTP();    
        
        $this->mail->SMTPDebug  = $debug;
        
        $this->mail->Host       = env('mail.host');// $config['smtp_host'];
        $this->mail->Port       = env('mail.port');//$config['smtp_port'];
        $this->mail->Username   = env('mail.email');//$config['smtp_user'];
        $this->mail->Password   = env('mail.password');//$config['smtp_pass'];
        //tls
        $this->mail->SMTPSecure = 'ssl';
        $this->mail->CharSet    = 'UTF-8';
        //$this->mail->SMTPSecure = 'tls';
        $this->mail->SMTPAuth   = true;
        $this->mail->setFrom(env('mail.email'), env('mail.email') );
	}
    
	public function setSubject( $subject )
	{
		$this->mail->Subject = $subject;
	}

	public function setFrom( $email , $name  = '' )
	{
        $this->mail->setFrom($email ,  $name );
    }

	public function addAddress( $email  )
	{
		$this->mail->addAddress( $email );
	}

    public function send( $content , $type = 'HTML')
    {
        
        if($type ==='HTML')
        {
        	$this->mail->isHTML(true);
        	$this->mail->msgHTML( $content );
        }
        else
        {
        	$this->mail->isHTML(false); 
        	$this->mail->Body = $content;
        }

        try {
    	    $send = $this->mail->send();
            
            $this->mail->clearAddresses();
            
            if(!$send)
            {
				$this->errorMessage = $this->mail->ErrorInfo;
            }

            return $send;

        }catch(\phpmailerException $e){
           
            $this->errorMessage = $e->errorMessage();
            
            return FALSE;
        }
        catch(\Exception $e)
        {
        	return FALSE;	
        }
    }
}