<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Predicate;

class Contact extends AbstractMapper
{
    /**
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getList($user_id, $exclude = null, $search = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['accepted_date', 'user_id', 'contact_id'])
            ->join(['ocontact' => 'contact'], 'ocontact.user_id = contact.contact_id AND ocontact.contact_id = contact.user_id', [])
            ->join('user', 'user.id= contact.contact_id', [])
            ->where(['contact.user_id' => $user_id])
            ->where(['ocontact.deleted_date IS NULL'])
            ->where(['contact.deleted_date IS NULL'])
            ->where(['user.deleted_date IS NULL']);

        if (!empty($exclude)) {
            $select->where->notIn('user.id', $exclude);
        }

        if (null !== $search) {
            $select->where(array('( CONCAT_WS(" ", user.firstname, user.lastname) LIKE ? ' => ''.$search.'%'))
                ->where(array('CONCAT_WS(" ", user.lastname, user.firstname) LIKE ? ' => ''.$search.'%'), Predicate::OP_OR)
                ->where(array('user.nickname LIKE ? )' => ''.$search.'%'), Predicate::OP_OR);
        }
        return $this->selectWith($select);
    }

    /**
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getListFollowers($user = null)
    {
        if (null === $user && null === $contact) {
            throw new \Exception('Invalid params');
        }
        $select = $this->tableGateway->getSql()->select();

        $select->columns(['request_date', 'user_id', 'contact_id'])
            ->where(['contact.request_date IS NOT NULL',
                'contact.deleted_date IS NULL'
            ])->join('user', 'contact.user_id = user.id', [])
              ->where('user.deleted_date IS NULL')
              ->where(['contact.contact_id' => $user]);
        return $this->selectWith($select);
    }

    /**
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getListFollowings($user = null)
    {
        if (null === $user && null === $contact) {
            throw new \Exception('Invalid params');
        }
        $select = $this->tableGateway->getSql()->select();

        $select->columns(['request_date', 'user_id', 'contact_id'])
            ->where(['contact.request_date IS NOT NULL',
                'contact.deleted_date IS NULL'
            ])->join('user', 'contact.contact_id = user.id', [])
              ->where('user.deleted_date IS NULL')
              ->where(['contact.user_id' => $user]);
        return $this->selectWith($select);
    }


    public function getAcceptedCount($me, $interval, $start_date = null, $end_date = null, $organization_id = null, $date_offset = 0)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(
            [
                'contact$accepted_date' => new Expression('SUBSTRING(DATE_SUB(contact.accepted_date, INTERVAL '.$date_offset.' HOUR ),1,'.$interval.')'),
            'contact$accepted' => new Expression('COUNT(DISTINCT contact.id)')]
        )
            ->where('contact.deleted_date IS NULL')
            ->where('contact.accepted IS TRUE')
            ->group(new Expression('SUBSTRING(DATE_SUB(contact.accepted_date, INTERVAL '.$date_offset.' HOUR ),1,'.$interval.')'));
        if (null != $organization_id) {
            $select->join('user', 'contact.contact_id = user.id', [])
                ->join('page_user', 'user.id = page_user.user_id', [])
                ->where(['page_user.page_id' => $organization_id]);
        }
        if(null !== $start_date) {
            $select->where(['contact.accepted_date > ? ' => $start_date]);
        }
        if(null !== $end_date) {
            $select->where(['contact.accepted_date < ? ' => $end_date]);
        }

        return $this->selectWith($select);
    }

    public function getRequestsCount($me, $interval, $start_date = null, $end_date = null, $organization_id = null, $date_offset = 0)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(
            [ 'contact$request_date' => new Expression(' SUBSTRING(DATE_SUB(contact.request_date, INTERVAL '.$date_offset. ' HOUR ),1,'.$interval.')'),
                          'contact$requested' => new Expression('COUNT(DISTINCT contact.id)')]
        )
            ->where('contact.deleted_date IS NULL')
            ->where('contact.requested IS TRUE')
            ->group(new Expression(' SUBSTRING(DATE_SUB(contact.request_date, INTERVAL '.$date_offset. ' HOUR ),1,'.$interval.')'));

        if (null != $organization_id) {
            $select->join('user', 'contact.user_id = user.id', [])
                ->join('page_user', 'user.id = page_user.user_id', [])
                ->where(['page_user.page_id' => $organization_id]);
        }
        if(null !== $start_date) {
            $select->where(['contact.request_date > ? ' => $start_date]);
        }
        if(null !== $end_date) {
            $select->where(['contact.request_date < ? ' => $end_date]);
        }
        return $this->selectWith($select);


    }
}
