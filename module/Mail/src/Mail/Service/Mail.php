<?php

namespace Mail\Service;

use Mail\Mime\Part;
use Mail\Template\Model\TplModel;
use Zend\Mail\Storage\Imap;
use Zend\Mail\Transport\Factory;
use Zend\Mime\Mime;
use Mail\Mail\Message;
use Mail\Template\Storage\AbstractStorage;

class Mail
{
    protected $storage;
    protected $tpl_storage;
    protected $transport;
    protected $is_init = false;
    protected $options;

    public function init($login = null, $password = null)
    {
        if (null !== $login) {
            $this->options['transport']['options']['connection_config']['username'] = $login;
            $this->options['storage']['user'] = $login;
        }
        if (null !== $password) {
            $this->options['transport']['options']['connection_config']['username'] = $password;
            $this->options['storage']['password'] = $password;
        }
        if ($this->options['storage']['active'] === true) {
            $this->storage = new Imap($this->options['storage']);
        }
        if ($this->options['transport']['active'] === true) {
            $this->transport = Factory::create($this->options['transport']);
        }

        $this->is_init = true;
    }

    /**
     * Create Template mail
     *
     * @invokable
     *
     * @param string $name
     * @param string $from
     * @param string $subject
     * @param string $content
     * @param string $text
     * @param string $from_name
     * @param array  $files
     *
     * @return bool
     */
    public function addTpl($name, $from, $subject, $content, $text = null, $from_name = null, array $files = [])
    {
        $m_tpl = new TplModel();
        $m_tpl->setName($name)
            ->setSubject($subject)
            ->setFrom($from)
            ->setFromName($from_name);

        

        $part_text = new Part($text);
        $part_text->setEncoding(Mime::ENCODING_8BIT);
        $part_text->setType(Mime::TYPE_TEXT);
        $part_text->setIsMappable(true);
        $m_tpl->append($part_text);
        
        $html = new Part($content);
        $html->setEncoding(Mime::ENCODING_8BIT);
        $html->setType(Mime::TYPE_HTML);
        $html->setIsMappable(true);
        $m_tpl->append($html);
        
        foreach ($files as $file) {
            $attachement = new Part($file['content']);
            $attachement->setIsPath(true);
            $attachement->setIsEncoded($file['is_encoding']);
            $attachement->setEncoding((isset($file['encoding']) ? $file['encoding'] : Mime::ENCODING_BASE64));
            $attachement->setType($file['type']);
            $attachement->setFilename($file['name']);
            $attachement->setDisposition(Mime::DISPOSITION_ATTACHMENT);
            $attachement->saveBuffer();
            $m_tpl->append($attachement);
        }

        return ($this->tpl_storage->write($m_tpl)) ? true : false;
    }

    public function sendTpl($name, $to, $datas = [])
    {
        $message = $this->getMessage()
            ->setTplStorage($this->tpl_storage)
            ->setEncoding('UTF-8')
            ->setBodyTpl($name, $datas)
            ->setTo($to);

        $this->getTransport()->send($message);
        
        return true;
    }

    /**
     * Set Storage Mail
     *
     * @param \Mail\Template\Storage\AbstractStorage $storage
     */
    public function setTplStorage(AbstractStorage $storage)
    {
        $this->tpl_storage = $storage;

        return $this;
    }

    /**
     * @return \Mail\Mail\Message
     */
    protected function getMessage()
    {
        return new Message();
    }

    /**
     * @invokable
     */
    public function getListTpl()
    {
        $results = $this->tpl_storage->getList();

        return array('count' => count($results), 'results' => $results);
    }

    /**
     * @invokable
     */
    public function getTpl($name)
    {
        if (!$this->tpl_storage->exist($name)) {
            throw new \Exception('Model name does not exist');
        }

        return $this->tpl_storage->read($name);
    }

    /**
     * @throws \Exception
     *
     * @return \Zend\Mail\Storage\Imap
     */
    public function getStorage()
    {
        if (!$this->is_init) {
            $this->init();
        }

        return $this->storage;
    }

    /**
     * @throws \Exception
     *
     * @return \Zend\Mail\Transport\Smtp
     */
    public function getTransport()
    {
        if (!$this->is_init) {
            $this->init();
        }

        return $this->transport;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }
}
