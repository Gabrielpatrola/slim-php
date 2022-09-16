<?php

namespace App\Domain\Queue\Service;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Producer
{
    /**
     * @param AMQPChannel $channel
     */
    public function __construct(private AMQPChannel $channel)
    {
    }

    public function produce($email, $message)
    {
        $queuedMessage = ['email' => $email, 'message' => $message];
        $this->channel->queue_declare('email', false, true, false, false);
        $newMessage = new AMQPMessage(
            json_encode($queuedMessage),
            array('delivery_mode' => 2)
        );

        $this->channel->basic_publish($newMessage, '', 'email');

        $this->channel->close();
    }
}
