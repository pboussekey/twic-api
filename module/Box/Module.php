<?php

namespace Box;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Box\Service\Api;
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
                'box.service' => \Box\Service\Api::class,
            ],
            'factories' => [
                \Box\Service\Api::class => function ($container) {
                    $box = $container->get('config')['box-conf'];
                    $client = new Client();
                    $client->setOptions($container->get('config')[$box['adapter']]);

                    return new Api($client, $box['apikey'], $box['url']);
                },
            ],
        ];
    }
}
