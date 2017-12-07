<?php

$return = array_replace_recursive(
    include __DIR__ . '/../../config/application.config.php',
    array('module_listener_options' =>
        array('config_cache_enabled' => false,
              'config_cache_key' => 'cache-configuration',
                      'module_map_cache_enabled' => false,
                      'module_map_cache_key' =>
                      'cache-module',
                      'cache_dir' => 'data/cache',
                      'config_static_paths' => array(
                              __DIR__ . '/../../config/autoload/global.php',
                              __DIR__ . '/autoload/local.php',
                      ),
        ),
    )
);

$return['module_listener_options']['config_glob_paths'] = array();

return $return;
