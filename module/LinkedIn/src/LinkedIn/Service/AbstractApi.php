<?php

namespace LinkedIn\Service;

use Zend\Http\Response;
use JRpc\Json\Server\Exception\JrpcException;

abstract class AbstractApi
{
    protected $path;
    protected $api_url;
    protected $access_token;
    
    protected $client_id;
    protected $client_secret;
    protected $redirect_uri;

    /**
     * @var \Zend\Http\Client
     */
    protected $http_client;

    public function __construct(\Zend\Http\Client $client, $client_id, $client_secret, $api_url, $redirect_uri)
    {
        $this->http_client = $client;
        $this->api_url = $api_url;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
    }

    public function _init()
    {
        $this->http_client->getRequest()->setUri($this->api_url);
        $this->path = $this->http_client->getUri()->getPath();
        $this->http_client->getRequest()->getHeaders()->clearHeaders();
        $this->http_client->getRequest()->getHeaders()->addHeaderLine('Connection', 'Keep-Alive');
        $this->http_client->getRequest()->getHeaders()->addHeaderLine('Authorization', sprintf('Bearer %s', $this->access_token));
    }
    
    public function setMethode($methode)
    {
        $this->http_client->setMethod($methode);
    }

    public function setPath($path)
    {
        $this->http_client->getUri()->setPath($this->path.$path);
    }

    public function setParams($params)
    {
        $this->http_client->getRequest()->setContent(json_encode($params));
    }

    public function setPost($post)
    {
        foreach ($post as $k => $v) {
            $this->http_client->getRequest()->getPost()->set($k, $v);
        }
    }
    
    public function send()
    {
        $response = $this->http_client->send();
        if (!$response->isSuccess()) {
            $this->handleException($response);
        }

        return $response;
    }

    public function getBody($response)
    {
        $data = json_decode($response->getBody(), true);

        return  (is_array($data)) ? $data : [];
    }

    public function printRequest()
    {
        echo $this->http_client->getRequest()->toString();
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
                'The API server responded with an error: '.$resp->getReasonPhrase().' Code: '.$resp->getStatusCode(),
                $resp->getStatusCode()
            );
        } else {
            throw new \Exception('An unexpected error occurred:'.$resp->getReasonPhrase());
        }
    }
}
