<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Activity
 */
namespace Application\Service; 

use Dal\Service\AbstractService;
use OneLogin\Saml2\Auth;
use JRpc\Json\Server\Exception\JrpcException;
use OneLogin\Saml2\Response;
use OneLogin\Saml2\Settings;
use OneLogin\Saml2\LogoutResponse;
use OneLogin\Saml2\Constants;
use OneLogin\Saml2\LogoutRequest;
use OneLogin\Saml2\Utils;

/**
 * Class Saml
 */
class Saml extends AbstractService
{
    protected $auth;
    
    /**
     * 
     */
    public function getArrSetting($organization_id)
    {
        $m_page = $this->getServicePage()->getLite($organization_id);
        $domaine = 'https://'.$m_page->getDomaine();
        
        return [
            'sp' => [
                'entityId' => $domaine,
                'assertionConsumerService' => [
                    'url' => $domaine.'/acs',
                ],
                'singleLogoutService' => [
                    'url' => $domaine.'/sls',
                ],
                'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
            ],
            'idp' => [
                'entityId' => $m_page->getSsoEntityId(),
                'singleSignOnService' => array (
                    'url' => $m_page->getSingleSignOnService(),
                ),
                'singleLogoutService' => array (
                    'url' => $m_page->getSingleLogoutService(),
                ),
                'x509cert' => $m_page->getSsoX509cert(),
            ],
        ];
    }
    
    /**
     * @invokable
     * 
     * @param int $organization_id
     * @param string $relaystate
     */
    public function login($organization_id, $relaystate = null)
    {
        $this->auth = new Auth($this->getArrSetting($organization_id));
        
        $url = $this->auth->login($relaystate, [], false, false, true);
        $uid = $this->auth->getLastRequestID();
        
        $this->getCache()->setItem($uid, $organization_id);
        return [
            'url' => $url,
            'request_id' => $uid
        ];
    }
    
    /**
     * @invokable
     */
    public function logout()
    {
        $id = $this->getServiceUser()->getIdentity()['id'];
        $m_user = $this->getServiceUser()->getLite($id);
        $this->auth = new Auth($this->getArrSetting($m_user->getOrganizationId()));
        
        $session_index = null;
        // nous avons besoin de la sessionIndex, que nous ne stockons pas car nous ne les gérons pas
        // du coup soit ca ne fonctionne pas soit ca suprime toute les sessions. 
        //$session_index = '_fausse_session';

        $url = $this->auth->logout(null, [], $m_user->getSsoUid(), $session_index, true /*,$nameIdFormat*/);
        return [
            'url' => $url,
            'logout_request_id' => $this->auth->getLastRequestID(),
        ];
    }
    
    /**
     * Valide du login
     * 
     * @invokable
     * 
     * @param string $request_id
     * @param string $SAMLResponse
     */
    public function acs($request_id, $SAMLResponse)
    {
        $organization_id = $this->getCache()->getItem($request_id);
        $this->getCache()->removeItem($request_id);

        $response = new Response(new Settings($this->getArrSetting($organization_id)), $SAMLResponse);

        if (!$response->isValid($request_id)) {
            throw new JrpcException($response->getErrorException());
        }
        
        $sso_uid = $response->getNameId();
        /*
         * Remarque : Pour répondre aux spécifications SAML, 
         * le NameID doit être unique, pseudo-aléatoire et 
         * il ne changera pas pour l’utilisateur au fil du temps 
         * (comme le numéro d’identification d’un employé). 
         */
        /*echo "\nName Id : \n";
        print_r($response->getNameId()); echo "\n";
        echo "\nName Id Format : \n";
        print_r($response->getNameIdFormat()); echo "\n";
        */
        
        /*
         * Attributs 
         */
        /*echo "\nAttributes : \n";
        print_r($response->getAttributes()); echo "\n"; //User attributes data.
        print_r($response->getAttributesWithFriendlyName()); echo "\n";
        */
        
        //ici on pourais utiliser les session du saml
        /*echo "\nSession Index : \n";
        print_r($response->getSessionIndex()); echo "\n";
       */
        
        /*echo "\nName Id Name Qualifier : \n";
        print_r($response->getNameIdNameQualifier()); echo "\n";
        echo "\nName Id SP Name Qualifier : \n";
        print_r($response->getNameIdSPNameQualifier()); echo "\n";
        echo "\nId : \n";
        print_r($response->getId()); echo "\n";
        echo "\nAssertion Id : \n";
        print_r($response->getAssertionId()); echo "\n";
        echo "\nAssertion Not On Or After : \n";
        print_r($response->getAssertionNotOnOrAfter()); echo "\n";
        */
        
        $m_user = $this->getServiceUser()->getLiteBySsoUid($sso_uid);
        
        //on créé l'utilisateur
        if ($m_user === false) {
            $this->getServiceUser()->_add(
                /*$firstname*/ null, /*$lastname*/ null, /*$email*/ null, /*$gender*/ null, /*$origin*/ null, /*$nationality*/ null, 
                /*$sis*/ null, /*$password*/ null, /*$birth_date*/ null, /*$position*/ null, $organization_id, /*$interest*/ null, /*$avatar*/ null,
                /*$roles*/ null, /*$timezone*/ null, /*$background*/ null, /*$nickname*/ null, /*$ambassador*/ null, /*$address*/ null,
                /*$active*/ true, /*$graduation_year*/ null, $sso_uid);
        }
        
        return $this->getServiceUser()->loginSaml($sso_uid);
    }
    
