<?php
/**
 * TheStudnet (http://thestudnet.com)
 *
 * CacheBddStorage
 */
namespace Auth\Authentication\Storage;

use Zend\Db\Sql\Sql as DbSql;

/**
 * Class CacheBddStorage
 */
class CacheBddStorage implements StorageInterface
{
    use TraitStorage;
    
    /**
     * Bdd Adapter
     *
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $db_adapter;
    
    /**
     * Cache
     *
     * @var \Zend\Cache\Storage\StorageInterface
     */
    protected $cache;
    
    /**
     * Data Session
     *
     * @var mixed
     */
    protected $data;
    
    /**
     * Prefix token
     *
     * @var string
     */
    protected $prefix_session = 'sess_';

    /**
     * Constructor
     *
     * @param \Zend\Db\Adapter\Adapter             $adapter
     * @param \Zend\Cache\Storage\StorageInterface $cache
     */
    public function __construct($adapter, $cache)
    {
        $this->db_adapter = $adapter;
        $this->cache = $cache;
    }
    
    /**
     * Returns true if and only if storage is empty
     *
     * @throws \Zend\Authentication\Exception\ExceptionInterface If it is impossible to determine whether storage is empty
     * @return bool
     */
    public function isEmpty()
    {
        return !$this->getSession();
    }
    
    /**
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty
     *
     * @throws \Zend\Authentication\Exception\InvalidArgumentException
     * @return \Auth\Authentication\Storage\Session
     */
    public function read()
    {
        return $this->getSession();
    }
    
    /**
     * Writes $contents to storage
     *
     * @param  mixed $contents
     * @throws \Zend\Authentication\Exception\InvalidArgumentException If writing $contents to storage is impossible
     */
    public function write($data)
    {
        $this->setToken($data->getToken());
        $this->saveCacheSession($data);
        $this->saveDbbSession($data);
        $this->data = $data;
        
        return;
    }
    
    /**
     * Clears contents from storage
     *
     * @throws \Zend\Authentication\Exception\InvalidArgumentException If clearing contents from storage is impossible
     */
    public function clear()
    {
        $this->clearToken();

        return;
    }
    
    /**
     * Get Session If exist
     *
     * @return false|mixed
     */
    protected function getSession()
    {
        if (!$this->data && $this->getToken() !== null && ($this->cache === null || ($this->cache !== null && ($this->data = $this->cache->getItem($this->getPrefixToken())) === null))) {
            $sql = new DbSql($this->db_adapter);
            $select = $sql->select('session');
            $select->columns(['token', 'data', 'uid'])
                ->where(['token' => $this->getPrefixToken()]);
            $statement = $sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            if ($results->count() > 0) {
                $this->data = $results->current()['data'];
                $this->data = unserialize($this->data);
                $this->saveCacheSession($this->data);
            }
        }
           
        return $this->data;
    }
    
    /**
     * Get token with prefix.
     *
     * @return string
     */
    protected function getPrefixToken()
    {
        return sprintf($this->prefix_session.'%s', $this->getToken());
    }
    
    /**
     * Save Cache Session
     *
     * @param  string $data
     * @return boolean
     */
    protected function saveCacheSession($data)
    {
        $ret = false;
        if ($this->cache !== null) {
            if ($this->cache->getItem($this->getPrefixToken()) === null) {
                $this->cache->addItem($this->getPrefixToken(), $data);
            } else {
                $this->cache->setItem($this->getPrefixToken(), $data);
            }
            $ret = true;
        }
    
        return $ret;
    }
    
    /**
     * Save Bdd Session
     *
     * @param  string $data
     * @return boolean
     */
    protected function saveDbbSession($data)
    {
        $ret = false;
        $sql = new DbSql($this->db_adapter);
        $select = $sql->select('session');
        $select->columns(['token', 'data', 'uid'])
            ->where(['token' => $this->getPrefixToken()]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        if ($results->count() > 0) {
            $update = $sql->update('session');
            $update->set(['data' => serialize($data)])
                ->set(['uid' => $data->getId()])
                ->where(['token' => $this->getPrefixToken()]);
            $sql->prepareStatementForSqlObject($update)->execute();
            ;
            
            $ret = true;
        } else {
            $insert = $sql->insert('session');
            $insert->values(
                [
                'token' => $this->getPrefixToken(),
                'data' => serialize($data),
                'uid' => $data->getId()]
            );
            $sql->prepareStatementForSqlObject($insert)->execute();

            $ret = true;
        }
        
        return $ret;
    }
    
    /**
     * Clear Token
     *
     * @param string $token
     */
    protected function clearToken($token = null)
    {
        if (null === $token) {
            $token = $this->getPrefixToken();
            $this->data = null;
        }
        
        if ($this->cache !== null && $this->cache->hasItem($token)) {
            $this->cache->removeItem($token);
        }
        
        $sql = new DbSql($this->db_adapter);
        $delete = $sql->delete('session');
        $delete->where(['token' => $token]);
        $sql->prepareStatementForSqlObject($delete)->execute();
    }
    
    /**
     * Clear Session
     *
     * @param  int $uid
     * @return boolean
     */
    public function clearSession($uid)
    {
        $resultSet = $this->getListSession($uid);
        foreach ($resultSet as $result) {
            $this->clearToken($result['token']);
        }
    
        $sql = new DbSql($this->db_adapter);
        $delete = $sql->delete('session');
        $delete->where(['uid' => $uid]);
        $sql->prepareStatementForSqlObject($delete)->execute();
        
        return true;
    }
    
    /**
     * GetListSession
     *
     * @param  int $uid
     * @return array
     */
    public function getListSession($uid)
    {
        $sql = new DbSql($this->db_adapter);
        $select = $sql->select('session');
        $select->columns(['token', 'data', 'uid'])
            ->where(['uid' => $uid]);
        $resultSet = $sql->prepareStatementForSqlObject($select)->execute();
        $ret = [];
        foreach ($resultSet as $result) {
            $result['data'] = unserialize($result['data']);
            $ret[] = $result;
        }
        
        return $ret;
    }
}
