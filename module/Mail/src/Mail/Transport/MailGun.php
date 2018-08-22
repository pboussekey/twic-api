<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Mail\Transport;

use Zend\Mail\Message;
use Zend\Mail\Address\AddressInterface;
use Zend\Mail\Transport\TransportInterface;
use Mailgun\Mailgun as LibMailgun;
use Zend\Mime\Mime;

/**
 * MailGun connection object
 *
 */
class MailGun implements TransportInterface
{

    protected $key;
    protected $domaine;

    /**
     * 
     * @param string $key
     * @param string $domaine
     */
    public function __construct($key, $domaine)
    {
        $this->key = $key;
        $this->domaine = $domaine;
    }
    
    /**
     * Send an email via the MailGun connection protocol
     *
     * The connection via the protocol adapter is made just-in-time to allow a
     * developer to add a custom adapter if required before mail is sent.
     *
     * @param Message $message
     */
    public function send(Message $message)
    {
        // Prepare message
        $from       = $this->prepareFromAddress($message);
        $recipients = $this->prepareRecipients($message);
        $headers    = $this->prepareHeaders($message);
        
        $i=0;
        /** @var \Zend\Mime\Message **/
        $minemessage = $message->getBody();
        $arrayPart = [];
        $parts = [];
        $attachments = [];
        $inlines = [];
        
        $send = [
            'to' => $recipients,
            'from' => $from,
            'subject' => $message->getSubject(),
        ];

        if(!is_string($minemessage)) {
            foreach ($minemessage->getParts() as $part ) {
                if($part->getDisposition() === Mime::DISPOSITION_ATTACHMENT ) {
                    $attachments[] = $part;
                    continue;
                } 
                if($part->getDisposition() === Mime::DISPOSITION_INLINE ) {
                    $inlines[] = $part;
                    continue;
                }
                
                $parts[$part->getType()][] = $part->getContent();
            }
            
            
            if(isset($parts[Mime::TYPE_HTML])) {
                if(count($parts[Mime::TYPE_HTML]) === 1) {
                    $send['html'] = current($parts[Mime::TYPE_HTML]);
                } else if($parts[Mime::TYPE_HTML] > 1) {
                    //@todo trop part html
                }
            }
            if(isset($parts[Mime::TYPE_TEXT])) {
                if(count($parts[Mime::TYPE_TEXT]) === 1) {
                    $send['text'] = current($parts[Mime::TYPE_HTML]);
                } else if($parts[Mime::TYPE_TEXT] > 1) {
                    //@todo trop part text
                }
            }
            
            foreach ($attachments as $attachment) {
                $send['attachment'][] = [
                    'filename' => $attachment->getFileName(),
                    'fileContent' => $attachment->getRawContent(),
                ];
            }
            foreach ($inlines as $inline) {
                $send['inline'][] = [
                    'filename' => $inline->getFileName(),
                    'fileContent' => $inline->getRawContent(),
                ];
            }
        } else {
            $send['html'] = $minemessage;
        }
        
        $mgClient = LibMailgun::create($this->key);
        $result = $mgClient->messages()->send($this->domaine,$send);
    }
    
    
    /**
     * Retrieve email address for envelope FROM
     *
     * @param  Message$ $message
     * @return string
     */
    protected function prepareFromAddress(Message $message)
    {
        $sender = $message->getSender();
        if ($sender instanceof AddressInterface) {
            return $sender->getEmail();
        }
        
        $from = $message->getFrom();
        if (! count($from)) {
            // Per RFC 2822 3.6
            throw new \Exception(sprintf(
                '%s transport expects either a Sender or at least one From address in the Message; none provided',
                __CLASS__
                ));
        }
        
        $from->rewind();
        $sender = $from->current();
        return $sender->getEmail();
    }
    
    /**
     * Prepare array of email address recipients
     *
     * @param  Message $message
     * @return array
     */
    protected function prepareRecipients(Message $message)
    {
        $recipients = [];
        foreach ($message->getTo() as $address) {
            $recipients[] = $address->getEmail();
        }
        foreach ($message->getCc() as $address) {
            $recipients[] = $address->getEmail();
        }
        foreach ($message->getBcc() as $address) {
            $recipients[] = $address->getEmail();
        }
        
        $recipients = array_unique($recipients);
        return $recipients;
    }
    
    /**
     * Prepare header string from message
     *
     * @param  Message $message
     * @return string
     */
    protected function prepareHeaders(Message $message)
    {
        $headers = clone $message->getHeaders();
        $headers->removeHeader('Bcc');
        return $headers->toString();
    }
    
    /**
     * Prepare body string from message
     *
     * @param  Message $message
     * @return string
     */
    protected function prepareBody(Message $message)
    {
        return $message->getBodyText();
    }

}
