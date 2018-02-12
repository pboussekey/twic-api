<?php

return array(
    'http-adapter' => array(
        'adapter' => 'Zend\Http\Client\Adapter\Curl',
        'maxredirects' => 5,
        'sslverifypeer' => false,
        'ssltransport' => 'tls',
        'timeout' => 30,
    ),
    'box-conf' => array(
        'apikey' => '**********',
        'url' => ' https://upload.box.com/api/2.0/files/content',
        'adapter' => 'http-adapter',
    ),
);
