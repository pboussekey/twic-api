<?php

namespace Box\Service;

use Zend\Http\Request;
use Box\Model\Document;
use Box\Model\Session;
use JRpc\Json\Server\Exception\JrpcException;

class Api extends AbstractApi
{
    protected $allow_type = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
        'application/x-python',
        'text/x-python',
        'text/javascript',
        'application/x-javascript',
        'application/javascript',
        'text/xml',
        'application/xml',
        'text/css',
        'text/x-markdown',
        'text/x-script.perl',
        'text/x-c',
        'text/x-m',
        'application/json',
    ];

    /**
     * @param string $url
     *
     * @return \Box\Model\Document
     */
    public function addFile($url, $type)
    {
        if (!in_array($type, $this->allow_type)) {
            return;
        }
        $this->setMethode(Request::METHOD_POST);
        $this->setPath(sprintf('/documents'));
        $this->setParams(['url' => $url]);

        return new Document($this->getBody($this->send()));
    }

    /**
     * @param int $document_id
     * @param int $duration
     *
     * @return \Box\Model\Session
     */
    public function createSession($document_id, $duration = 60)
    {
        $this->setMethode(Request::METHOD_POST);
        $this->setPath(sprintf('/sessions'));
        $this->setParams(['document_id' => $document_id, 'duration' => $duration]);

        $rep = $this->send();

        if ($rep->getStatusCode() === 202) {
            throw new JrpcException($rep->getHeaders()->get('Retry-After'), 202);
        }

        return new Session($this->getBody($rep));
    }
}
