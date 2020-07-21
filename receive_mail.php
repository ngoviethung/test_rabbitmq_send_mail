<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;


$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('send-mail', false, false, false, false);

echo "Hung Waiting for email...";

$callback = function ($msg) {

    echo " * Message received", "\n";
    $data = json_decode($msg->body, true);

    $from = $data['from'];
    $from_email = $data['from_email'];
    $to_email = $data['to_email'];
    $subject = $data['subject'];
    $content = $data['message'];

    $transporter = new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls');

    $transporter->setUsername('secretphotos.sm@gmail.com');
    $transporter->setPassword('sm12345690');

    $mailer = new Swift_Mailer($transporter);

    $message = new Swift_Message($transporter);

    $message->setSubject($subject)
        ->setFrom($from_email)
        ->setTo($to_email)
        ->setBody($content);

    $mailer->send($message);

    echo "Message was sent", "\n";


};

$channel->basic_consume('send-mail', '', false, true, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();