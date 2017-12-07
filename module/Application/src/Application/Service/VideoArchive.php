<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Archive Video
 */
namespace Application\Service;

use Dal\Service\AbstractService;
use Application\Model\VideoArchive as CVF;

/**
 * Class VideoArchive.
 */
class VideoArchive extends AbstractService
{

    /**
     * Get Video.
     *
     * @param int $id
     *
     * @return \Application\Model\VideoArchive
     */
    public function get($id)
    {
        $res_videoconf_archive = $this->getMapper()->get($id);
        if ($res_videoconf_archive->count() <= 0) {
            throw new \Exception('video_conf');
        }

        return $res_videoconf_archive->current();
    }

    /**
     * Start record video conf
     *
     * @invokable
     *
     * @param int $conversation_id
     *
     * @return array
     */
    public function startRecord($conversation_id)
    {
        try {
            $this->stopRecord($conversation_id);
        } catch (\Exception $e) {
            // pas de video
        }
        
        $m_conversation = $this->getServiceConversation()->getLite($conversation_id);

        $arr_archive = json_decode($this->getServiceZOpenTok()->startArchive($m_conversation->getToken()), true);
        if ($arr_archive['status'] == 'started') {
            $this->add($m_conversation->getId(), $arr_archive['id']);
        }

        return $arr_archive;
    }

    /**
     * Stop record video conf.
     *
     * @invokable
     *
     * @param int $conversation_id
     *
     * @return mixed
     */
    public function stopRecord($conversation_id)
    {
        $res_video_archive = $this->getMapper()->getLastArchiveId($conversation_id);
        if ($res_video_archive->count() <= 0) {
            throw new \Exception('no video with conversation: '.$conversation_id);
        }
        $m_video_archive = $res_video_archive->current();

        $ret = $this->getServiceZOpenTok()->stopArchive($m_video_archive->getArchiveToken());

        $this->updateByArchiveToken($m_video_archive->getArchiveToken(), $ret['status']);
        
        return $ret;
    }

    /**
     * Update Status Video.
     *
     * @param string $token
     * @param string $status
     * @param int    $duration
     * @param string $link
     *
     * @return int
     */
    public function updateByArchiveToken($archive_token, $status, $duration = null, $link = null)
    {
        $m_video_archive = $this->getModel();
        $m_video_archive->setArchiveDuration($duration)
            ->setArchiveStatus($status)
            ->setArchiveLink($link);

        return $this->getMapper()->update($m_video_archive, ['archive_token' => $archive_token]);
    }

    /**
     * Valide the video transfer
     *
     * @param array $json
     */
    public function checkStatus($json)
    {
        $ret = false;
        if ($json['status'] == 'uploaded') {
            $ret = $this->updateByArchiveToken($json['id'], CVF::ARV_AVAILABLE, null, $json['link']);
            if ($ret) {
                $m_video_archive = $this->getMapper()->select($this->getModel()->setArchiveToken($json['id']))->current();
                $m_conversation = $this->getServiceConversation()->getLite($m_video_archive->getConversationId());

                $m_user = $this->getServiceConversationUser()->getListUserIdByConversation($m_conversation->getId());
                $miid = [];
                foreach ($m_user as $u_id) {
                    $miid[] = 'M'.$u_id;
                }

                $this->getServicePost()->addSys(
                    'VCONV'.$m_conversation->getId(),
                    '',
                    [
                    'conversation' => $m_conversation->getId(),
                    'link' => $json['link']
                    ],
                    'create',
                    $miid/*sub*/,
                    null/*parent*/,
                    null/*page*/,
                    null/*user*/,
                    'video'
                );
            }
        }

        return $ret;
    }

    /**
     * Add Video.
     *
     * @param int    $conversation
     * @param string $token
     *
     * @return int
     */
    public function add($conversation_id, $token)
    {
        $m_video_archive = $this->getModel();
        $m_video_archive->setConversationId($conversation_id)
            ->setArchiveToken($token)
            ->setArchiveStatus(CVF::ARV_STARTED)
            ->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));

        $this->getMapper()->insert($m_video_archive);

        return $this->getMapper()->getLastInsertValue();
    }

    /**
     * Get Service Conversation.
     *
     * @return \Application\Service\Conversation
     */
    private function getServiceConversation()
    {
        return $this->container->get('app_service_conversation');
    }
    
       /**
     * Get Service ConversationUser.
     *
     * @return \Application\Service\ConversationUser
     */
    private function getServiceConversationUser()
    {
        return $this->container->get('app_service_conversation_user');
    }

    /**
     * Get Service OpenTok.
     *
     * @return \ZOpenTok\Service\OpenTok
     */
    private function getServiceZOpenTok()
    {
        return $this->container->get('opentok.service');
    }

    /**
     * Get Service Post
     *
     * @return \Application\Service\Post
     */
    private function getServicePost()
    {
        return $this->container->get('app_service_post');
    }

    /**
     * Get Service User.
     *
     * @return \Application\Service\User
     */
    private function getServiceUser()
    {
        return $this->container->get('app_service_user');
    }
}
