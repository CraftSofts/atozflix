<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require(__DIR__.'/vendor/autoload.php');

function email($to,$name,$subject,$body){
// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

//Server settings
$mail->SMTPDebug = 0;  // Enable verbose debug output
$mail->isSMTP();// Send using SMTP
$mail->Host   = 'server61.web-hosting.com';// Set the SMTP server to send through
$mail->SMTPAuth   = true;   // Enable SMTP authentication
$mail->Username   = 'contact@atozflix.com'; // SMTP username
$mail->Password   = 'password';   // SMTP password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
$mail->Port   = 587;// TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

//Recipients
$mail->setFrom('contact@atozflix.com', 'Admin');
$mail->addAddress($to, $name); // Add a recipient
$mail->addReplyTo('contact@atozflix.com', 'Admin');

// Attachments
/*$mail->addAttachment('/var/tmp/file.tar.gz'); // Add attachments
$mail->addAttachment('/tmp/image.jpg', 'new.jpg');// Optional name*/

// Content
$mail->isHTML(true);  // Set email format to HTML
$mail->Subject = $subject;
$mail->Body= $body;
//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if($mail->send()) {
	return true;
} else {
	echo '<br>Mailer Error: ' . $mail->ErrorInfo;
}
}
