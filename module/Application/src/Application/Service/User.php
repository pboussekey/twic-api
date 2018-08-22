<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * User
 */
namespace Application\Service;

use Dal\Service\AbstractService;
use DateTimeZone;
use DateTime;
use JRpc\Json\Server\Exception\JrpcException;
use Application\Model\Role as ModelRole;
use Firebase\JWT\JWT;
use Zend\Db\Sql\Predicate\IsNull;
use Application\Model\PageUser as ModelPageUser;
use Zend\Mail\Message;

/**
 * Class User.
 */
class User extends AbstractService
{
    
    public function isStudnetAdmin()
    {
        $identity = $this->getIdentity();
        return $identity['roles'] !== null && in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles']);
    }
    
    public function loginLinkedIn($linkedin_id)
    {
        $auth = $this->getServiceAuth();
        $auth->getAdapter()->setLinkedinId($linkedin_id);
        
        $code = - 32000;
        $result = $auth->authenticate();
        if (! $result->isValid()) {
            switch ($result->getCode()) {
            case - 5:
                $code = - 32031;
                break;
            }
            
            throw new JrpcException($result->getMessages()[0], $code);
        }
        
        $identity = $this->getIdentity(true);
        
        // ici on check que le role externe ne ce connect pas avec login
        if (in_array(ModelRole::ROLE_EXTERNAL_STR, $identity['roles']) && count($identity['roles']) === 1) {
            $this->logout();
            throw new \Exception("Error: unauthorized Role");
        }
        
        return $identity;
    }

    /**
     * Log user
     *
     * @invokable
     *
     * @param  string $user
     * @param  string $password
     * @throws JrpcException
     * @return array
     */
    public function login($user, $password)
    {
        $auth = $this->getServiceAuth();
        $auth->getAdapter()->setIdentity(trim($user));
        $auth->getAdapter()->setCredential(trim($password));
        
        $code = - 32000;
        $result = $auth->authenticate();
        if (! $result->isValid()) {
            switch ($result->getCode()) {
            case - 5:
                $code = - 32031;
                break;
            case - 6:
                $code = - 32032;
                break;
            case - 7:
                $code = - 32033;
                break;
            }
            
            throw new JrpcException($result->getMessages()[0], $code);
        }
        
        $identity = $this->getIdentity(true);
        
        // ici on check que le role externe ne ce connect pas avec login
        if (in_array(ModelRole::ROLE_EXTERNAL_STR, $identity['roles']) && count($identity['roles']) === 1) {
            $this->logout();
            throw new \Exception("Error: unauthorized Role");
        }
        
        return $identity;
    }
    
    
    
    /**
     * Accept CGU
     *
     * @invokable
     *
     * @return int
     */
    public function acceptCgu()
    {
        return $this->getMapper()->update($this->getModel()->setId($this->getIdentity()['id'])->setCguAccepted(1));
    }

    // //////////////// EXTERNAL METHODE ///////////////////
    
    /**
     * Get/Create Identity in cache.
     *
     * @param bool $init
     *
     * @return array
     */
    public function _getCacheIdentity($init = false)
    {
        $user = [];
        $identity = $this->getServiceAuth()->getIdentity();
        if ($identity === null) {
            return;
        }
        $id = $identity->getId();
        if ($init === false && $this->getCache()->hasItem('identity_' . $id)) {
            $user = $this->getCache()->getItem('identity_' . $id);
        } else {
            $user = $identity->toArray();
            $user['roles'] = [];
            foreach ($this->getServiceRole()->getRoleByUser() as $role) {
                $user['roles'][$role->getId()] = $role->getName();
            }
            
            $secret_key = $this->container->get('config')['app-conf']['secret_key'];
            $user['wstoken'] = sha1($secret_key . $id);
            // $generator = new TokenGenerator($secret_key_fb);
            // $user['fbtoken'] = $generator->setData(array('uid' => (string) $id))->setOption('debug', $secret_key_fb_debug)->setOption('expires', 1506096687)->create();
            $user['fbtoken'] = $this->create_custom_token($id);
            $this->getCache()->setItem('identity_' . $id, $user);
        }
        
        return $user;
    }

    /**
     * Crete token custom firebase
     *
     * @invokable
     *
     * @return string
     */
    public function getCustomTokenfb()
    {
        return $this->create_custom_token($this->getIdentity()['id']);
    }

    public function create_custom_token($uid, $is_premium_account = false)
    {
        $service_account_email = $this->container->get('config')['app-conf']['account_email'];
        $private_key = $this->container->get('config')['app-conf']['private_key'];
        
        $now_seconds = time();
        $payload = [
            "iss" => $service_account_email,
            "sub" => $service_account_email,
            "aud" => "https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit",
            "iat" => $now_seconds,
            "exp" => $now_seconds + (60 * 60), // Maximum expiration time is one hour
            "uid" => $uid
            // "claims" => ["premium_account" => $is_premium_account]
        ];
        
        return JWT::encode($payload, $private_key, "RS256");
    }

    /**
     * Delete Cached Identity of user.
     *
     * @param int $id
     *
     * @return bool
     */
    private function deleteCachedIdentityOfUser($id)
    {
        return $this->getCache()->removeItem('identity_' . $id);
    }

  
    /**
     * Get Identity.
     *
     * @invokable
     *
     * @param bool $init
     *
     * @return array
     */
    public function getIdentity($init = false)
    {
        return $this->_getCacheIdentity($init);
    }

