<?php

namespace App\Domain\Mail\Service;

use Swift_Mailer;

class MailService
{

    /**
     * @param Swift_Mailer $mailer
     */
    public function __construct(private Swift_Mailer $mailer)
    {
    }

    public function sendMessage($email, $message)
    {
        $date = new \DateTime;
        $message = (new \Swift_Message('Stock result'))
            ->setFrom(['john@doe.com' => 'John Doe'])
            ->setTo([$email])
            ->setBody('Here is the result of you search:<br>', 'text/html')
            ->addPart("Symbol: {$message['symbol']}</br>", 'text/html')
            ->addPart("Name: {$message['name']}</br>", 'text/html')
            ->addPart("Open: {$message['open']}</br>", 'text/html')
            ->addPart("High: {$message['high']}</br>", 'text/html')
            ->addPart("Low: {$message['low']}</br>", 'text/html')
            ->addPart("Date of the request: {$date->format('Y-m-d H:m')}</br>", 'text/html');

        return $this->mailer->send($message);
    }

}
