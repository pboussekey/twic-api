<?php

namespace Box\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\Http\Response;
use JRpc\Json\Server\Exception\JrpcException;

abstract class AbstractApi
{
    /**
     * @var ServiceManager
     */
    protected $servicemanager;
    protected $path;

    /**
     * @var \Zend\http\Client
     */
    protected $http_client;

    public function __construct(\Zend\Http\Client $client, $apikey, $url)
    {
        $this->http_client = $client;
        $this->http_client->getRequest()->setUri($url);
        $this->path = $this->http_client->getUri()->getPath();
        $this->http_client->getRequest()->getHeaders()->addHeaderLine('Authorization', sprintf('Token %s', $apikey));
        $this->http_client->getRequest()->getHeaders()->addHeaderLine('Content-Type', 'application/json');
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

        return  (is_array($data)) ? $data : array();
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
