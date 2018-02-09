<?php

namespace Box\Service;

use Zend\Http\Response;
use JRpc\Json\Server\Exception\JrpcException;

abstract class AbstractApi
{
    protected $conf;

    /**
     * @var \Zend\http\Client
     */
    protected $http_client;
    protected $api_key;

    public function __construct(\Zend\Http\Client $client, $apikey, $conf)
    {
        $this->api_key = $apikey;
        $this->http_client = $client;
        $this->conf = $conf;
        $this->http_client->getRequest()->getHeaders()->addHeaderLine('Authorization', sprintf('Bearer %s', $this->api_key));
    }

    public function setUri($uri)
    {
        $this->http_client->getRequest()->setUri($uri);
    }
    
    public function setMethod($method)
    {
        $this->http_client->setMethod($method);
    }

    public function setPath($path)
    {
        $this->http_client->getUri()->setPath($this->path.$path);
    }
    
    public function setContent($content)
    {
        $this->http_client->getRequest()->setContent($content);
    }

    public function setPost($post)
    {
        $this->http_client->getRequest()->getPost()->fromArray($post);
    }

    public function send()
    {
        $response = $this->http_client->send();
          if (!$response->isSuccess()) {
            
              print_r($this->http_client->getRequest()->toString());
              
              print_r($response->getStatusCode());
            //$this->handleException($response->getReasonPhrase());
        }

        return $response;
    }

    public function getBody($response)
    {
        $data = json_decode($response->getBody(), true);

        return  (is_array($data)) ? $data : [];
    }

    private function handleException(Response $resp)
    {
        if ($resp->isClientError()) {
            if ($resp->getStatusCode() === 403) {
                throw new \Exception(
                    $resp->getStatusCode()
                );
            } else {
                throw new JrpcException(
                    $resp->getBody(),
                    $resp->getStatusCode()
                );
            }
        } elseif ($resp->isServerError()) {
            throw new \Exception(
                'The API server responded with an error: '.$resp->getContent().' Code: '.$resp->getStatusCode(),
                $resp->getStatusCode()
            );
        } else {
            throw new \Exception('An unexpected error occurred:'.$resp->getReasonPhrase());
        }
    }
}
