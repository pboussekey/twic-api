<?php

namespace Rbac\Db\Service;

use Dal\Service\AbstractService;

class Permission extends AbstractService
{
    public function getPermission()
    {
        return $this->getMapper()->getPermissions();
    }

    public function getPermissionByRole($role)
    {
    }

    public function insert($perm)
    {
        $this->getMapper()->insert($perm);

        return $this->getMapper()->getLastInsertValue();
    }

    /**
     * Add permission.
     *
     * @invokable
     *
     * @param string $libelle
     * @param int    $role
     *
     * @throws \Exception
     *
     * @return int
     */
    public function add($libelle, $role = null)
    {
        $m_permission = $this->getModel()->setLibelle($libelle);

        if ($this->getMapper()->insert($m_permission) <= 0) {
            throw new \Exception('error insert permission : '.$libelle);
        }

        $permission_id = $this->getMapper()->getLastInsertValue();

        if (null !== $role) {
            $this->getserviceRolePermission()->delete($permission_id);
            $this->getserviceRolePermission()->add($role, $permission_id);
        }

        $this->getServiceRbac()->createRbac();

        return $permission_id;
    }

    /**
     * Delete Permission by libelle.
     *
     * @invokable
     *
     * @param string $libelle
     *
     * @return int
     */
    public function delete($libelle)
    {
        $ret = array();
        if (!is_array($libelle)) {
            $libelle = array($libelle);
        }

        foreach ($libelle as $i) {
            $m_libelle = $this->getModel();
            $m_libelle->setLibelle($i);
            $ret[$i] = $this->getMapper()->delete($m_libelle);
        }

        $this->getServiceRbac()->createRbac();

        return $ret;
    }

    /**
     * @invokable
     *
     * @param int    $id
     * @param string $libelle
     */
    public function update($id, $libelle, $role = null)
    {
        $m_permission = $this->getModel()
            ->setId($id)
            ->setLibelle($libelle);

        $ret = $this->getMapper()->update($m_permission);

        if (null !== $role) {
            $this->getserviceRolePermission()->delete($id);
            $this->getserviceRolePermission()->add($role, $id);
        }

        if ($ret > 0) {
            $this->getServiceRbac()->createRbac();
        }

        return $ret;
    }

    public function getListByRole($role)
    {
        return $this->getMapper()->getListByRole($role);
    }

    /**
     * Get permission list.
     *
     * @invokable
     *
     * @param string $filter
     * @param string $search
     * @param int    $roleId
     *
     * @return array
     */
    public function getList($filter = null, $roleId = null, $search = null)
    {
        $mapper = $this->getMapper();
        $res = $mapper->usePaginator($filter)->getList($filter, $roleId, $search);

        return array('list' => $res, 'count' => $mapper->count());
    }

    /**
     * @return \Rbac\Service\Rbac
     */
    public function getServiceRbac()
    {
        return $this->container->get('rbac.service');
    }

    /**
     * @return \Rbac\Db\Service\RolePermission
     */
    public function getserviceRolePermission()
    {
        return $this->container->get('rbac_service_role_permission');
    }
}
