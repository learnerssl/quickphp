<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/17
 * Time: 12:41
 */
/**
 * smtp发送邮件类
 */
include_once ROOT . "/extend/phpmailer/PHPMailerAutoload.php";

class Mailer {
	private $mail;
	private $address;
	private $subject;
	private $body;
	private static $_instance;
	
	private function __construct( $address, $subject, $body )
	{
		$this->mail = new PHPMailer( true ); //Passing `true` enables exceptions
		$this->address = $address;
		$this->subject = $subject;
		$this->body = $body;
	}
	
	private function __clone()
	{
		// TODO: Implement __clone() method.
	}
	
	public static function getInstance( $address, $subject, $body )
	{
		if ( ! self::$_instance ) {
			self::$_instance = new self( $address, $subject, $body );
		}
		return self::$_instance;
	}
	
	public function sendMail()
	{
		try {
			//Server settings
			//        $this->mail->SMTPDebug = 2;                               // Enable verbose debug output
			$this->mail->isSMTP();                                      // Set mailer to use SMTP
			$this->mail->Host = 'smtp.aliyun.com';  //  Specify main and backup SMTP servers
			$this->mail->SMTPAuth = true;                               // Enable SMTP authentication
			$this->mail->CharSet = 'UTF-8';         //set charset
			$this->mail->Username = 'ssl312785362@aliyun.com';                 // SMTP username
			$this->mail->Password = 'cqsjhzx201315';                           // SMTP password
			//        $this->mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			//        $this->mail->Port = 587;                                    // TCP port to connect to
			
			//Recipients
			$this->mail->setFrom( 'ssl312785362@aliyun.com', 'simon' );
			$this->mail->addAddress( $this->address );     // Add a recipient
			//        $this->mail->addAddress('ellen@example.com');               // Name is optional
			$this->mail->addReplyTo( 'ssl312785362@aliyun.com', 'simon' );
			//        $this->mail->addCC('cc@example.com');
			//        $this->mail->addBCC('bcc@example.com');
			
			//Attachments
			//			$this->mail->addAttachment( '/var/tmp/file.tar.gz' );         // Add attachments
			//			$this->mail->addAttachment( '/tmp/image.jpg', 'new.jpg' );    // Optional name
			
			//Content
			$this->mail->isHTML( true );                                  // Set email format to HTML
			$this->mail->Subject = $this->subject;
			$this->mail->Body = $this->body;
			//        $this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
			
			$this->mail->send();
			return true;
		} catch ( Exception $e ) {
			return \common::output_error( $this->mail->ErrorInfo );
		}
	}
}
