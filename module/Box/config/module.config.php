<?php

return array(
    'http-adapter' => array(
        'adapter' => 'Zend\Http\Client\Adapter\Curl',
        'maxredirects' => 5,
        'sslverifypeer' => false,
        'ssltransport' => 'tls',
        'timeout' => 10,
    ),
    'box-conf' => array(
        'apikey' => 'cxtjsc7gmibtu84caf0grun8thbp2ga1',
        'url' => 'https://view-api.box.com/1',
        'adapter' => 'http-adapter',
    ),
);
