<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/*
http://youtube.com/watch?v=iwGFalTRHDA
http://www.youtube.com/watch?v=iwGFalTRHDA&feature=related4
http://youtu.be/n17B_uFF4cA
http://www.youtube.com/embed/watch?feature=player_embedded&v=r5nB9u4jjy4
http://www.youtube.com/watch?v=t-ZRX8984sc
http://youtu.be/synufRvV09M
*/
/*
 * get youtube id form url
 * */
function get_youtube_id_from_url($url)
{
    preg_match_all("#(?<=v=|v\/|vi=|vi\/|youtu.be\/)[a-zA-Z0-9_-]{11}#",
        $url, $matches);

    return $matches[0][0];
}
//echo @get_youtube_id_from_url('http://youtu.be/n17B_uFF4cA');

function getRandomCode($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }

    return $randomString;
}

function sendEmail($name, $to, $subject, $message){
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/Exception.php';
    require 'PHPMailer/SMTP.php';

    $emailFrom = 'verify@divyakalash.in';
    $emailFromName = 'Divya Kalash';
    $mail = new PHPMailer;
    //From email address and name
    $mail->From = $emailFrom;
    $mail->FromName = $emailFromName;
    
    //To address and name
    $mail->addAddress($to, $name);
    //$mail->addAddress("recepient1@example.com"); //Recipient name is optional
    
    //Address to which recipient will reply
    $mail->addReplyTo($emailFrom, "Reply");
    
    //CC and BCC
    //$mail->addCC("cc@example.com");
    //$mail->addBCC("bcc@example.com");
    
    //Send HTML or Plain Text email
    $mail->isHTML(true);
    
    $mail->Subject = "Subject Text";
    $mail->Body = "<i>Mail body in HTML</i>";
    $mail->AltBody = "This is the plain text version of the email content";
    if($mail->send()){
        //echo "mail sent";
        return true;
    }else{
        //echo "Mailer Error: " . $mail->ErrorInfo;
        echo false;
    }
}