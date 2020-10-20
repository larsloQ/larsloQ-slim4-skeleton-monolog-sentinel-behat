<?php
/* setup mailer */
namespace App\Services;

use SwiftMailer;

class Mailer
{
    protected $mailer;
    protected $message;
    public function __construct($settings)
    {
        $transport = new \Swift_SmtpTransport($settings['smtp']['host'], $settings['smtp']['port'], $settings['smtp']['secure']);
        $transport->setUsername($settings['smtp']['username']);
        $transport->setPassword($settings['smtp']['password']);
        $this->mailer = new \Swift_Mailer($transport);
        $this->message = new \Swift_Message;
        $this->message->setSubject("empty");
        $this->message->setFrom($settings['from']);
        $this->message->setTo($settings['to']);
        $this->message->setBody("empty");
        $this->message->setContentType("text/html");
    }

    public function sendmail($subject, $body)
    {
        $this->message->setSubject($subject);
        $this->message->setBody($body);
        $results = $this->mailer->send($this->message);
    }
}
