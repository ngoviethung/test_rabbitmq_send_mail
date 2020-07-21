<?php


require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


class A{
    public function send(){

        $connection = new AMQPStreamConnection('localhost', 5672, 'admin', '1viethung');
        $channel = $connection->channel();

        $channel->queue_declare('send-mail', false, false, false, false);

        $from = 'Hung';
        $from_email = 'secretphotos.sm@gmail.com';
        $to_email = 'trucxanh.mobi@gmail.com';
        $subject = 'Test';
        $content = 'This is test!';

        $arr = [
            'from' => $from,
            'from_email' => $from_email,
            'sub' => $subject,
            'content' => $content
        ];

        $data = json_encode($arr);

        $msg = new AMQPMessage($data);

        $channel->basic_publish($msg, '', 'send-mail');

        echo " Hung Sent \n";

        $channel->close();
        $connection->close();
    }
}

class B{
     public function send(){
         $from = 'Hung';
         $from_email = 'secretphotos.sm@gmail.com';
         $to_email = 'trucxanh.mobi@gmail.com';
         $subject = 'Test';
         $content = 'This is test!';

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
     }
}

$a = new A(); $a->send();

//$b = new B(); $b->send();


