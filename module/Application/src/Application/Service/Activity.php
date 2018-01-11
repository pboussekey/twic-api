<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Activity
 */
namespace Application\Service; 

use Dal\Service\AbstractService;
use JRpc\Json\Server\Exception\JrpcException;
use Zend\Db\Sql\Predicate\Between;
use Application\Model\Role as ModelRole;

/**
 * Class Activity
 */
class Activity extends AbstractService
{
    /**
     * Create Activity
     *
     * @invokable
     *
     * @param  array $activities
     * @return array
     */
    public function add($activities)
    {
        $ret = []; 
        $user = $this->getServiceUser()->getIdentity()['id'];
        foreach ($activities as $activity) {
            $date = (isset($activity['date'])) ? $activity['date'] : null;
            $event = (isset($activity['event'])) ? $activity['event'] : null;
            $object = (isset($activity['object'])) ? $activity['object'] : null;
            $target = (isset($activity['target'])) ? $activity['target'] : null;

            $ret[] = $this->_add($date, $event, $object, $target, $user);
        }
        $this->getServiceConnection()->add();

        return $ret;
    }

    /**
     * Create Activity.
     *
     * @param string $date
     * @param string $event
     * @param array  $object
     * @param array  $target
     * @param int    $user_id
     *
     * @throws \Exception
     *
     * @return int
     */
    public function _add($date = null, $event = null, $object = null, $target = null, $user_id = null)
    {
        if(null !== $date) {
            $date = (new \DateTime($date))->format('Y-m-d H:i:s');
        }
        $m_activity = $this->getModel();
        $m_activity->setEvent($event);
        $m_activity->setDate($date);
        $m_activity->setUserId($user_id);

        if (null !== $object) {
            if (isset($object['id'])) {
                $m_activity->setObjectId($object['id']);
            }
            if (isset($object['value'])) {
                $m_activity->setObjectValue($object['value']);
            }
            if (isset($object['name'])) {
                $m_activity->setObjectName($object['name']);
            }
            if (isset($object['data'])) {
                $m_activity->setObjectData(json_encode($object['data']));
            }
        }
        if (null !== $target) {
            if (isset($target['id'])) {
                $m_activity->setTargetId($target['id']);
            }
            if (isset($target['name'])) {
                $m_activity->setTargetName($target['name']);
            }
            if (isset($target['data'])) {
                $m_activity->setTargetData(json_encode($target['data']));
            }
        }

        if ($this->getMapper()->insert($m_activity) <= 0) {
            throw new \Exception('error insert ativity');// @codeCoverageIgnore
        }

        return $this->getMapper()->getLastInsertValue();
    }

    /**
     * Get List Activity.
     *
     * @invokable
     *
     * @param int    $user_id
     * @param array  $filter
     * @param string $search
     * @param string $start_date
     * @param string $end_date
     *
     * @return array
     */
    public function getList($filter = [], $search = null, $start_date = null, $end_date = null, $user_id = null)
    {
        $mapper = $this->getMapper();
        $res_activity = $mapper->usePaginator($filter)->getList($search, $start_date, $end_date, null, $user_id);

        return ['count' => $mapper->count(), 'list' => $res_activity];
    }

    /**
     * Get List connections.
     *
     * @invokable
     *
     * @param int    $organization_id
     * @param int    $user_id
     * @param string $start_date
     * @param string $interval_date
     * @param string $end_date
     *
     * @return array
     */
    public function getConnections($start_date = null, $end_date = null, $organization_id = null, $interval_date = 'D', $user_id = null)
    {
        if(null !== $organization_id && !is_array($organization_id)){
            $organization_id = [$organization_id];
        }
        $mapper = $this->getMapper();
        $res_activity = $mapper->getList(null, $start_date, $end_date, $organization_id, $user_id);
        $arrayUser = [];
        $connections = [];
        $interval = $this->interval($interval_date);
        foreach ($res_activity as $m_activity)
        {
            if(!array_key_exists($m_activity->getUserId(), $arrayUser)) {
                $arrayUser[$m_activity->getUserId()] = 
                    ['start_date' => $m_activity->getDate(), 'end_date' => $m_activity->getDate()];
            }
            else 
            {
                $difference = (strtotime($m_activity->getDate()) - strtotime($arrayUser[$m_activity->getUserId()]['end_date']));
                if ($difference < 600 && strcmp(substr($m_activity->getDate(), 0, $interval), substr($arrayUser[$m_activity->getUserId()]['end_date'], 0, $interval)) == 0) {
                    $arrayUser[$m_activity->getUserId()]['end_date'] = $m_activity->getDate();
                }
                else
                {
                    $actual_day = substr($arrayUser[$m_activity->getUserId()]['end_date'], 0, $interval);
                    if (!array_key_exists($actual_day, $connections)) {
                        $connections[$actual_day] = [];
                    }
                    $connections[$actual_day][] = strtotime($arrayUser[$m_activity->getUserId()]['end_date']) - strtotime($arrayUser[$m_activity->getUserId()]['start_date']);

                    $arrayUser[$m_activity->getUserId()] = 
                        ['start_date' => $m_activity->getDate(), 'end_date' => $m_activity->getDate()];
                }
            }

        }
        foreach ($arrayUser as $m_arrayUser)
        {
            $actual_day = substr($m_arrayUser['end_date'], 0, $interval);
            if (!array_key_exists($actual_day, $connections)) {
                $connections[$actual_day] = [];
            }
            $connections[$actual_day][] = strtotime($m_arrayUser['end_date']) - strtotime($m_arrayUser['start_date']);
        }

        foreach ($connections as $actual_day => $m_connections)
        {
            $connections[$actual_day] =  [ 'avg' => array_sum($m_connections) / count($m_connections), 'count' => count($m_connections)];
        }

        return $connections;
    }

    public function interval($interval = 'D') 
    {
        $ret = false;
        switch ($interval) {
        case 'D':
            $ret = 10;
            break;
        case 'M':
            $ret = 7;
            break;
        case 'Y':
            $ret = 4;
            break;
        }

        return $ret;
    }

    /**
     * @invokable
     * 
     * @param string $start_date
     * @param string $end_date
     * @param string $object_name
     */
    public function getPages($start_date, $end_date, $object_name = null)
    {
        $mapper = $this->getMapper();
        return $mapper->getPages($object_name, $start_date, $end_date);
    }

    /**
     * Get Service User.
     *
     * @return \Application\Service\User
     */
    private function getServiceUser()
    {
        return $this->container->get('app_service_user');
    }

    /**
     * Get Service Connection.
     *
     * @return \Application\Service\Connection
     */
    private function getServiceConnection()
    {
        return $this->container->get('app_service_connection');
    }
}
