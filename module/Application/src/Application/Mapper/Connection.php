<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Expression;

class Connection extends AbstractMapper
{
    public function selectLastConnection($token, $user)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->columns(['id', 'start', 'end'])
            ->where(['token' => $token])
            ->where(['user_id' => $user])
            ->order(['id' => 'DESC'])
            ->limit(1);

        return $this->selectWith($select);
    }

    public function getAvg($school, $day = null)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->columns(
            [
            'connection$avg' => new Expression('AVG(TIMESTAMPDIFF(SECOND, connection.start, connection.end))'),
            'connection$nbr_session' => new Expression('SUM(true)'), ]
        )
            ->join('user', 'user.id=connection.user_id', [])
            ->where(['user.school_id' => $school]);

        if (null !== $day) {
            $select->where(array('connection.start > DATE_ADD(UTC_TIMESTAMP(), INTERVAL -'.$day.' DAY)'));
        }

        return $this->selectWith($select);
    }
}
