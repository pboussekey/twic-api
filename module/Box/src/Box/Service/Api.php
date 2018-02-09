<?php

namespace Box\Service;

use Zend\Http\Request;
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
     * @param string $name
     * @param string $type
     * @return \Zend\Http\Response
     */
    public function addFile($url, $name, $type = null)
    {
        $this->setMethod(Request::METHOD_POST);
        $this->setUri("https://upload.box.com/api/2.0/files/content");
        
        $boundary = uniqid();
        $data = $this->buildMultiPartRequest(
            $boundary,
            ['attributes' =>  json_encode(['name' => $name,'parent' => ['id' => '0']])],
            ['file' => file_get_contents($url)]
        );
        
        $this->http_client->getRequest()->getHeaders()->addHeaderLine('Content-Type: multipart/form-data; boundary=-------------' . $boundary);
        $this->http_client->getRequest()->getHeaders()->addHeaderLine('Content-Length: ' . strlen($data));
        $this->setContent($data);
        $rep = $this->send();
        $json = json_decode($rep->getContent(), 1);
        
        return current($json['entries'])['id'];
    }

    private function buildMultiPartRequest($boundary, $fields, $files) 
    {
        $delimiter = '-------------' . $boundary;
        $data = '';
        foreach ($fields as $name => $content) {
            $data .= "--" . $delimiter . "\r\n" . 'Content-Disposition: form-data; name="' . $name . "\"\r\n\r\n" . $content . "\r\n";
        }
        foreach ($files as $name => $content) {
            $data .= "--" . $delimiter . "\r\n" . 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $name . '"' . "\r\n\r\n" . $content . "\r\n";
        }
        
        $data .= "--" . $delimiter . "--\r\n";
        
        return $data;
    }
    
    /**
     * @param int $document_id
     * @param int $duration
     *
     * @return array
     */
    public function createSession($document_id, $duration = 60)
    {
        $this->setMethod(Request::METHOD_POST);
        $this->setUri("https://api.box.com/oauth2/token");
        $this->setPost([
            'subject_token' => $this->api_key, 
            'subject_token_type' => 'urn:ietf:params:oauth:token-type:access_token',
            'scope' => 'item_preview item_upload',
            'resource' => 'https://api.box.com/2.0/files/'.$document_id,
            'grant_type' => 'urn:ietf:params:oauth:grant-type:token-exchange',
        ]);

        $rep = $this->send();

        return json_decode($rep->getBody(), 1);
    }
}
