<?php


namespace App;

use Core\Error;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class for sending emails
 */
class Mail
{

    /**
     * Send an email
     *
     * @param string $to
     * @param string $subject
     * @param string $html_body
     * @param string $plaintext_body
     * @return bool
     */
    public static function send(string $to, string $subject, string $html_body, string $plaintext_body): bool
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = Config::SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = Config::SMTP_USERNAME;
            $mail->Password = Config::SMTP_PASSWORD;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom(Config::SENDER_ADDRESS, Config::SENDER_NAME);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html_body;
            $mail->AltBody = $plaintext_body;

            $mail->send();
            return true;
        } catch (Exception $e){
            Error::exceptionHandler($e, true);
            return false;
        }

    }
}