<?php

namespace App\Domain\Queue\Service;

use App\Domain\Mail\Service\MailService;
use PhpAmqpLib\Channel\AMQPChannel;
use Symfony\Component\Console\Output\OutputInterface;

class Consumer
{
    /**
     * @param AMQPChannel $channel
     */
    public function __construct(private AMQPChannel $channel, private MailService $mailService, private OutputInterface $output)
    {
    }

    public function listen()
    {
        $this->output->writeln("Started");

        $this->channel->queue_declare('email', false, true, false, false);
        $this->channel->basic_qos(null, 1, null);

        $this->channel->basic_consume(
            'email',
            '',
            false,
            false,
            false,
            false,
            function ($message) {
                $this->output->writeln("Consuming message: {$message->body}");
                $decodedMessage = json_decode($message->body, true);
                $this->mailService->sendMessage($decodedMessage['email'], $decodedMessage['message']);
                $channel = $message->delivery_info['channel'];
                $channel->basic_ack($message->delivery_info['delivery_tag']);
            }
        );

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
        $this->output->writeln("Done consuming messages!");
        $this->channel->close();
    }
}
