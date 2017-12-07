<?php

namespace Auth;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Auth\Authentication\Storage\CacheStorage;
use Auth\Authentication\Storage\CacheBddStorage;

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
                'auth.service' => \Zend\Authentication\AuthenticationService::class,
                'token.storage.mem' => \Auth\Authentication\Storage\CacheStorage::class,
                'token.storage.bddmem' => \Auth\Authentication\Storage\CacheBddStorage::class,
            ],
            'factories' => [
                \Zend\Authentication\AuthenticationService::class => function ($container) {
                    $conf = $container->get('Config')['auth-conf'];

                    return new \Zend\Authentication\AuthenticationService(
                        $container->get($conf['storage']['name']),
                        new Authentication\Adapter\DbAdapter(
                            $container->get($conf['adapter']['name']),
                            $conf['adapter']['options']['table'],
                            $conf['adapter']['options']['identity'],
                            $conf['adapter']['options']['credential'],
                            $conf['adapter']['options']['hash']
                        )
                    );
                },
                \Auth\Authentication\Storage\CacheStorage::class => function ($container) {
                    $authconf = $container->get('Config')['auth-conf'];
                    $storage = new CacheStorage($container->get($authconf['storage']['options']['adpater']));
                    $storage->setRequest($container->get('Request'));
                    
                    return $storage;
                },
                \Auth\Authentication\Storage\CacheBddStorage::class => function ($container) {
                    $authconf = $container->get('Config')['auth-conf'];
                    $storage = new CacheBddStorage(
                        $container->get($authconf['storage']['options']['bdd_adpater']),
                    $container->get($authconf['storage']['options']['adpater'])
                    );
                    $storage->setRequest($container->get('Request'));
                
                    return $storage;
                },
            ],
        ];
    }
}
