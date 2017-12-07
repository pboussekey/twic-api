<?php
namespace Application\Service;

use Dal\Service\AbstractService;
use Zend\Db\Sql\Predicate\IsNotNull;

class Session extends AbstractService
{
    /**
     * Get Session By $uuid
     *
     * @param  string $uuid
     * @return \Application\Model\Session[]
     */
    public function get($uuid = null, $uid = null, $package = null)
    {
        if (null === $uuid && null === $uid) {
            throw new \Exception('error get session: all params is null');
        }

        return $this->getMapper()->select(
            $this->getModel()
                ->setUuid($uuid)->setUid($uid)->setRegistrationId(new IsNotNull())->setPackage($package)
        );
    }

    /**
     * Update session fcm
     * 
     * @param  string $token
     * @param  string $uuid
     * @param  string $registration_id
     * @param  string $package
     * 
     * @return int
     */
    public function update($token, $uuid, $registration_id, $package = null)
    {
        return $this->getMapper()->update(
            $this->getModel()
                ->setUuid($uuid)
                ->setRegistrationId($registration_id)
                ->setPackage($package),
            ['token' => $token]
        );
    }
    
    /**
     * Delete sesion and fcm session
     *
     * @param  string $uuid
     * @param  string $token
     * @param  string $registration_id
     * @throws \Exception
     * @return bool
     */
    public function delete($uuid = null, $token = null, $registration_id = null)
    {
        if (null === $uuid && null === $token && null === $registration_id) {
            throw new \Exception('error delete session: all params is null');
        }

        return $this->getMapper()->delete(
            $this->getModel()
                ->setUuid($uuid)
                ->setToken($token)
                ->setRegistrationId($registration_id)
        );
    }
}
