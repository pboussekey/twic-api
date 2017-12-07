<?php

namespace Rbac\Db\Service;

use Dal\Service\AbstractService;

class Role extends AbstractService
{
    /**
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function fetchAll()
    {
        return $this->getMapper()->fetchAll();
    }

    public function insert($mRole)
    {
        $this->getMapper()->insert($mRole);

        return $this->getMapper()->getLastInsertValue();
    }

    public function delete($mRole)
    {
        return $this->getMapper()->delete($mRole);
    }

    public function getAll()
    {
        $res_role = $this->getMapper()->fetchAll();

        foreach ($res_role as $m_role) {
            $m_role->setParent($this->getServiceRole()->getListByParentId($m_role->getId()));
            $m_role->setPermission($this->getServicePermission()->getListByRole($m_role->getId()));
        }

        return $res_role;
    }

    public function getListByParentId($parent)
    {
        return $this->getMapper()->getListByParentId($parent);
    }

    /**
     * @return \Rbac\Db\Service\RoleRelation
     */
    public function getServiceRoleRelation()
    {
        return $this->container->get('rbac_service_role_relation');
    }

    /**
     * @return \Rbac\Db\Service\Role
     */
    public function getServiceRole()
    {
        return $this->container->get('rbac_service_role');
    }

    /**
     * @return \Rbac\Db\Service\Permission
     */
    public function getServicePermission()
    {
        return $this->container->get('rbac_service_permission');
    }
}
