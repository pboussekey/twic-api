<?php

namespace Mail;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Mail\Service\Mail;
use Zend\Mail\Storage\Imap;
use Zend\Mail\Transport\Factory;
use Mail\Transport\MailGun;

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
                'mailgun' => function($container) {
                    $mailgunConf = $container->get('config')['mail-conf']['transport']['options'];
                    return new MailGun($mailgunConf['key'], $mailgunConf['domaine']);
                },
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
                    case 'gs':
                        $bj_storage = new \Mail\Template\Storage\GsStorage();
                        if (isset($conf_mail['cache'])) {
                            $bj_storage->setCache($container->get($conf_mail['cache']));
                        }
                        $bj_storage->init($conf_storage);
                        break;
                    }

                    /*if (null !== $login) {
                        $this->options['transport']['options']['connection_config']['username'] = $login;
                        $this->options['storage']['user'] = $login;
                    }
                    if (null !== $password) {
                        $this->options['transport']['options']['connection_config']['username'] = $password;
                        $this->options['storage']['password'] = $password;
                    }*/
                    
                    $mail =  new Mail();
                    $mail->setTplStorage($bj_storage)
                        ->setOptions($conf_mail);
                    
                    if ($conf_mail['storage']['active'] === true) {
                        $mail->setStorage(new Imap($conf_mail['storage']));
                    }
                    
                    if ($conf_mail['transport']['active'] === true) {
                        $transport = ($container->has($conf_mail['transport']['type'])) ?
                            $container->get($conf_mail['transport']['type']) :
                            Factory::create($conf_mail['transport']);
                       
                        $mail->setTransport($transport);
                    }

                    return $mail;
                },
            ],

        );
    }
}
