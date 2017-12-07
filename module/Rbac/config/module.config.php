<?php

return array(
    'dal-conf' => array(
        'namespace' => array(
            'rbac' => array(
                'service' => 'Rbac\\Db\\Service',
                'mapper' => 'Rbac\\Db\\Mapper',
                'model' => 'Rbac\\Db\\Model',
            ),
        ),
    ),
    'rbac-conf' => array(
        'cache' => array(
            'name' => 'storage_memcached',
            'enable' => true,
        ),
    ),
);
