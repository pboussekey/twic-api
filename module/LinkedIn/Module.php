<?php

namespace LinkedIn;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use LinkedIn\Service\Api;
use Zend\Http\Client;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'aliases' => [
                'linkedin.service' => \LinkedIn\Service\Api::class,
            ],
            'factories' => [
                \LinkedIn\Service\Api::class => function ($container) {
                    $linkedin = $container->get('config')['linkedin-conf'];
                   
                    $client = new Client();
                    $client->setOptions($container->get('config')[$linkedin['adapter']]);

                    return new Api($client, $linkedin['client_id'], $linkedin['client_secret'], $linkedin['api_url'], $linkedin['redirect_uri']);
                },
            ],
        ];
    }
}
