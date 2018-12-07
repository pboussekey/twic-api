<?php

/**
 * Global Configuration Override.
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [
    'version' => "2.0.4",
    'build-commit' => 1331,
    'app-conf' => [
        'cache' => 'storage_memcached',
    ],
    'dms-conf' => [
        'size_allowed' => [
            ['width' => 300, 'height' => 200],
            ['width' => 300, 'height' => 300],
            ['width' => 150, 'height' => 100],
            ['width' => 80, 'height' => 80],
            ['width' => 300],
        ],
        'check_size_allowed' => false,
        'default_path' => 'upload/',
        'adapter' => 'http-adapter',
        'convert' => [
                'tmp' => '/tmp/',
        ],
        'headers'=>[
            'Access-Control-Allow-Credentials' => 'false',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'PUT, GET, POST, OPTIONS, DELETE',
            'Access-Control-Allow-Headers' => 'Authorization, X-auth-token, Origin, X-Requested-With, Content-Type, Accept',
            'Access-Control-Max-Age' => '600',
        ],
    ],
    'dal-conf' => [
        'adapter' => 'db-adapter',
        'cache' => 'storage_memcached',
        'log' => 'log-system',
        'namespace' => [
            'app' => [
                'service' => 'Application\\Service',
                'mapper' => 'Application\\Mapper',
                'model' => 'Application\\Model',
            ],
        ],
    ],
    'rbac-conf' => [
        'cache' => [
            'name' => 'storage_memcached',
            'enable' => true,
        ],
    ],
    'json-rpc-server' => [
        'cache' => 'storage_memcached',
        'log' => 'log-system',
        'environment' => 'dev', /* dev|prod */
        'persistence' => true,
        'services' => [
            'app_service_user',
            'app_service_role',
            'app_service_conversation',
            'app_service_conversation_user',
            'app_service_contact',
            'app_service_activity',
            'app_service_circle',
            'app_service_report',
            'app_service_item',
            'app_service_resume',
            'app_service_message',
            'app_service_library',
            'app_service_language',
            'app_service_page',
            'app_service_page_relation',
            'app_service_page_user',
            'app_service_page_doc',
            'app_service_post',
            'app_service_post_doc',
            'app_service_post_like',
            'app_service_submission',
            'app_service_quiz',
            'app_service_group',
            'app_service_preregistration',
            'app_service_video_archive',
            'app_service_tag',
            'app_service_event',
            'app_service_page_program_user',
            'app_service_page_program',
            'mail.service',
            'rbac_service_permission',
            'rbac_service_role_permission',
            'rbac_service_role_relation',
            'Application\Service\Saml',
            [
                'class' => 'addr_service_address',
                'methods' => [
                    'getAddress',
                ],
            ],
            [
                'class' => 'addr_service_country',
                'methods' => [
                    'getList',
                    'getCountry',
                    'getCountryById'
                ],
             ],
            [
                'class' => 'addr_service_city',
                'methods' => [
                    'getList',
                    'getCity',
                ],
            ],
            [
                'class' => 'addr_service_division',
                'methods' => [
                    'getList',
                    'getDivision',
                ],
            ],
        ],
        'headers' =>
        [
            'Access-Control-Allow-Credentials' => 'false',
            'Access-Control-Allow-Methods' => 'PUT, GET, POST, OPTIONS, DELETE',
            'Access-Control-Allow-Headers' => 'Authorization, X-auth-token, Origin, X-Requested-With, Content-Type, Accept',
            'Access-Control-Max-Age' => '600',
        ],
    ],
    'zopentok-conf' => [
        'expire_time' => 60 * 60 * 24 * 30,
        'adapter' => 'http-adapter',
    ],
    'caches' => [
        'storage_memcached' => [
            'adapter' => [
                'name' => 'memcached',
                'options' => [
                    'namespace' => 'LMS1331',
                    'lib_options' => [
                        ['option' => Memcached::OPT_PREFIX_KEY, 'value' => 'LMS1331'],
                    ],
                ],
            ],
            'plugins' => [
                'exception_handler' => ['throw_exceptions' => true],
            ],
        ],
    ],
];
