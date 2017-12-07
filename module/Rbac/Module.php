<?php

namespace Rbac;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'aliases' => array(
                'rbac.service' => 'Rbac\Service\Rbac',
            ),
            'factories' => array(
                'Rbac\Service\Rbac' => function ($container) {
                    $cache = $container->get('config')['rbac-conf']['cache'];
                    
                    return new \Rbac\Service\Rbac($container->get('rbac_service_role'), $container->get($cache['name']));
                },
            ),
        );
    }
}
