<?php

namespace Rbac\Service;

use Zend\Permissions\Rbac\Role;
use Zend\Permissions\Rbac\Rbac as ZRBac;

class Rbac
{
    /**
     * @var \Zend\Permissions\Rbac\Rbac
     */
    protected $rbac;

    /**
     * @var \Rbac\Db\Service\Role
     */
    protected $service_role;

    /**
     * @var \Zend\Cache\Storage\StorageInterface
     */
    protected $cache;

    /**
     * Constructor
     *
     * @param \Rbac\Db\Service\Role                $service_role
     * @param \Zend\Cache\Storage\StorageInterface $cache
     */
    public function __construct($service_role, $cache)
    {
        $this->service_role = $service_role;
        $this->cache = $cache;
    }

    /**
     * @param array $options
     */
    public function initialize()
    {
        $this->getRbac();
    }

    /**
     * Check permission.
     *
     * @param array|string $role
     * @param string       $permission
     *
     * @return bool
     */
    public function isGranted($role, $permission)
    {
        if (!is_array($role)) {
            $role = [$role];
        }

        foreach ($role as $r) {
            if ($this->getRbac()->isGranted($r, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Rbac Obj.
     *
     * @return \Zend\Permissions\Rbac\Rbac
     */
    public function getRbac()
    {
        if ($this->rbac === null) {
            $this->rbac = (!$this->cache->hasItem('rbac')) ?
                $this->createRbac() : $this->cache->getItem('rbac');
        }

        return $this->rbac;
    }

    public function createRbac()
    {
        $roles = $this->service_role->getAll()->toArray();
        $rbac = new ZRBac();
        foreach ($roles as $role) {
            $ar_child = array();
            if (isset($role['parent'])) {
                foreach ($role['parent'] as $parent) {
                    if (!$rbac->hasRole($parent['name'])) {
                        $rbac->addRole(new Role($parent['name']));
                    }
                    $ar_child[] = $rbac->getRole($parent['name']);
                }
            }
            if (!$rbac->hasRole($role['name'])) {
                $rbac->addRole(new Role($role['name']));
            }
            $r = $rbac->getRole($role['name']);
            $rbac->addRole($r, $ar_child);
            if (isset($role['permission'])) {
                foreach ($role['permission'] as $p) {
                    $r->addPermission($p['libelle']);
                }
            }
        }

        $this->rbac = $rbac;

        if ($this->cache->hasItem('rbac')) {
            $this->cache->replaceItem('rbac', $rbac);
        } else {
            $this->cache->setItem('rbac', $rbac);
        }

        return $this->rbac;
    }
}
