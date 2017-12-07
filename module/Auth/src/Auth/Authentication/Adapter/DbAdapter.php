<?php

namespace Auth\Authentication\Adapter;

use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Db\Adapter\Adapter;
use Zend\Authentication\Result;
use Zend\Db\Sql\Sql as DbSql;
use Auth\Authentication\Adapter\Model\Identity;
use Zend\Math\Rand;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Predicate\IsNotNull;

class DbAdapter extends AbstractAdapter
{
    const FAILURE_ACCOUNT_SUSPENDED = -5;
    const FAILURE_ACCOUNT_BAD_MDP = -6;
    const FAILURE_ACCOUNT_BAD_LOGIN = -7;
    /**
     * Bdd Adapter.
     *
     * @var Adapter
     */
    protected $db_adapter;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var \Auth\Authentication\Adapter\Model\IdentityInterface
     */
    protected $result;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @var string
     */
    protected $identity_column;

    /**
     * @var string
     */
    protected $credential_column;

    /**
     * @var string
     */
    protected $linkedin_id;
    
    /**
     * Sets username and password for authentication.
     */
    public function __construct(Adapter $db_adapter, $table, $identity_column, $credential_column, $hash = 'MD5(?)', $result = null)
    {
        $this->db_adapter = $db_adapter;
        $this->table = $table;
        $this->identity_column = $identity_column;
        $this->credential_column = $credential_column;
        $this->result = $result;
        $this->hash = $hash;
    }

    /**
     * Performs an authentication attempt.
     *
     * @return \Zend\Authentication\Result
     *
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     */
    public function authenticate()
    {
        $code = Result::FAILURE;
        $message = array();
        $identity = null;

        $sql = new DbSql($this->db_adapter);
        $select = $sql->select();
        $select->from($this->table)
            ->columns(['*']);
        
        if (null !== $this->linkedin_id) {
            $select->where(['user.linkedin_id' => $this->linkedin_id])
                ->where(['user.deleted_date IS NULL']);
        } elseif (null !== $this->credential && null !== $this->identity) {
            $select->where([' ( user.password = MD5(?) ' => $this->credential])
                ->where(['user.new_password = MD5(?) )' => $this->credential], Predicate::OP_OR)
                ->where(['user.'.$this->identity_column.' = ? ' => $this->identity])
                ->where(['user.deleted_date IS NULL']);
        } else {
            throw new \Exception("error authentification");
        }
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        if ($results->count() < 1) {
            $select = $sql->select();
            $select->from($this->table)
                ->columns(['*'])
                ->where(['user.'.$this->identity_column.'' => $this->identity])
                ->where(array('user.deleted_date IS NULL'));
            $statement = $sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            if ($results->count() < 1) {
                $code = self::FAILURE_ACCOUNT_BAD_LOGIN;
            } elseif ($results->count() > 0) {
                $code = self::FAILURE_ACCOUNT_BAD_MDP;
            } else {
                $code = Result::FAILURE_CREDENTIAL_INVALID;
            }
            
            $message[] = 'A record with the supplied identity could not be found.';
        } elseif ($results->count() > 1) {
            $code = Result::FAILURE_IDENTITY_AMBIGUOUS;
            $message[] = 'More than one record matches the supplied identity.';
        } elseif ($results->count() === 1) {
            $arrayIdentity = (new ResultSet())->initialize($results)->toArray();
            $arrayIdentity = current($arrayIdentity);
            if (null !== $arrayIdentity['suspension_date']) {
                $code = self::FAILURE_ACCOUNT_SUSPENDED;
                $message[] = $arrayIdentity['suspension_reason'];
            } else {
                $code = Result::SUCCESS;
                $message[] = 'Authentication successful.';
                $identity = $this->getResult()->exchangeArray($arrayIdentity);
                $identity->setToken($identity->getId().md5($identity->getId().$identity->getEmail().Rand::getBytes(10).time()));
                $update = $sql->update('user');
                $update->set(array('password' => md5($this->credential), 'new_password' => null))->where(array('id' => $arrayIdentity['id'], new IsNotNull('new_password')));
                $statement = $sql->prepareStatementForSqlObject($update);
                $statement->execute();
            }
        } else {
            throw new \Exception("Error number result authentification");
        }
        
        return new Result($code, $identity, $message);
    }

    /**
     * @return \Auth\Authentication\Adapter\Model\IdentityInterface
     */
    public function getResult()
    {
        if (null === $this->result) {
            $this->result = new Identity();
        }

        return $this->result;
    }
    
    public function setLinkedinId($linkedin_id)
    {
        $this->linkedin_id = trim($linkedin_id);
        
        return $this;
    }
    
    public function getLinkedinId()
    {
        return $this->linkedin_id;
    }
}
