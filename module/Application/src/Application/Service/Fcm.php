<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Assignment
 */
namespace Application\Service;

use Dal\Service\AbstractService;
use ZendService\Google\Gcm\Message as GcmMessage;

/**
 * Class Assignment
 */
class Fcm extends AbstractService
{
    const PREFIX='sess_';
    const ACTIVITY='FCM_PLUGIN_ACTIVITY';
    const PACKAGE_TWIC_MESSENGER='com.twic.messenger';
    const PACKAGE_TWIC_APP='com.thestudnet.app';

    /**
     * Fcm Client
     *
     * @var \ZendService\Google\Gcm\Client
     */
    protected $fcm_client;

    /**
     * Service Session
     *
     * @var \Application\Service\Session
     */
    protected $session;

    /**
     * Token user
     *
     * @var string
     */
    protected $token;

    /**
     *
     * @param \Application\Service\Session   $session
     * @param \ZendService\Google\Gcm\Client $fcm_client
     * @param string                         $token
     */
    public function __construct($session, $fcm_client, $token)
    {
        $this->setToken($token);
        $this->session = $session;
        $this->fcm_client = $fcm_client;
    }

    public function register($uuid, $registration_id, $package = null)
    {
        $res_session = $this->session->get($uuid);
        foreach ($res_session as $m_session) {
            // if c un autre uuid que moi on suprime la session puis le champ bdd
            if ($this->token !== $m_session->getToken()) {
                $this->session->delete(null, $m_session->getToken());
            }
        }
        return $this->session->update($this->token, $uuid, $registration_id, $package);
    }

    public function send($to, $data, $notification = null, $package = null)
    {
        if (null !== $notification && empty($notification->getClickAction())) {
            $notification->setClickAction(self::ACTIVITY);
        }

        $register_ids = [];
        $res_session = $this->session->get(null, $to, $package);

        foreach ($res_session as $m_session) {
            $register_ids[] = $m_session->getRegistrationId();
        }


        $nbTo = count($register_ids);
        if ($nbTo > 0) {
            $gcm_message = new GcmMessage();
            $gcm_message->setNotification($notification)
                ->setPriority("high")
                ->setData($data?:[]);

            if ($nbTo > 1) {
                $gcm_message->setRegistrationIds($register_ids);
            } else {
                $gcm_message->setTo(current($register_ids));
            }

            try {
                $response = $this->fcm_client->send($gcm_message);


                // LOG FUKING FCM ERROR ... HELPING FUTURE DEBUG...
                foreach ( $response->getResults() as $token => $result ){
                    if(!empty($result['error']) ) {
                    }
                }
                return $response;
            } catch (\Exception $e) {
                syslog(1, "error fcm: ".$e->getMessage());
            }
        }

        return false;
    }

    /**
     * Set Client Fcm
     *
     * @param  \ZendService\Google\Gcm\Client $fcm_client
     * @return \Application\Service\Fcm
     */
    private function setServiceFcmClient($fcm_client)
    {
        $this->fcm_client = $fcm_client;

        return $this;
    }

    /**
     * Set Service Session
     *
     * @param  \Application\Service\Session $session
     * @return \Application\Service\Fcm
     */
    private function setServiceSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Set Token User
     *
     * @param  string $token
     * @return \Application\Service\Fcm
     */
    private function setToken($token)
    {
        $this->token = self::PREFIX.$token;

        return $this;
    }
}