    /**
     * Valide du logout
     *
     * @invokable
     * 
     * @param string $request_id
     * @param string $SAMLResponse
     */
    public function sls($request_id, $SAMLResponse)
    {
        $id = $this->getServiceUser()->getIdentity()['id'];
        $m_user = $this->getServiceUser()->getLite($id);

        $logoutResponse = new LogoutResponse(new Settings($this->getArrSetting($m_user->getOrganizationId())), $SAMLResponse);

        if (!$logoutResponse->isValid($request_id, false) && $logoutResponse->getStatus() !== Constants::STATUS_SUCCESS) {
            throw new JrpcException('Unucessfully logged out');
            //$logoutResponse->getErrorException();
            //$this->_lastError = $logoutResponse->getError();
        }
           
        return $this->getServiceUser()->logout();
    }
    
    /**
     * logout request
     *
     * @invokable
     *
     * @param string $request_id
     * @param string $SAMLRequest
     */
    public function slsr($request_id, $SAMLRequest, $relaystate = null)
    {
        $id = $this->getServiceUser()->getIdentity()['id'];
        $m_user = $this->getServiceUser()->getLite($id);

        $settings = new Settings($this->getArrSetting($m_user->getOrganizationId()));
        $logoutRequest = new LogoutRequest($settings, $SAMLRequest);
  
        if (!$logoutRequest->isValid()) {
            throw new JrpcException('Unsucessfully logged out');
        }
        
        $inResponseTo = $logoutRequest->id;
        $responseBuilder = new LogoutResponse($settings);
        $responseBuilder->build($inResponseTo);
        $logoutResponse = $responseBuilder->getResponse();

        $parameters = ['SAMLResponse' => $logoutResponse];
        if (null !== $relaystate) {
            $parameters['RelayState'] = $relaystate;
        }
        
        $this->auth = new Auth($settings);
        $security = $settings->getSecurityData();
        if (isset($security['logoutResponseSigned']) && $security['logoutResponseSigned']) {
            $signature = $this->auth->buildResponseSignature($logoutResponse, isset($parameters['RelayState'])? $parameters['RelayState']: null, $security['signatureAlgorithm']);
            $parameters['SigAlg'] = $security['signatureAlgorithm'];
            $parameters['Signature'] = $signature;
        }
        
        $idpData = $settings->getIdPData();
        $this->getServiceUser()->logout();
        
        return (isset($idpData['singleLogoutService']) && isset($idpData['singleLogoutService']['url'])) ?
             Utils::redirect($idpData['singleLogoutService']['url'], $parameters, true) :
             true;
    }
    
    /**
     * Get Service User
     *
     * @return \Application\Service\User
     */
    private function getServiceUser()
    {
        return $this->container->get('app_service_user');
    }
    
    /**
     * Get Storage if define in config.
     *
     * @return \Zend\Cache\Storage\StorageInterface
     */
    private function getCache()
    {
        $config = $this->container->get('config')['app-conf'];
        
        return $this->container->get($config['cache']);
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
    
}
