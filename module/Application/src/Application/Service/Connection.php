<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Connection
 */
namespace Application\Service;

use Dal\Service\AbstractService;

/**
 * Class Connection.
 */
class Connection extends AbstractService
{
    /**
     * Add Connection.
     *
     * @return int
     */
    public function add()
    {
        $identity = $this->getServiceUser()->getIdentity();
        $m_connection = $this->selectLast();
        $current = new \DateTime('now', new \DateTimeZone('UTC'));

        $diff = ($m_connection) ? ($current->getTimestamp() - (new \DateTime($m_connection->getEnd(), new \DateTimeZone('UTC')))->getTimestamp()) : null;

        if ($diff > 3600 || $diff === null) {
            $m_connection = $this->getModel()
                ->setUserId($identity['id'])
                ->setToken($identity['token'])
                ->setStart($current->format('Y-m-d H:i:s'))
                ->setEnd($current->format('Y-m-d H:i:s'));

            return $this->getMapper()->insert($m_connection);
        } else {
            $m_connection->setEnd($current->format('Y-m-d H:i:s'));

            return $this->getMapper()->update($m_connection);
        }
    }

    /**
     * Select Last Connection.
     *
     * @return \Application\Model\Connection
     */
    public function selectLast()
    {
        $identity = $this->getServiceUser()->getIdentity();

        $m_connection = null;
        $res_connection = $this->getMapper()->selectLastConnection($identity['token'], $identity['id']);
        if ($res_connection->count() > 0) {
            $m_connection = $res_connection->current();
        }

        return $m_connection;
    }

    /**
     * Get Service user.
     *
     * @return \Application\Service\User
     */
    private function getServiceUser()
    {
        return $this->container->get('app_service_user');
    }
}
