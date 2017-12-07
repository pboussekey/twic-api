<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Circle Organization
 */
namespace Application\Service;

use Dal\Service\AbstractService;

/**
 * Class CircleOrganization
 */
class CircleOrganization extends AbstractService
{
    /**
     * Add Relation schools circle
     *
     * @param  int       $circle_id
     * @param  int|array $organization_id
     * @return array
     */
    public function add($circle_id, $organization_id)
    {
        if (!is_array($organization_id)) {
            $organization_id = [$organization_id];
        }

        $ret = [];
        foreach ($organization_id as $o) {
            $ret[$o] = $this->getMapper()->insert($this->getModel()->setCircleId($circle_id)->setOrganizationId($o));
        }

        return $ret;
    }

    /**
     * Remove Relation schools circle
     *
     * @param  int       $circle_id
     * @param  int|array $organization_id
     * @return array
     */
    public function delete($circle_id, $organization_id)
    {
        if (!is_array($organization_id)) {
            $organization_id = [$organization_id];
        }

        $ret = [];
        foreach ($organization_id as $o) {
            $ret[$o] = $this->getMapper()->delete($this->getModel()->setCircleId($circle_id)->setOrganizationId($o));
        }

        return $ret;
    }

    /**
     * Get List Organization Circle
     *
     * @param  int $circle_id
     * @param  int $organization_id
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getList($circle_id = null, $organization_id = null)
    {
        if (null === $circle_id && null === $organization_id) {
            throw new \Exception('Error params'); // @codeCoverageIgnore
        }

        return $this->getMapper()->select($this->getModel()->setCircleId($circle_id)->setOrganizationId($organization_id));
    }
}
