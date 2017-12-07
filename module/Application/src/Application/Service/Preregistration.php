<?php
namespace Application\Service;

use Dal\Service\AbstractService;

class Preregistration extends AbstractService
{

    /**
     * Get Pregestration
     *
     * @invokable
     *
     * @param string $account_token
     *
     * @return array
     */
    public function get($account_token)
    {
        return $this->getMapper()
            ->select($this->getModel()
            ->setAccountToken($account_token))
            ->current();
    }

    /**
     * Add pregegistration
     *
     * @invokable
     *
     * @param string $account_token
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param int $organization_id
     * @param int $user_id
     *
     * @return int
     */
    public function add($account_token, $firstname, $lastname, $email, $organization_id, $user_id = null)
    {
        $m_preregistration = $this->getModel()
            ->setAccountToken($account_token)
            ->setFirstName($firstname)
            ->setLastName($lastname)
            ->setEmail($email)
            ->setOrganizationId($organization_id)
            ->setUserId($user_id);
        
        $this->getMapper()->insert($m_preregistration);
        
        return $account_token;
    }

    /**
     * Delete preregistration
     *
     * @invokable
     *
     * @param string $account_token
     * @param int $user_id
     */
    public function delete($account_token = null, $user_id = null)
    {
        $nb = 0;
        if (null !== $account_token) {
            $nb += $this->getMapper()->delete($this->getModel()
                ->setAccountToken($account_token));
        }
        if (is_numeric($user_id)) {
            $nb += $this->getMapper()->delete($this->getModel()
                ->setUserId($user_id));
        }
        
        return $nb;
    }
}
