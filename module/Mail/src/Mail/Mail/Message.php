<?php

namespace Mail\Mail;

use Zend\Mail\Message as BaseMessage;
use Zend\Mime\Message as MimeMessage;
use Mail\Template\Storage\AbstractStorage;
use Zend\Mime\Mime;

class Message extends BaseMessage
{
    protected $has_template = false;
    
    /**
     * Storage Tpl
     *
     * @var AbstractStorage
     */
    protected $tpl_storage;

    public function setBodyTpl($name, $datas)
    {
        if (null === $this->tpl_storage || !$this->tpl_storage->exist($name)) {
            throw new \Exception('Model name does not exist');
        }

        $tpl_model = $this->tpl_storage->read($name);

        $key = [];
        $value = [];
        foreach ($datas as $k => $v) {
            $key[] = sprintf('{%s}', $k);
            $value[] = $v;
        }

        $parts = [];
        $mimemessage = new MimeMessage();
        foreach ($tpl_model as $m_part) {
            if ($m_part->getIsMappable()) {
                $m_part->setDatas(['k' => $key, 'v' => $value]);
            }
            $parts[] = $m_part;
        }
        
        $mimemessage->setParts($parts);

        $this->setSubject(str_replace($key, $value, $tpl_model->getSubject()));
        $this->setFrom($tpl_model->getFrom(), $tpl_model->getFromName());
        $this->setBody($mimemessage);
        $this->setEncoding('UTF-8');
        $this->getHeaders()->get('content-type')->setType(Mime::MULTIPART_ALTERNATIVE);

        $this->has_template = true;

        return $this;
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
}
