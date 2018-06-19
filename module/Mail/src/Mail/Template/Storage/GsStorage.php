<?php
namespace Mail\Template\Storage;

use Google\Cloud\Storage\StorageClient;

class GsStorage extends AbstractStorage
{

    protected $path;

    protected $init_path = false;

    protected $cache_tpl = [];

    protected $client;

    /**
     *
     * @var \Zend\Cache\Storage\StorageInterface
     */
    protected $cache;

    public function write(\Mail\Template\Model\TplModel $model)
    {
        if (! is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }
        
        $fp = fopen($this->path . $model->getName() . '.obj', 'w');
        $ret = fwrite($fp, serialize($model));
        fclose($fp);
        
        if ($this->cache && $this->cache->hasItem('tpl_mail_' . $model->getName())) {
            $this->cache->replaceItem('tpl_mail_' . $model->getName(), $model);
        } else if ($this->cache) {
            $this->cache->setItem('tpl_mail_' . $model->getName(), $model);
        }
        
        $this->cache_tpl[$model->getName()] = $model;
        
        return ($ret) ? true : false;
    }

    public function read($name)
    {
        $model = null;
        switch (true) {
            case isset($this->cache_tpl[$name]):
                $model = $this->cache_tpl[$name];
                break;
            
            case ($this->cache && $this->cache->hasItem('tpl_mail_' . $name)):
                $model = $this->cache->getItem('tpl_mail_' . $name);
                $this->cache_tpl[$name] = $model;
                break;
            
            default:
                $model = unserialize(file_get_contents($this->path . $name . '.obj'));
                $this->cache->setItem('tpl_mail_' . $name, $model);
                $this->cache_tpl[$name] = $model;
                break;
        }
        
        return $model;
    }

    public function getList()
    {
        $ret = [];
        if ($handle = opendir($this->path)) {
            while (false !== ($entry = readdir($handle))) {
                if (preg_match('/.obj$/', $entry)) {
                    $ret[] = unserialize(file_get_contents($this->path . $entry));
                }
            }
            closedir($handle);
        }
        
        return $ret;
    }

    public function exist($name)
    {
        return (isset($this->cache_tpl[$name]) || ($this->cache && $this->cache->hasItem('tpl_mail_' . $name)) || file_exists($this->path . $name . '.obj'));
    }

    public function init($config = [])
    {
        if ($this->init_path === false) {
            if(!empty($config['credentials_file'])) {
                putenv('GOOGLE_APPLICATION_CREDENTIALS='.$config['credentials_file']);
            }
            $this->client = new StorageClient($config['options']);
            $this->client->registerStreamWrapper();
            $init_path = true;
        }

        $this->path = sprintf('gs://%s/', $config['bucket']);
        
        return $this;
    }

    /**
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Set Cache
     *
     * @param
     *            \Zend\Cache\Storage\StorageInterface
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
        
        return $this;
    }
}
