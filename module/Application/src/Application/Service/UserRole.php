<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * User Role
 */
namespace Application\Service;

use Dal\Service\AbstractService;

/**
 * Class UserRole.
 */
class UserRole extends AbstractService
{
    /**
     * Add a Role to User.
     *
     * @param int $role_id
     * @param int $user_id
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function add($role_id, $user_id)
    {
        $m_user_role = $this->getModel();
        $m_user_role->setRoleId($role_id)->setUserId($user_id);

        if ($this->getMapper()->insert($m_user_role) <= 0) {
            throw new \Exception('error insert');
        }

        return true;
    }

    /**
     * Delete Role to User.
     *
     * @param int $user_id
     *
     * @return bool
     */
    public function deleteByUser($user_id)
    {
        return $this->getMapper()->delete(
            $this->getModel()
                ->setUserId($user_id)
        );
    }
}
