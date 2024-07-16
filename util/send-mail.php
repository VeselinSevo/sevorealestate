<?php
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($to, $subject, $body) {
    try {
        $mail = new PHPMailer(true);

        // Configurations
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'veselinsevo@gmail.com';
        $mail->Password = 'qtjs pndj puic rifv';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Set email parameters
        $mail->setFrom('veselinsevo@gmail.com', 'Veselin Sevo from Sevo Real Estate');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        // Send the email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
