<?php
/**
 * Library
 */
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Mail\Service\Mail as MailService;
use Application\Service\Event as EventService;
use Zend\Mail\Message;

/**
 * Plugin Mail
 */
class Mail extends AbstractPlugin
{

    protected $mail;
    protected $event;

    /**
     */
    public function __construct(MailService $mail, EventService $event)
    {
        $this->mail = $mail;
        $this->event = $event;
    }

    public function send($to, $body, $subject, $from, $from_name = null)
    {
        $mess = new Message();
        $mess->setSubject($subject)
             ->setBody($body)
             ->setFrom($from, $from_name)
             ->setTo($to);

        return $this->mail->send($mess);
    }

    public function sendRecapEmail($users)
    {
        return $this->event->sendRecapEmail($users);
    }

}
