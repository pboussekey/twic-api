<?php

ini_set('date.timezone', "Europe/Paris");
error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

/**
 * Test bootstrap, for setting up autoloading
 */
class bootstrap
{
    public static function init()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        
        /*if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }*/
        
        //system('phing init-conf');
        static::initAutoloader();
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');
        $loader = include $vendorPath.'/autoload.php';
        
        Zend\Loader\AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true,
                'namespaces' => array(
                    'ModuleTest' => __DIR__ . '/Module',
                    'JsonRpcTest' => __DIR__ . '/JsonRpcClient',
                    'JrpcMock' => __DIR__ . '/JrpcMock')
            )
        ));
    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (! is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }
        
        return $dir . '/' . $path;
    }
}

Bootstrap::init();
