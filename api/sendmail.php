<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';


function getData()
{
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    return $data !== null;
}

function sendMail($qrImage)
{

    try {

        // Mail Object
        $mail = new PHPMailer(true);

        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = 'deus.varus@gmail.com';                     //SMTP username
        $mail->Password = 'zlvqobhifqbpolnv';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('deus.varus@gmail.com', 'Mailer');
        $mail->addAddress($qrImage['recepient'], 'Recepient');     //Add a recipient
        $mail->addReplyTo('deus.varus@gmail.com', 'Information');

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name


        // Convert the base64 string back to binary
        list($type, $data) = explode(';', $qrImage['attachment']); // Separate MIME type and data
        list(, $data) = explode(',', string: $data); // Remove base64 encoding

        // Decode the base64 string into binary data
        $imageData = base64_decode($data);

        // Attach the image as a string

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'EVENT REGISTRATION';
        $mail->Body = $qrImage['message'];
        $mail->addStringAttachment($imageData, 'qrcode.png', 'base64', 'image/png');
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();

        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";

    }


}





if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (getData() == true) {
        // Directly echo the result of isset() check
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        // echo isset($data['message']) ? sendMail() : 'Message not set';
        echo isset($data['message']) ? sendMail($data) : 'Message not set';
    }
}

?>