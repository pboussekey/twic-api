<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Role
 */
namespace Application\Service;

use Dal\Service\AbstractService;
use Application\Model\Role as ModelRole;

/**
 * Class Role.
 */
class Role extends AbstractService
{
    /**
     * add role.
     *
     * @invokable
     *
     * @param string $name
     *
     * @throws \Exception
     *
     * @return int
     */
    public function add($name)
    {
        if ($this->getMapper()->insert(
            $this->getModel()
                ->setName($name)
        ) <= 0) {
            throw new \Exception('error insert');
        }

        return $this->getMapper()->getLastInsertValue();
    }

    /**
     * Update Role.
     *
     * @invokable
     *
     * @param int    $id
     * @param string $name
     *
     * @return int
     */
    public function update($id, $name)
    {
        $m_role = $this->getModel();

        $m_role->setId($id)->setName($name);

        return $this->getMapper()->update($m_role);
    }

    /**
     * Delete Role by ID.
     *
     * @invokable
     *
     * @param int $id
     *
     * @return int
     */
    public function delete($id)
    {
        return $this->getMapper()->delete(
            $this->getModel()
                ->setId($id)
        );
    }

    /**
     * Add role to user.
     *
     * @invokable
     *
     * @param int $role
     * @param int $user
     *
     * @return int
     */
    public function addUser($role, $user)
    {
        return $this->getServiceUserRole()->add($role, $user);
    }

    /**
     * Get Role By User Id.
     *
     * @param int $id
     *
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getRoleByUser($user_id = null)
    {
        if ($user_id === null) {
            $user_id = $this->getServiceAuth()
                ->getIdentity()
                ->getId();
        }

        return $this->getMapper()->getRoleByUser($user_id);
    }

    /**
     * Get Id By Name.
     *
     * @param string $namerole
     *
     * @return string
     */
    public function getIdByName($namerole)
    {
        return array_search($namerole, ModelRole::$role);
    }

    /**
     * Get Service UserRole.
     *
     * @return \Application\Service\UserRole
     */
    private function getServiceUserRole()
    {
        return $this->container->get('app_service_user_role');
    }

    /**
     * Get Service Auth.
     *
     * @return \Zend\Authentication\AuthenticationService
     */
    private function getServiceAuth()
    {
        return $this->container->get('auth.service');
    }
}
