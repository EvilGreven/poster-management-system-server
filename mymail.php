<?	//Purpose:		This function sends email for whatever need
	//Original Author:	Brett Jones
	//Creation Date:	12/1/2013

//we're using PHPMailer from https://github.com/Synchro/PHPMailer
require_once('class.phpmailer.php');

//sends an Email through a Google account
//$address is the email address to send to
//$name is the addressee
//$subject is the subject of the email
//$body is the body of the email
function SendEmail($address, $name, $subject, $body)
{	//start a new instance
	$mail			= new PHPMailer();

	//initialize the class
	$mail->IsSMTP();
	$mail->SMTPAuth		= true;
	$mail->SMTPSecure	= "tls";
	$mail->Username		= "some_email@gmail.com";
	$mail->Password		= "some_password";          
	$mail->Host		= "smtp.gmail.com";
	$mail->Port		= 587;           

	//tell who is sending the message
	$mail->SetFrom('some_email@gmail.com', 'Poster Management System');

	//add the subject and body
	$mail->Subject		= $subject;
	$mail->Body		= $body;

	//finally, add the address and name
	$mail->AddAddress($address, $name);

	//now send it!
	$mail->Send();    
}
?>
