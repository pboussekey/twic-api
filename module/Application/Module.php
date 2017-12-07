<?php

/**
 * Zend Framework (http://framework.zend.com/).
 *
 * @link http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 *
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use JRpc\Json\Server\Exception\JrpcException;
use Rbac\Db\Model\Role;
use Sge\Service\Sge;
use Application\Service\Fcm;

class Module
{
    public function onBootstrap(MvcEvent $event)
    {
        $sge = new Sge();
        $sge->init();
        $eventManager = $event->getApplication()->getEventManager();
        $eventManagerShare = $eventManager->getSharedManager();
        $eventManagerShare->attach(
            'JRpc\Json\Server\Server',
            'sendRequest.pre',
            function ($e) use ($event) {
                $permission = $e->getParams()['methode'];
                $authService = $event->getApplication()
                    ->getServiceManager()
                    ->get('auth.service');
                if ($authService->hasIdentity()) {
                    $identity = $event->getApplication()
                        ->getServiceManager()
                        ->get('app_service_user')
                        ->getIdentity();
                } else {
                    $identity['roles'] = Role::STR_GUEST;
                }
                $rbacService = $event->getApplication()
                    ->getServiceManager()
                    ->get('rbac.service');

                if (! $rbacService->isGranted($identity['roles'], $permission)) {
                    if ($e->getTarget()->getServiceMap()->getService($permission) === false
                    ) {
                        throw new JrpcException('Method not found: ' . $permission, - 32028);
                    }
                    if (! $authService->hasIdentity()) {
                        throw new JrpcException('Not connected: ' . $permission, - 32027);
                    }
                    throw new JrpcException('No authorization: ' . $permission, - 32029);
                }
            }
        );

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'aliases' => [
                'gcm-client' => \ZendService\Google\Gcm\Client::class,
                'fcm' => \Application\Service\Fcm::class,
            ],
            'factories' => [
                \ZendService\Google\Gcm\Client::class => function ($container) {
                    $config = $container->get('config');
                    $client = new \ZendService\Google\Gcm\Client();
                    $client->setApiKey($config['gcm']['api_key'])
                        //->setSenderId($config['gcm']['sender_id'])
                        ->setHttpClient(new \Zend\Http\Client(null, $config[$config['gcm']['adapter']]));

                    return $client;
                },
                \Application\Service\Fcm::class => function ($container) {
                    return new Fcm(
                        $container->get('app_service_session'),
                        $container->get('gcm-client'),
                        $container->get('app_service_user')->getIdentity()['token']
                    );
                }
            ],
        ];
    }
}