    /**
     * Log out.
     *
     * @invokable
     *
     * @return bool
     */
    public function logout()
    {
        $this->getServiceAuth()->clearIdentity();
        
        return true;
    }

    /**
     * Suspend or reactivate user account.
     *
     * @invokable
     *
     * @param int    $id
     * @param bool   $suspend
     * @param string $reason
     *
     * @return bool
     */
    public function suspend($id, $suspend, $reason = null)
    {
        if(!$this->isStudnetAdmin() ) {
            
            throw new JrpcException('Unauthorized operation user.suspend', -38003);
        }
        $m_user = $this->getModel()
            ->setId($id)
            ->setSuspensionDate(1 === $suspend ? (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s') : new IsNull())
            ->setSuspensionReason(1 === $suspend ? $reason : new IsNull());
        if (1 === $suspend) {
            $this->getServiceAuth()
                ->getStorage()
                ->clearSession($id);
        }
        return $this->getMapper()->update($m_user);
    }

    /**
     * Add User
     *
     * @invokable
     *
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $gender
     * @param string $origin
     * @param string $nationality
     * @param string $sis
     * @param string $password
     * @param string $birth_date
     * @param string $position
     * @param int    $organization_id
     * @param string $interest
     * @param string $avatar
     * @param array  $roles
     * @param string $timezone
     * @param string $background
     * @param string $nickname
     * @param string $ambassador
     * @param array  $address
     *
     * @return int
     */
    public function add($firstname, $lastname, $email, $gender = null, $origin = null, $nationality = null, $sis = null, $password = null, $birth_date = null, $position = null, $organization_id = null, $interest = null, $avatar = null, $roles = null, $timezone = null, $background = null, $nickname = null, $ambassador = null, $address = null)
    {
        
        if (! empty($sis)) {
            if ($this->getNbrSisUnique($sis) > 0) {
                throw new JrpcException('uid email', - 38002);
            }
        }
        if(!$this->isStudnetAdmin() && (null === $organization_id || !$this->getServicePage()->isAdmin($organization_id))) {
            
            throw new JrpcException('Unauthorized operation user.add', -38003);
        }
        
        return $this->_add($firstname, $lastname, $email, $gender, $origin, $nationality, $sis, $password, $birth_date, $position, $organization_id, $interest, $avatar, $roles, $timezone, $background, $nickname, $ambassador, $address);
    }
    
    public function _add($firstname, $lastname, $email, $gender = null, $origin = null, $nationality = null, $sis = null, $password = null, $birth_date = null, $position = null, $organization_id = null, $interest = null, $avatar = null, $roles = null, $timezone = null, $background = null, $nickname = null, $ambassador = null, $address = null, $active = null)
    {
        $m_user = $this->getModel();
        
        if ($address !== null) {
            $address = $this->getServiceAddress()->getAddress($address);
            if ($address && null !== ($address_id = $address->getId())) {
                $m_user->setAddressId($address_id);
            }
        }
        
        $m_user->setFirstname($firstname)
            ->setLastname($lastname)
            ->setEmail($email)
            ->setSis($sis)
            ->setOrigin($origin)
            ->setGender($gender)
            ->setNationality($nationality)
            ->setBirthDate($birth_date)
            ->setPosition($position)
            ->setInterest($interest)
            ->setAvatar($avatar)
            ->setTimezone($timezone)
            ->setBackground($background)
            ->setNickname($nickname)
            ->setAmbassador($ambassador)
            ->setEmailSent(0)
            ->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'))
            ->setIsActive($active);
        
        if (! empty($password)) {
            $m_user->setPassword(md5($password));
        }
        
        if ($this->getMapper()->insert($m_user) <= 0) {
            throw new \Exception('error insert'); // @codeCoverageIgnore
        }
        
        $id = (int) $this->getMapper()->getLastInsertValue();
        
        // Si il n'y a pas de role ou que ce n'est pas un admin c'est un user
        if (empty($roles) || ! in_array(ModelRole::ROLE_ADMIN_STR, $this->getIdentity()['roles'])) {
            $roles = [
                ModelRole::ROLE_USER_STR
            ];
        }
        foreach ($roles as $r) {
            $this->getServiceUserRole()->add(
                $this->getServiceRole()
                    ->getIdByName($r), $id
            );
        }
        
        
        if ($organization_id !== null) {
            $this->getServicePageUser()->_add($organization_id, $id, ModelPageUser::ROLE_USER, ModelPageUser::STATE_INVITED);
        }
        
        $this->getServiceSubscription()->add('SU' . $id, $id);
        
        return $id;
    }
   
    /**
     * Update User
     *
     * @invokable
     *
     * @param int    $id
     * @param string $gender
     * @param string $origin
     * @param string $nationality
     * @param string $firstname
     * @param string $lastname
     * @param string $sis
     * @param string $email
     * @param string $birth_date
     * @param string $position
     * @param int    $organization_id
     * @param string $interest
     * @param string $avatar
     * @param array  $roles
     * @param string $resetpassword
     * @param bool   $has_email_notifier
     * @param string $timezone
     * @param string $background
     * @param string $nickname
     * @param bool   $ambassador
     * @param string $password
     * @param array  $address
     *
     * @return int
     */
    public function update($id = null, $gender = null, $origin = null, $nationality = null, $firstname = null, $lastname = null, $sis = null, $email = null, $birth_date = null, $position = null, $organization_id = null, $interest = null, $avatar = null, $roles = null, $resetpassword = null, $has_email_notifier = null, $timezone = null, $background = null, $nickname = null, $suspend = null, $suspension_reason = null, $ambassador = null, $password = null, $address = null)
    {
        if ($this->getNbrEmailUnique($email, $id) > 0) {
            throw new JrpcException('duplicate email', - 38001);
        }
        if(!$this->isStudnetAdmin()){
            if(null !== $id && $id !==  $this->getIdentity()['id']) {
                throw new JrpcException('Unauthorized operation user.update', -38003);
            }
            
            if(null !== $roles){
                $roles = null;
            }
        }
        
        
         /*
         * if (null !== $avatar && $id === $this->getIdentity()['id']) {
         * $this->getServicePost()->addSys(
         * 'UU'.$id. 'A'.$avatar, 'Avatar update', [
         * 'state' => 'update',
         * 'user' => $id,
         * 'avatar' => $avatar,
         * ], 'update',
         * null/*sub/,
         * null/*parent/,
         * null/*page/,
         * $id/*user/,
         * 'user'
         * );
         * }
         */
        
        return $this->_update($id, $gender, $origin, $nationality, $firstname, $lastname, $sis, $email, $birth_date, $position, $organization_id, $interest, $avatar, $roles, $resetpassword, $has_email_notifier, $timezone, $background, $nickname, $suspend, $suspension_reason, $ambassador, $password, $address);
    }
    
    public function _update($id = null, $gender = null, $origin = null, $nationality = null, $firstname = null, $lastname = null, $sis = null, $email = null, $birth_date = null, $position = null, $organization_id = null, $interest = null, $avatar = null, $roles = null, $resetpassword = null, $has_email_notifier = null, $timezone = null, $background = null, $nickname = null, $suspend = null, $suspension_reason = null, $ambassador = null, $password = null, $address = null)
    {
         $m_user = $this->getModel();
        
        if ($id === null) {
            $id = $this->getIdentity()['id'];
        }
        if (! empty($password)) {
            $m_user->setPassword(md5($password));
        }
        
        if(null !== $birth_date) {
            $birth_date = (new \DateTime($birth_date))->format('Y-m-d H:i:s');
        }  
        
        if ($address !== null) {
            $address_id = null;
            if ($address === 'null') {
                $address_id = new IsNull('address_id');
            } else {
                $address = $this->getServiceAddress()->getAddress($address);
                if ($address) {
                    $address_id = $address->getId();
                }
            }
            if ($address_id !== null) {
                $m_user->setAddressId($address_id);
            }
        }
        
        $m_user->setId($id)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setOrigin(('null' === $origin) ? new IsNull('origin') : $origin)
            ->setGender($gender)
            ->setNationality(('null' === $nationality) ? new IsNull('nationality') : $nationality)
            ->setSis($sis)
            ->setBirthDate(('null' === $birth_date) ? new IsNull('birth_date') : $birth_date)
            ->setPosition($position)
            ->setInterest($interest)
            ->setAvatar(('null' === $avatar) ? new IsNull('avatar') : $avatar)
            ->setHasEmailNotifier($has_email_notifier)
            ->setTimezone($timezone)
            ->setBackground($background)
            ->setNickname($nickname)
            ->setAmbassador($ambassador);
        
        // @TODO secu school_id
        if ($organization_id !== null) {
            if ($organization_id === 'null') {
                $organization_id = new IsNull('organization_id');
            }
            $this->addOrganization($organization_id, $id, true);
        }
        
        if ($roles !== null) {
            if (! is_array($roles)) {
                $roles = [
                    $roles
                ];
            }
            $this->getServiceUserRole()->deleteByUser($id);
            foreach ($roles as $r) {
                $this->getServiceUserRole()->add(
                    $this->getServiceRole()
                        ->getIdByName($r), $id
                );
            }
        }
        if(null !== $email){
            $tmp_user = $this->getLite($id);
            if($tmp_user->getInitialEmail() instanceof IsNull){
                $m_user->setInitialEmail($tmp_user->getEmail());
            }
            if(strcmp($tmp_user->getEmail(),$email) !== 0){
                $m_user->setSwapEmail($email);
                $m_user->setSwapToken(uniqid($m_user->getId() . "_", true));
            }
        }
        
        $ret = $this->getMapper()->update($m_user);
        if($m_user->getSwapToken() !== null){
            $this->sendEmailUpdateConf();
        }
        if ($resetpassword) {
            $this->lostPassword($this->get($id)['email']);
        }
        
        if (null !== $suspend) {
            $this->suspend($id, $suspend, $suspension_reason);
        }
        // on supprime son cache identity pour qu'a ca prochaine cannection il el recré.
        $this->deleteCachedIdentityOfUser($id);
        $this->getServiceEvent()->sendData(
            $id, 'user.update', [
            'M'.$id,
            'PU' . $id
            ]
        );
        
        return $ret;
    }
    
    /**
     * Send email for confirm the update of an email address
     *
     * @invokable
     *
     */
    public function sendEmailUpdateConf(){
        
        $identity = $this->getIdentity();
        $m_user = $this->getLite($identity['id']);
        $m_organization = !$m_user->getOrganizationId() instanceof IsNull ? $this->getServicePage()->getLite($m_user->getOrganizationId()) : false;
        
        $prefix = ($m_organization !== false && is_string($m_organization->getLibelle()) && !empty($m_organization->getLibelle())) ?
        $m_organization->getLibelle() : null;
        if(!($m_user->getSwapEmail() instanceof IsNull)){
            $url = sprintf("https://%s%s/confirm-email/%s/%s", ($prefix ? $prefix.'.':''), $this->container->get('config')['app-conf']['uiurl'], $m_user->getId(), $m_user->getSwapToken());
            try{
                $this->getServiceMail()->sendTpl(
                    'tpl_emailupdate', $m_user->getSwapEmail(), [
                    'firstname' => $m_user->getFirstname(),
                    'newemail' => $m_user->getSwapEmail(),
                    'pageurl' => $url
                    ]
                );
            }
            catch (\Exception $e) {
                syslog(1, 'Model name does not exist tpl_emailupdate <MESSAGE> ' . $e->getMessage() . '  <CODE> ' . $e->getCode());
            }
            return true;
        }
        return false;
    }
    
    
     /**
     * Confirm the update of an email address
     *
     * @invokable
     *
     * @param int $id
     * @param string $token
     */
    public function confirmEmailUpdate($id, $token){
        $m_user = $this->getLite($id);
        if($token === $m_user->getSwapToken()){
            $this->getMapper()->update(
                $this->getModel()
                   ->setId($id)
                    ->setEmail($m_user->getSwapEmail())
                    ->setSwapEmail(new IsNull('swap_email'))
                    ->setSwapToken(new IsNull('swap_token'))
            );   
            $this->deleteCachedIdentityOfUser($id);
            $this->getServiceEvent()->sendData(
                $id, 'user.update', [
                    'M' . $id
                ]
            );
            return true;
        }
        return false;
    }
    
       /**
     * Cancel the update of an email address
     *
     * @invokable
     */
    public function cancelEmailUpdate(){
        $identity = $this->getIdentity();
        $this->getMapper()->update(
            $this->getModel()
               ->setId($identity['id'])
                ->setSwapEmail(new IsNull('swap_email'))
                ->setSwapToken(new IsNull('swap_token'))
        );   
        return true;
    }

    /**
     * Get number of email.
     *
     * @param string $email
     * @param int    $user_id
     *
     * @return int
     */
    public function getNbrEmailUnique($email, $user_id = null)
    {
        $res_user = $this->getMapper()->getEmailUnique($email, $user_id);
        
        return ($res_user->count() > 0) ? $res_user->current()->getNbUser() : 0;
    }

    /**
     * Get number of sis.
     *
     * @param string $sis
     *
     * @return int
     */
    public function getNbrSisUnique($sis)
    {
        $res_user = $this->getMapper()->getNbrSisUnique($sis);
        
        return ($res_user->count() > 0) ? $res_user->current()->getNbUser() : 0;
    }

    /**
     * Lost Password.
     *
     * @invokable
     *
     * @param string $email
     */
    public function lostPassword($email)
    {
        if(empty($email)) {
            throw new \Exception("email is empty");
        }
        
        $m_user = $this->getMapper()->select(
            $this->getModel()
                ->setEmail($email)
                ->setSuspensionDate(new IsNull())
                ->setDeletedDate(new IsNull())
        )->current();
        
        if ($m_user !== false && $m_user->getIsActive() === 1) {
            $uniqid = uniqid($m_user->getId() . "_", true);
            $m_page = $this->getServicePage()->getLite($m_user->getOrganizationId());
            $this->getServicePreregistration()->add($uniqid, null, null, null, $m_user->getOrganizationId(), $m_user->getId());
            
            $prefix = ($m_page !== false && is_string($m_page->getLibelle()) && !empty($m_page->getLibelle())) ?
            $m_page->getLibelle() : null;

            $url = sprintf("https://%s%s/newpassword/%s", ($prefix ? $prefix.'.':''),  $this->container->get('config')['app-conf']['uiurl'], $uniqid);
            try {
                $this->getServiceMail()->sendTpl(
                    'tpl_forgotpasswd', $m_user->getEmail(), [
                    'email' => $m_user->getEmail(),
                    'accessurl' => $url,
                    'uniqid' => $uniqid,
                    'lastname' => $m_user->getLastname() instanceof IsNull ? "" : $m_user->getLastname() ,
                    'firstname' => $m_user->getFirstname() instanceof IsNull ? "" : $m_user->getFirstname()
                    ]
                );
            } catch (\Exception $e) {
                syslog(1, 'Model name does not exist <> uniqid is : ' . $uniqid . ' <MESSAGE> ' . $e->getMessage() . '  <CODE> ' . $e->getCode() . ' <URL> ' . $url . ' <Email> ' . $m_user->getEmail());
            }
        }
        else if($m_user !== false && $m_user->getIsActive() === 0){
            $this->sendPassword($m_user->getId());
        }
        else {
            throw new \Exception("no account with email: ". $email);
        }
        
        return true;
    }

    /**
     * Pre SignIn
     *
     * @invokable
     *
     * @param string $email
     * @param int $page_id
     * @param string $firstname
     * @param string $lastname
     * @return boolean
     */
    public function preSignIn($email, $page_id, $firstname = null, $lastname = null)
    {
        $res_page = $this->getServicePage()->getCustom(null, $page_id);
        if($res_page->getDomaine() !== explode("@", $email)[1]) {
            throw \Exception('Error Mail is not valide');
        }
        
        $uniqid = uniqid($page_id . strlen($email) . "_", true);
        $m_page = $this->getServicePage()->getLite($page_id);
        
        $this->getServicePreregistration()->add($uniqid, $firstname, $lastname, $email, $page_id);
        
        $prefix = ($m_page !== false && is_string($m_page->getLibelle()) && !empty($m_page->getLibelle())) ?
        $m_page->getLibelle() : null;
        
        $url = sprintf("https://%s%s/signin/%s", ($prefix ? $prefix.'.':''),  $this->container->get('config')['app-conf']['uiurl'], $uniqid);
        
        try {
            $this->getServiceMail()->sendTpl(
                'tpl_sendpasswd', $email, [
                    'uniqid' => $uniqid,
                    'email' => $email,
                    'accessurl' => $url,
                    'lastname' => $lastname,
                    'firstname' => $firstname
                ]
                );
        } catch (\Exception $e) {
            syslog(1, 'Model name does not exist <> uniqid is : ' . $uniqid . ' <MESSAGE> ' . $e->getMessage() . '  <CODE> ' . $e->getCode() . ' <URL> ' . $url . ' <Email> ' . $email);
        }

        return true;
    }
    
    /**
     * Send New Password
     *
     * @invokable
     *
     * @param array|int $id
     * @param int       $page_id
     */
    public function sendPassword($id = null, $page_id = null, $unsent = true)
    {
        if (null !== $page_id) {
            $identity = $this->getIdentity();
            $is_admin = (in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles']));
            $res_user = $this->getMapper()->getList($identity['id'], $is_admin, null, null, $page_id, null, null, null, $unsent, null, null, null, null, null, ModelPageUser::STATE_INVITED);
            $id = [];
            foreach ($res_user as $m_user) {
                $id[] = $m_user->getId();
            }
        }
        
        if (!is_array($id)) {
            $id = [$id];
        }

        $nb = 0;
        
        $invitation_date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');
        foreach ($id as $uid) {
            $res_user = $this->getMapper()->select($this->getModel()->setId($uid));
            if ($res_user->count() <= 0) {
                continue;
            }
            
            $uniqid = uniqid($uid . "_", true);
            $m_user = $res_user->current();
            $m_page = $this->getServicePage()->getLite($m_user->getOrganizationId());
            $this->getServicePreregistration()->add($uniqid, null, null, null, $m_user->getOrganizationId(), $m_user->getId());
             
            $prefix = ($m_page !== false && is_string($m_page->getLibelle()) && !empty($m_page->getLibelle())) ?
            $m_page->getLibelle() : null;
            
            $url = sprintf("https://%s%s/signin/%s", ($prefix ? $prefix.'.':''),  $this->container->get('config')['app-conf']['uiurl'], $uniqid);
            try {
                $this->getServiceMail()->sendTpl(
                    'tpl_sendpasswd', $m_user->getEmail(), [
                    'uniqid' => $uniqid,
                    'email' => $m_user->getEmail(),
                    'accessurl' => $url,
                    'lastname' => $m_user->getLastname() instanceof IsNull ? "" : $m_user->getLastname(),
                    'firstname' => $m_user->getFirstname() instanceof IsNull ? "" : $m_user->getFirstname()
                    ]
                );
                $this->getMapper()->update(
                        $this->getModel()
                        ->setEmailSent(true)
                        ->setInvitationDate($invitation_date), ['id' => $uid]);
                $nb++;
            } catch (\Exception $e) {
                syslog(1, 'Model name does not exist <> uniqid is : ' . $uniqid . ' <MESSAGE> ' . $e->getMessage() . '  <CODE> ' . $e->getCode() . ' <URL> ' . $url . ' <Email> ' . $m_user->getEmail());
            }
        }
        
        return $nb;
    }

    /**
     * Update Password.
     *
     * @invokable
     *
     * @param string $oldpassword
     * @param string $password
     *
     * @return int
     */
    public function updatePassword($oldpassword, $password)
    {
        return $this->getMapper()->update(
            $this->getModel()
                ->setPassword(md5($password)), array(
            'id' => $this->getServiceAuth()
                ->getIdentity()
                ->getId(),
            'password' => md5($oldpassword)
            )
        );
    }

    /**
     *
     * @param int $id
     * @return \Dal\Db\ResultSet\ResultSet|\Application\Model\User
     */
    public function getLite($id)
    {
        $res_user = $this->getMapper()->select(
            $this->getModel()
                ->setId($id)
        );
        return (is_array($id)) ? $res_user : $res_user->current();
    }
    
    /**
     * Check if an account token is valid
     * 
     * @invokable
     * @param     string $token
     * @return    \Dal\Db\ResultSet\ResultSet|\Application\Model\User
     */
    public function checkAccountToken($token)
    {
        //$res_user = $this->getServicePreregistration()->get($token);
        $res_user = $this->getMapper()->checkUser($token);
        
        return $res_user->current();
    }
    
    /**
     * Check if an email is valid
     * 
     * @invokable
     * @param     string $email
     * @return    \Dal\Db\ResultSet\ResultSet|\Application\Model\User
     */
    public function checkEmail($email)
    {
        $res_user = $this->getMapper()->checkUser(null, $email);
        
        return $res_user->current();
    }

    /**
     * Get User
     *
     * @invokable
     *
     * @param  int|array $id
     * @return array
     */
    public function get($id = null)
    {
        $users = [];
        $identity = $this->getIdentity();
        $user_id = $identity['id'];
        if ($id === null) {
            $id = $user_id;
        }
        
        $is_admin = (in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles']));
        $res_user = $this->getMapper()->get($id, $user_id, $is_admin);
        
        if ($res_user->count() <= 0) {
            throw new \Exception('error get user: ' . json_encode($id));
        }
        
        foreach ($res_user->toArray() as $user) {
            $user['roles'] = [];
            foreach ($this->getServiceRole()->getRoleByUser($user['id']) as $role) {
                $user['roles'][] = $role->getName();
            }
            $users[$user['id']] = $user;
        }
        
        if (is_array($id)) {
            foreach ($id as $i) {
                if (! isset($users[$i])) {
                    $users[$i] = null;
                }
            }
        }
        
        return (is_array($id)) ? $users : reset($users);
    }

    /**
     * Get User Id
     *
     * @invokable
     *
     * @param string $search
     * @param array  $exclude
     * @param array  $filter
     * @param int    $contact_state
     * @param int    $page_id
     * @param int    $post_id
     * @param array  $order
     * @param string $role
     * @param int    $conversation_id
     * @param string $page_type
     * @param int $unsent
     * @param string $is_pinned
     *
     * @return array
     */
    public function getListId($search = null, $exclude = null, $filter = null, $contact_state = null, $page_id = null, $post_id = null, $order = null, $role = null, $conversation_id = null, $page_type = null, $unsent = null, $is_pinned = null)
    {
        $identity = $this->getIdentity();
        if (null !== $exclude && ! is_array($exclude)) {
            $exclude = [$exclude];
        }
        
        $is_admin = $this->isStudnetAdmin();
        $mapper = $this->getMapper();
        $res_user = $mapper->usePaginator($filter)->getList($identity['id'], $is_admin, $post_id, $search, $page_id, $order, $exclude, $contact_state, $unsent, $role, $conversation_id, $page_type, null, $is_pinned);
        
        $users = [];
        foreach ($res_user as $m_user) {
            $users[] = $m_user->getId();
        }
        
        return (null === $filter) ? $users : [
            'list' => $users,
            'count' => $mapper->count()
        ];
    }
    
    /**
     * Get User Id
     *
     * @invokable
     *
     * @param array|string $email
     *
     * @return array
     */
    public function getListIdByEmail($email)
    {
        
        if(!is_array($email)) {
            $email = [$email];
        }
        
        if(count($email) === 0) {
            return null;
        }
        $users = [];
        foreach($email as $key => $e){
            $email[$key] = strtolower($e);
            $users[$e] = null; 
        }
        $identity = $this->getIdentity();
        $is_admin = (in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles']));
        $mapper = $this->getMapper();
        $res_user = $mapper->getList($identity['id'], $is_admin, null, null, null, null, null, null, null, null, null, null, $email);
        foreach ($res_user as $m_user) {
            if(array_key_exists(trim($m_user->getEmail()), $users)){
                $users[trim($m_user->getEmail())] = $m_user->getId();
            }
            if(!$m_user->getInitialEmail() instanceof IsNull 
                && array_key_exists(trim($m_user->getInitialEmail()), $users)){
                $users[trim($m_user->getInitialEmail())] = $m_user->getId();
            }
        }
        
        return $users;
    }

    /**
     * Delete User.
     *
     * @invokable
     *
     * @param int $id
     *
     * @return array
     */
    public function delete($id)
    {
        $ret = [];
        if (! is_array($id)) {
            $id = array(
                $id
            );
        }
        foreach ($id as $i) {
            
            $tmp_user = $this->getLite($i);
            if(!$this->getServicePage()->isAdmin($tmp_user->getOrganizationId())) {
                throw new JrpcException('Unauthorized operation user.delete', -38003);
            }
            $m_user = $this->getModel();
            $m_user->setId($i)->setDeletedDate((new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'))->setIsActive(0)->setLinkedinId(new IsNull());
            
            $ret[$i] = $this->getMapper()->update($m_user);
        }
        
        return $ret;
    }

    /**
     * @invokable
     *
     * @param string $token
     * @param string $uuid
     * @param string $package
     */
    public function registerFcm($token, $uuid, $package)
    {
        return $this->getServiceFcm()->register($uuid, $token, $package);
    }

    /**
     * Add School relation
     *
     * @invokable
     *
     * @param  int  $organization_id
     * @param  int  $user_id
     * @param  bool $default
     * @return NULL|int
     */
    public function addOrganization($organization_id, $user_id, $default = false)
    {
        $ret = null;
        if ($default === true) {
            $ret = $this->getMapper()->update(
                $this->getModel()
                    ->setId($user_id)
                    ->setOrganizationId($organization_id)
            );
        }
        
        return $ret;
    }

    public function removeOrganizationId($organization_id)
    {
        return $this->getMapper()->update(
            $this->getModel()
                ->setOrganizationId(new IsNull('organization_id')), [
            'organization_id' => $organization_id
                ]
        );
    }

    /**
     * sign In Password
     *
     * @invokable
     *
     * @param string $account_token
     * @param string $password
     */
    public function signIn($account_token, $password, $firstname = null, $lastname = null)
    {
        $m_registration = $this->getServicePreregistration()->get($account_token);
        if (false === $m_registration) {
            throw new \Exception('Account token not found.');
        }
        if (is_numeric($m_registration->getUserId())) {
            $this->getMapper()->update(
                $this->getModel()
                    ->setFirstname($firstname)
                    ->setLastname($lastname)
                    ->setIsActive(1)
                    ->setPassword(md5($password)), [
                'id' => $m_registration->getUserId()
                    ]
            );
            $user_id = $m_registration->getUserId();
        } else {
            $lastname = (!empty($lastname)) ? $lastname : ( (is_string($m_registration->getLastname())) ? $m_registration->getLastname() : null);
            $firstname = (!empty($firstname)) ? $firstname : ( (is_string($m_registration->getFirstname())) ? $m_registration->getFirstname() : null);
            
            $user_id = $this->_add(
                $firstname, 
                $lastname, 
                $m_registration->getEmail(), 
                null, 
                null, 
                null, 
                null, 
                $password, 
                null, 
                null, 
                (is_numeric($m_registration->getOrganizationId()) ? $m_registration->getOrganizationId() : null),
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                true
            );
        }
       
        $m_user = $this->getLite($user_id);
        $login = $this->login($m_user->getEmail(), $password);
        if(is_numeric($m_user->getOrganizationId())) {
            $this->getServicePageUser()->update($m_user->getOrganizationId(), $user_id, ModelPageUser::ROLE_USER, ModelPageUser::STATE_MEMBER);
        }
        $this->getServicePreregistration()->delete($account_token, $m_user->getId());
        
        return $login;
    }

    /**
     * @invokable
     *
     * @param string $code
     * @param string $account_token
     */
    public function linkedinSignIn($code, $account_token = null)
    {
        syslog(
            1, json_encode(
                [
                'code' => $code,
                'account_token' => $account_token,
                ]
            )
        );
        
        $identity = $this->getIdentity();
        $linkedin = $this->getServiceLinkedIn();
        $linkedin->init($code);
        $m_people = $linkedin->people();
        $linkedin_id = $m_people->getId();
        $login = false;
        if (empty($linkedin_id) || ! is_string($linkedin_id)) {
            throw new \Exception('Error LinkedIn Id');
        }
        $res_user = $this->getMapper()->select($this->getModel()->setDeletedDate(new IsNull())->setIsActive(1)->setLinkedinId($linkedin_id));
        if ($res_user->count() > 0) { // utilisateur existe on renvoie une session
            $m_user = $res_user->current();
            $login = $this->loginLinkedIn($linkedin_id);
        } else { // Si l'utilisateur n'existe pas
            if (null !== $account_token) { // SI pas connecté
                $m_registration = $this->getServicePreregistration()->get($account_token);
                if (false === $m_registration) {
                    throw new \Exception('Account token not found.');
                }
                $firstname = strlen($m_registration->getFirstname()) === 0 ? $m_people->getFirstname() : $m_registration->getFirstname();
                $lastname = strlen($m_registration->getLastname()) === 0   ? $m_people->getLastname() : $m_registration->getLastname();
                $avatar = null;
              
                $user_id = $m_registration->getUserId();
                if (is_numeric($user_id)) {
                    
                    syslog(
                        1, json_encode(
                            [
                            'code' => $code,
                            'account_token' => $account_token,
                            'user_id' => $user_id,
                            'type' => 'send ou lost password'
                            ]
                        )
                    );
                    $m_user = $this->getModel()->setId($user_id);
                    if($this->getMapper()->update($m_user->setIsActive(1)) > 0) {
                        if($m_user->getAvatar() === null  
                            && !empty($m_people->getPictureUrls()) && array_key_exists('values', $m_people->getPictureUrls())  
                            && count($m_people->getPictureUrls()['values']) > 0
                        ) {
                            $url = $m_people->getPictureUrls()['values']['0'];
                            $avatar = $this->getServiceLibrary()->upload($url, $firstname.' '.$lastname);
                        }
                        if($m_registration->getOrganizationId() !== null) {
                            
                            $this->getServicePageUser()->update($m_registration->getOrganizationId(), $user_id, ModelPageUser::ROLE_USER, ModelPageUser::STATE_MEMBER);
                        }
                        
                        $m_user->setFirstname($firstname)->setLastname($lastname)->setAvatar($avatar);
                    }
                    $this->getMapper()->update($m_user->setLinkedinId($linkedin_id));
                    $user_id = $m_registration->getUserId();
                } else {
                    $user_id = $this->_add($firstname, $lastname, $m_registration->getEmail(), null, null, null, null, null, null, null, (is_numeric($m_registration->getOrganizationId()) ? $m_registration->getOrganizationId() : null), $avatar);
                    $this->getMapper()->update($this->getModel()->setLinkedinId($linkedin_id), ['id' => $user_id]);
                    
                    syslog(
                        1, json_encode(
                            [
                            'code' => $code,
                            'account_token' => $account_token,
                            'user_id' => $user_id,
                            'type' => 'create compte'
                            ]
                        )
                    );
                }
                
                $m_user = $this->getLite($user_id);
                
                $login = $this->loginLinkedIn($linkedin_id);
                $this->getServicePreregistration()->delete($account_token, $m_user->getId());
            } else if(is_numeric($identity['id'])) {
                
                syslog(
                    1, json_encode(
                        [
                        'code' => $code,
                        'account_token' => $account_token,
                        'user_id' => $identity['id'],
                        'type' => 'Already connected'
                        ]
                    )
                );
                $m_user = $this->getModel()->setLinkedinId($linkedin_id);
                if((!isset($identity['avatar']) || $identity['avatar'] === null) && $m_people->getPictureUrls() !== null) {
                    $url = $m_people->getPictureUrls()['values']['0'];
                    $m_user->setAvatar($this->getServiceLibrary()->upload($url, $identity['firstname'].' '.$identity['lastname']));
                }
                $this->getMapper()->update($m_user, ['id' => $identity['id']]);
                $identity['has_linkedin'] = true;
                
                $login = $identity;
            } else {
                throw new \Exception('Error linkedinSignIn > no: $identity["id"] and no: $account_token');
            }
        }
        
        return $login;
    }
    
    /**
     * @invokable
     *
     * @param string $linkedin_id
     */
    public function linkedinLogIn($linkedin_id)
    {
        $res_user = $this->getMapper()->select($this->getModel()->setIsActive(1)->setLinkedinId($linkedin_id));
        if ($res_user->count() > 0) {
            $m_user = $res_user->current();
            return $this->loginLinkedIn($linkedin_id);
        } else {
            throw new \Exception('Error linkedinLogIn >'. $linkedin_id);
        }
    }
    
    /**
     * @invokable
     *
     * @param string $delay 
     */
    public function closeWelcome($delay = false)
    {
        $identity = $this->getIdentity();
        $res_user = $this->getMapper()->select($this->getModel()->setId($identity['id']));
        
        $datetime = (new DateTime('now', new DateTimeZone('UTC')))->modify('-4 hours');
        $welcome_delay = 1;
        if ($res_user->count() > 0) {
            $m_user = $res_user->current();
            $welcome_delay = $delay && !$m_user->getWelcomeDelay() instanceof IsNull ? $m_user->getWelcomeDelay() * 2 : 1;
            $this->getMapper()->update($this->getModel()
                ->setId($identity['id'])
                ->setWelcomeDate($datetime->format('Y-m-d H:i:s'))
                ->setWelcomeDelay($welcome_delay));
        }
        return $datetime->modify('+'.$welcome_delay.' days')->format('Y-m-d H:i:s');
    }

    /**
     * Get Service Preregistration
     *
     * @return \Application\Service\Preregistration
     */
    private function getServicePreregistration()
    {
        return $this->container->get('app_service_preregistration');
    }

    /**
     * Get Service LinkedIn
     *
     * @return \LinkedIn\Service\Api
     */
    private function getServiceLinkedIn()
    {
        return $this->container->get('linkedin.service');
    }

    /**
     * Get Service Event
     *
     * @return \Application\Service\Event
     */
    private function getServiceEvent()
    {
        return $this->container->get('app_service_event');
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

    /**
     * Get Service Role.
     *
     * @return \Application\Service\Role
     */
    private function getServiceRole()
    {
        return $this->container->get('app_service_role');
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
     * Get Storage if define in config.
     *
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function getCache()
    {
        $config = $this->container->get('config')['app-conf'];
        
        return $this->container->get($config['cache']);
    }

    /**
     * Get Service Mail.
     *
     * @return \Mail\Service\Mail
     */
    private function getServiceMail()
    {
        return $this->container->get('mail.service');
    }

    /**
     * Get Service Subscription
     *
     * @return \Application\Service\Subscription
     */
    private function getServiceSubscription()
    {
        return $this->container->get('app_service_subscription');
    }

    /**
     * Get Service Fcm
     *
     * @return \Application\Service\Fcm
     */
    private function getServiceFcm()
    {
        return $this->container->get('fcm');
    }

    /**
     * Get Service Address
     *
     * @return \Address\Service\Address
     */
    private function getServiceAddress()
    {
        return $this->container->get('addr_service_address');
    }

    /**
     * Get Service PageUser
     *
     * @return \Application\Service\PageUser
     */
    private function getServicePageUser()
    {
        return $this->container->get('app_service_page_user');
    }

    /**
     * Get Service Page
     *
     * @return \Application\Service\Page
     */
    private function getServicePage()
    {
        return $this->container->get('app_service_page');
    }

    /**
     * Get Service Library
     *
     * @return \Application\Service\Library
     */
    private function getServiceLibrary()
    {
        return $this->container->get('app_service_library');
    }
}
