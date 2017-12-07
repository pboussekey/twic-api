<?php

use \Application\Controller\Plugin\ConfFactory;
use \Application\Controller\Plugin\videoArchive;
use \Application\Controller\Plugin\item;
use \Zend\Router\Http\Literal;

/**
 * Zend Framework (http://framework.zend.com/).
 *
 * @link http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 *
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return [
    'controller_plugins' => [
        'factories' => [
            'conf' => ConfFactory::class,
            //'videoArchive' => function (\Interop\Container\ContainerInterface\ContainerInterface $container) {
              'videoArchive' => function ($container) {
                  return new videoArchive($container->get('app_service_video_archive'));
              },    
              'item' => function ($container) {
                  return new item($container->get('app_service_item'));
              }
        ],
    ],
    'router' =>[
        'routes' => [
            /*'home' => array(
                'type' => Literal::class,
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),*/
            'statuschange' => [
                 'type' => Literal::class,
                     'options' => [
                     'route' => '/statuschange',
                     'defaults' => [
                         'controller' => 'Application\Controller\Index',
                         'action' => 'statusChange',
                     ],
                 ],
             ],
            'notify' => [
                 'type' => Literal::class,
                     'options' => [
                     'route' => '/notify',
                     'defaults' => [
                         'controller' => 'Application\Controller\Index',
                         'action' => 'notify',
                     ],
                 ],
             ],
            'version' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/version',
                    'defaults' => [
                        'controller' => 'Application\Controller\Version',
                        'action' => 'index',
                    ],
                ],
            ],
            'confall' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/conf',
                    'defaults' => [
                        'controller' => 'Application\Controller\Version',
                        'action' => 'conf',
                    ],
                ],
            ],
            'info' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/info',
                    'defaults' => [
                        'controller' => 'Application\Controller\Version',
                        'action' => 'info',
                    ],
                ],
            ],
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/application',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ],
        ],
    ],
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'invokables' => array(
            '\Application\Service\Opentok' => '\Application\Service\Opentok',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__.'/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Version' => 'Application\Controller\VersionController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__.'/../view/layout/layout.phtml',
            'application/index/index' => __DIR__.'/../view/application/index/index.phtml',
            'error/404' => __DIR__.'/../view/error/404.phtml',
            'error/index' => __DIR__.'/../view/error/index.phtml',
        ),
        'template_path_stack' => [
            __DIR__.'/../view',
        ],
    ),
    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [],
        ],
    ],
];
