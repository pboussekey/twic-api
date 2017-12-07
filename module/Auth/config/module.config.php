<?php

return [
    'auth-conf' => [
        'adapter' => [
            'name' => 'db-adapter',
            'options' => [
                'table' => 'user',
                'identity' => 'email',
                'credential' => 'password',
                'lost' => 'new_password',
                'hash' => 'MD5(?)',
            ],
        ],
        'storage' => [
            'name' => 'token.storage.bddmem',
            'options' => [
                'adpater' => 'storage_memcached',
                'bdd_adpater' => 'db-adapter',
            ],
        ],
    ],
];
