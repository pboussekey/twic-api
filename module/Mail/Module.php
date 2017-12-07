<?php

namespace Mail;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Mail\Service\Mail;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'aliases' => [
                'mail.service' => Mail\Service\Mail::class,
            ],
            'factories' => [
                Mail\Service\Mail::class => function ($container) {
                    $conf_mail = $container->get('config')['mail-conf'];
                    $conf_storage = $conf_mail['template']['storage'];
                    $bj_storage = null;

                    switch ($conf_storage['name']) {
                    case 'fs':
                        $bj_storage = new \Mail\Template\Storage\FsStorage();
                        $bj_storage->init($conf_storage);
                        break;
                    case 's3':
                        $bj_storage = new \Mail\Template\Storage\FsS3Storage();
                        if (isset($conf_mail['cache'])) {
                            $bj_storage->setCache($container->get($conf_mail['cache']));
                        }
                        $bj_storage->init($conf_storage);
                        break;
                    }

                    $mail =  new Mail();
                    $mail->setTplStorage($bj_storage)
                        ->setOptions($conf_mail);

                    return $mail;
                },
            ],

        );
    }
}
