<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Activity
 */
namespace Application\Service;

use Dal\Service\AbstractService;

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
    public function getList($filter = [], $search = null, $start_date = null, $end_date = null, $user_id = null, $date_offset = 0)
    {
        $mapper = $this->getMapper();
        $res_activity = $mapper->usePaginator($filter)->getList($search, $start_date, $end_date, null, $user_id, $date_offset);

        return ['count' => $mapper->count(), 'list' => $res_activity];
    }

    /**
     * Get List connections.
     *
     * @invokable
     *
     * @param string $start_date
     * @param string $end_date
     * @param int    $page_id
     * @param string $interval_date
     * @param int    $user_id
     *@param int $date_offset
     *
     * @return array
     */
    public function getConnections($start_date = null, $end_date = null, $page_id = null, $interval_date = 'D', $user_id = null, $date_offset = 0)
    {
        if(null !== $page_id && !is_array($page_id)){
            $page_id = [$page_id];
        }
        $mapper = $this->getMapper();
        $res_activity = $mapper->getList(null, $start_date, $end_date, $page_id, $user_id, $date_offset);
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
        $ret = 10;
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
     * Get List connections.
     *
     * @invokable
     *
     * @param string $start_date
     * @param string $end_date
     * @param int    $page_id
     * @param string $interval_date
     * @param int    $user_id
     *@param int $date_offset
     *
     * @return array
     */
    public function getVisitsCount($start_date = null, $end_date = null, $page_id = null, $interval_date = 'D', $user_id = null, $date_offset = 0)
    {
        if(null !== $page_id && !is_array($page_id)){
            $page_id = [$page_id];
        }
        $interval = $this->interval($interval_date);
        $identity = $this->getServiceUser()->getIdentity();

        return $this->getMapper()->getVisitsCount($identity['id'], $interval, $start_date, $end_date, $page_id, $date_offset);

    }

     /**
     * Get List connections.
     *
     * @invokable
     *
     * @param string $start_date
     * @param string $end_date
     * @param int    $page_id
     * @param string $interval_date
     * @param int    $user_id
     *@param int $date_offset
     *
     * @return array
     */
    public function getDocumentsOpeningCount($start_date = null, $end_date = null, $page_id = null, $interval_date = 'D', $user_id = null, $date_offset = 0)
    {
        if(null !== $page_id && !is_array($page_id)){
            $page_id = [$page_id];
        }
        $interval = $this->interval($interval_date);
        $identity = $this->getServiceUser()->getIdentity();
        return $this->getMapper()->getDocumentsOpeningCount($identity['id'], $interval, $start_date, $end_date, $page_id, $date_offset);

    }


     /**
     * Get List connections.
     *
     * @invokable
     *
     * @param int|array $page_id
     * @param string $interval_date
     * @param string $start_date
     * @param string $end_date
     *@param int $date_offset
     *
     * @return array
     */
    public function getVisitsPrc($page_id, $interval_date = 'D', $start_date = null, $end_date = null, $date_offset = 0)
    {
        if(!is_array($page_id)) {
            $page_id = [$page_id];
        }
        $interval = $this->interval($interval_date);
        $res_activity = $this->getMapper()->getVisitsPrc($page_id, $start_date, $end_date, $interval, $date_offset);
        foreach($res_activity as $m_activity){
            $m_activity->setObjectData(json_decode($m_activity->getObjectData(), true));
        }
        return $res_activity;
    }

     /**
     * Get List connections.
     *
     * @invokable
     *
     * @param int|array $page_id
     * @param int|array $library_id
     * @param string $start_date
     * @param string $end_date
     *
     * @return array
     */
    public function getDocumentsOpeningPrc($page_id, $library_id = null, $start_date = null, $end_date = null)
    {
        if(!is_array($page_id)){
            $page_id = [$page_id];
        }
        $res_activity = $this->getMapper()->getDocumentsOpeningPrc($start_date, $end_date, $page_id, $library_id);
        foreach($res_activity as $m_activity){
            $m_activity->setObjectData(json_decode($m_activity->getObjectData(), true));
        }
        return $res_activity;
    }




     /**
     *
     * @invokable
     *
     * @param string $start_date
     * @param string $end_date
     *
     * @return array
     */
    public function getUsersActivities($start_date = null, $end_date = null)
    {
        $res_activity = $this->getMapper()->getUsersActivities($start_date, $end_date);
        foreach($res_activity as $m_activity){
            $m_activity->setObjectData(json_decode($m_activity->getObjectData(), true));
        }
        return $res_activity;
    }

    /**
    *
    * @invokable
    *
    * @param array $users
    * @param int $delay
    *
    * @return array
    */
   public function getListInactive($users, $delay)
   {
       $res_activity = $this->getMapper()->getListActive($users, $delay);

       foreach($res_activity as $m_activity){
           if (($idx = array_search($m_activity->getUserId(), $users)) !== false) {
               unset($users[$idx]);
           }
       }
       return array_values($users);
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
