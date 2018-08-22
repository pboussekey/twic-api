<?php
/**
 * Library
 */
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Mail\Service\Mail as MailService;
use Zend\Mail\Message;

/**
 * Plugin Mail
 */
class Mail extends AbstractPlugin
{

    protected $mail;

    /**
     */
    public function __construct(MailService $mail)
    {
        $this->mail = $mail;
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
}
