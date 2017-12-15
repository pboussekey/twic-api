<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Circle
 */
namespace Application\Service;

use Dal\Service\AbstractService;

/**
 * Class Circle
 */
class Circle extends AbstractService
{

    /**
     * Add circle
     *
     * @invokable
     *
     * @param  string $name
     * @throws \Exception
     * @return int
     */
    public function add($name)
    {
        if ($this->getMapper()->insert(
            $this->getModel()
                ->setName($name)
        ) <= 0
        ) {
            throw new \Exception('error insert circle');// @codeCoverageIgnore
        }

        return $this->getMapper()->getLastInsertValue();
    }

    /**
     * Update Circle
     *
     * @invokable
     *
     * @param  int    $id
     * @param  string $name
     * @return int
     */
    public function update($id, $name)
    {
        return $this->getMapper()->update(
            $this->getModel()
                ->setId($id)
                ->setName($name)
        );
    }

    /**
     * Remove Circle
     *
     * @invokable
     *
     * @param  int $id
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
     * Get Circle
     *
     * @invokable
     *
     * @param  int $id
     * @return \Application\Model\Circle
     */
    public function get($id)
    {
        $m_organization = $this->getMapper()->select(
            $this->getModel()
                ->setId($id)
        )->current();

        $res_circle_organiszation = $this->getServiceCircleOrganization()->getList($id);
        $m_organization->setOrganizations(($res_circle_organiszation->count() > 0)?$res_circle_organiszation:[]);

        return $m_organization;
    }

    /**
     * Get List Circle
     *
     * @invokable
     *
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getList()
    {
        return $this->getMapper()->fetchAll();
    }


    /**
     * Add relation Circle Organizations
     *
     * @invokable
     *
     * @param int       $id
     * @param int|array $organizations
     */
    public function addOrganizations($id, $organizations)
    {
        return $this->getServiceCircleOrganization()->add($id, $organizations);
    }

    /**
     * Remove relation Circle Organizations
     *
     * @invokable
     *
     * @param int       $id
     * @param int|array $organizations
     */
    public function deleteOrganizations($id, $organizations)
    {
        return $this->getServiceCircleOrganization()->delete($id, $organizations);
    }

    /**
     * Get Service CircleOrganization
     *
     * @return \Application\Service\CircleOrganization
     */
    private function getServiceCircleOrganization()
    {
        return $this->container->get('app_service_circle_organization');
    }
}
