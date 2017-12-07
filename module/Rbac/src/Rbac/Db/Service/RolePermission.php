<?php

namespace Rbac\Db\Service;

use Dal\Service\AbstractService;

class RolePermission extends AbstractService
{
    public function getDroits()
    {
        return $this->getMapper()
            ->getDroit()
            ->toArray();
    }

    public function insert($permission)
    {
        return $this->getMapper()->insert($permission);
    }

    /**
     * Add role permission.
     *
     * @invokable
     *
     * @param int $role_id
     * @param int $permission_id
     *
     * @return int
     */
    public function add($role_id, $permission_id)
    {
        if (!is_array($role_id)) {
            $role_id = [$role_id];
        }

        $m_role_permission = $this->getModel()->setPermissionId($permission_id);

        foreach ($role_id as $r) {
            $m_role_permission->setRoleId($r);
            $ret = $this->getMapper()->insert($m_role_permission);
        }

        $this->getServiceRbac()->createRbac();

        return $ret;
    }

    /**
     * Delete role permission.
     *
     * @invokable
     *
     * @param int $permission_id
     *
     * @return int
     */
    public function delete($permission_id)
    {
        $ret = array();
        if (!is_array($permission_id)) {
            $permission_id = [$permission_id];
        }

        $m_permission = $this->getModel();
        foreach ($permission_id as $i) {
            $m_permission->setPermissionId($i);
            $ret[$i] = $this->getMapper()->delete($m_permission);
        }

        $this->getServiceRbac()->createRbac();

        return $ret;
    }

    /**
     * @invokable
     *
     * @param int $id
     * @param int $role_id
     * @param int $permission_id
     */
    public function update($id, $role_id, $permission_id)
    {
        $m_role_permission = $this->getModel($id)->setId($id)->setRoleId($role_id)->setPermissionId($permission_id);

        $ret = $this->getMapper()->update($m_role_permission);

        if ($ret > 0) {
            $this->getServiceRbac()->createRbac();
        }

        return $ret;
    }

    /**
     * @return \Rbac\Service\Rbac
     */
    public function getServiceRbac()
    {
        return $this->container->get('rbac.service');
    }
}
