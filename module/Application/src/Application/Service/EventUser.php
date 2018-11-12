<?php

namespace Application\Service;

use Dal\Service\AbstractService;
use Zend\Db\Sql\Predicate\IsNull;

class EventUser extends AbstractService
{

    function username($source){

        return  (isset($source['data']) ? $source['data']['firstname'] : $source['firstname']) .  " " .(isset($source['data']) ? $source['data']['lastname'] : $source['lastname']);
    }

    function limit($text, $length = 50){
        return strlen($text) > $length ? substr($text, $length).'...' : $text;
    }

    function getContent($data){
        if(!isset($data['content'])){
            return "";
        }
        else{
            $content = $data['content'];
            $mentions = [];
            $users = [];
            preg_match_all ( '/@{user:(\d+)}/', $content, $mentions );
            for ($i = 0; $i < count($mentions[0]); $i++) {
                $mention = $mentions[0][$i];
                $uid = $mentions[1][$i];
                if(!isset($users[$uid] )){
                    $users[$uid] = $this->getServiceUser()->getLite($uid);
                }
                if(false !== $users[$uid]){
                    $content = str_replace($mention, strtolower('<span class="mention">@'.$users[$uid]->getFirstname().$users[$uid]->getLastName()).'</span>', $content);
                    syslog(1, $mention. ' => <span class="mention">@'.strtolower($users[$uid]->getFirstname().$users[$uid]->getLastName()).'</span> '.$content);
                }
            }
            return " : &laquo;".$content."&raquo";
        }
    }

    function getText($source, $data){
        if($data['event'] === "connection.accept"){
            return "<b>" .$this->username($source). "</b> is now connected to you";
        }
        if($data['event'] === "post.create"){
          return (!$data['is_announcement'] ? ("<b>" .$this->username($source). "</b>") : ("<b>" . $this->limit($data['initial']['data']['page']['title']) . "</b>"))
           . ($data['is_in_page'] && $data['is_announcement'] !== $data['is_in_page'] ? (" just posted in <b>" . $this->limit($data['initial']['data']['target']['title']) . "</b>") : " just posted")
          . $this->getContent($data);
        }

        if($data['event'] === "post.com"){
          return "<b>" . $this->username($source) . "</b> "
           // REPLY OR COMMENT
           . ($data['is_reply'] ? ' replied' : ' commented')
           // "TO" FOR REPLY, "ON" FOR COMMENT, NOTHING IF THE USER COMMENT OR REPLY TO HIMSELF
           . ($data['has_announcement_parent'] ? "" : ($data['is_reply'] ? ' to' : ' on'))
           . ($data['on_himself'] && !$data['has_announcement_parent'] ? " their" : "")
           // "YOUR" IF YOU ARE THE OWNER OF THE COMMENTED OR REPLIED POST
           . ($data['is_comment'] && !$data['is_reply'] && $data['on_yours'] && !$data['is_announcement'] ? ' your post' : "")
           . ($data['is_reply'] && $data['on_yours'] && !$data['is_announcement'] ? " your comment" : "")
           // "PAGE NAME" IF THIS IS A COMMENT OF A PAGE'S POST
           . ($data['has_announcement_parent'] ? (" <b>" . $this->limit($data['parent']['data']['page']['title']) . "</b>'s") : "")
           // "USER NAME" IF THIS IS A REPLY/COMMENT TO AN USER'S COMMENT/POST
           . ($data['on_himself'] && $data['on_yours'] && $data['has_announcement_parent'] ? (" <b>" . $this->username($source) . "'s </b>") : "")
           // "USER NAME" IF THIS IS A COMMENT TO AN USER'S POST'S
           . ($data['on_yours']  && !$data['has_announcement_parent'] ? "" : (!$data['is_reply']  ? ' post' : ' comment'))
            // "IN PAGE NAME" IF THIS POST IS ON A PAGE FEED (BUT NOT FOR PAGE'S POSTS)
           . ($data['is_in_page']  ? (" in <b>" . $this->limit($data['origin']['data']['target']['title']) . "</b>") : "")
            // "POST CONTENT"
           . $this->getContent($data);
        }

        if($data['event'] === "post.share"){
            //"USER NAME" OR "PAGE NAME" FOR ANNOUNCEMENT
            return (!$data['is_announcement'] ? ("<b>" .$this->username($source) . "</b>") : ("<b>" . $this->limit($data['initial']['data']['page']['title']) . "</b>"))
             . " shared"
             //"USER NAME" OR "PAGE NAME" OF THE POST SHARED
             . (!$data['has_announcement_share'] && !$data['on_yours'] && ($data['is_announcement'] || !$data['on_himself']) ? (" <b>" . $this->username($data['shared']['user']) . "</b>'s post") : "")
             . ($data['on_yours'] && !$data['has_announcement_share'] ? ' your post' : '')
             . ($data['on_himself'] && !$data['has_announcement_share'] && !$data['is_announcement'] ? ' their post' : '')
             . ($data['is_announcement'] && $data['has_announcement_share'] === $data['is_announcement']  ? ' their post' : '')
             . ($data['has_announcement_share']  ? (" <b>" . $this->limit($data['shared']['page']['title']) . "</b>'s post") : "")
             // "IN PAGE NAME" IF IT'S NOT AN ANNOUNCEMENT
             . ($data['is_in_page']  ? (" in <b>" . $this->limit($data['initial']['data']['target']['title']) . "</b>") : "")
              // "POST CONTENT"
             . $this->getContent($data);
        }

        if($data['event'] === "page.member"){
            return "<b>" .$this->username($source) .  "</b> enrolled you in <b>" .$data['target']['title'] . "</b>";
        }

        if($data['event'] === "page.invited"){
            return "<b>" . $this->username($source) .  "</b> invited you to join <b>" .$data['target']['title'] . "</b>";
        }

        if($data['event'] === "page.pending"){
            return "<b>" .$this->username($source) .  "</b> requested to join <b>" .$data['target']['title'] . "</b>";
        }

        if($data['event'] === "post.like"){
            //"USER NAME"
            return "<b>"  .$this->username($source) . "</b> liked"
            // "YOUR" IF THIS YOUR POST OR YOUR COMMENT AND IF IT'S NOT AN ANNOUNCEMENT
             . ($data['on_yours'] ? " your" : "")
             // "USER NAME" IF IT'S NOT AN ANNOUNCEMENT OR ONE OF YOUR POST/COMMENT AND IF THE USER IS NOT LIKING HIMSELF
             . (!$data['on_himself'] && !$data['on_yours'] && !$data['is_announcement']? (" <b>" . $this->username($data['initial']['data']['user']). "</b>'s") : "")
             . ($data['on_himself'] && !$data['is_announcement'] ? " their" : "")
             //"PAGE NAME" IF THE POST IS AN ANNOUNCEMENT
             . ($data['is_announcement']  ? (" <b>" . $this->limit($data['initial']['data']['page']['title']) . "</b>'s") : "")
             // IS IT A POST/COMMENT OR REPLY
             . ($data['is_reply'] ? ' reply' : ($data['is_comment'] ? ' comment' : ' post'))
             // IN "PAGE NAME" IF IT'S ON A PAGE
             . ($data['is_in_page'] && isset($data['initial']['data']['target'])  ? (" in <b>" . $this->limit($data['initial']['data']['target']['title']) . "</b>") : "")
             . ($data['is_in_page'] && isset($data['parent']) && isset($data['parent']['data']['target'])  ? (" in <b>" . $this->limit($data['parent']['data']['target']['title']). "</b>") : "")
             . ($data['is_in_page'] && isset($data['origin']) && isset($data['origin']['data']['target'])  ? (" in <b>" . $this->limit($data['origin']['data']['target']['title']) . "</b>") : "")
             // "POST CONTENT"
            . $this->getContent($data);
        }
        if($data['event'] === "post.tag"){
            // "USER NAME" OR "PAGE NAME"
            return "<b>" .(!$data['is_announcement'] ?  $this->username($source) : $data['initial']['data']['page']['title']) . "</b>"
             . " mentionned you in a"
             . ($data['is_reply'] ? ' reply' : '')
             . (!$data['is_reply'] && $data['is_comment'] ? ' comment' : '')
             . (!$data['is_comment'] ? ' post' : '')
             // IN "PAGE NAME"
             . ($data['is_in_page'] && isset($data['initial']['data']['target'])  ? (" in <b>" . $this->limit($data['initial']['data']['target']['title']) . "</b>") : "")
             . ($data['is_in_page'] && isset($data['parent']) && isset($data['parent']['data']['target'])  ? (" in <b>" . $this->limit($data['parent']['data']['target']['title']) . "</b>") : "")
             . ($data['is_in_page'] && isset($data['origin']) && isset($data['origin']['data']['target'])  ? (" in <b>" . $this->limit($data['origin']['data']['target']['title']) . "</b>") : "")
              // "POST CONTENT"
             . $this->getContent($data);
        }
        if($data['event'] === "item.publish"){
            return "<b>" . $this->username($source) . "</b> published a new item"
            . (isset($data['page']) ? (" in <b>" . $this->limit($data['page']['title']) . "</b>") : " in one of your course");
        }
        if($data['event'] === "item.update"){
            return "<b>" .  $this->username($source) . "</b> updated an item"
            . (isset($data['page']) ? (" in <b>" . $this->limit($data['page']['title']) . "</b>") : " in one of your course");
        }
        if($data['event'] === "page.doc"){
            return "<b>" .  $this->username($source) . "</b> added a new material"
            . (isset($data['page']) ? (" in <b>" . $this->limit($data['page']['title']) . "</b>") : " in one of your course");
        }
        return null;
    }

    /**
     * Get events list for current user.
     *
     * @param array|int $event_id
     *
     *
     * @return array
     */
    public function add($event_id, $user_id, $source = null, $data = null){
        $ret = 0;
        $m_event_user = $this->getModel()
            ->setUserId($user_id)
            ->setEventId($event_id);

        $res_event_user = $this->getMapper()->select($m_event_user);
        if($res_event_user->count() === 0){
            if(null !== $data){
              $data['is_comment'] = (isset($data['parent']) && $data['parent']['data']['id'] !== $data['initial']['data']['id']) ;
              $data['is_reply'] =  (isset($data['parent']) && isset($data['origin']) && !empty($data['parent']['data']['post_id']) &&   $data['parent']['data']['post_id'] !== $data['origin']['data']['post_id'] );
              $data['is_announcement'] = isset($data['initial']['data']['page']) ?  $data['initial']['data']['page']['id'] : null;
              $data['has_announcement_parent'] = isset($data['parent']) && isset($data['parent']['data']['page']) ? $data['parent']['data']['page']['id'] : null;
              $data['has_announcement_origin'] = isset($data['origin']) && isset($data['origin']['data']['page']) ? $data['origin']['data']['page']['id'] : null;
              $data['has_announcement_share'] = isset($data['shared']) && isset($data['shared']['page']) ? $data['shared']['page']['id'] : null;
              $data['is_in_page'] = isset($data['initial']['data']['target']) || (isset($data['parent']) && isset($data['parent']['data']['target'])) || (isset($data['origin']) && isset($data['origin']['data']['target']));
              $ntf_source = $data['event'] === 'post.like' || $data['event'] === 'post.create' || $data['event'] === 'post.tag' ? $source : $data['initial']['data']['user'];
              $ntf_object = $data['event'] === 'post.like' || $data['event'] === 'post.create' || $data['event'] === 'post.tag' ? $data['initial']['data'] : ($data['event'] === 'post.share' ? $data['shared'] : $data['parent']['data']);
              $data['on_himself'] = $ntf_source['id'] === $user_id;
              $data['on_yours'] =  isset($ntf_object['user']) && $ntf_object['user']['id'] === $user_id;
              $data['content'] =  $data['initial']['data']['content'];
              $m_event_user->setText($this->getText($source, $data));
              $m_event_user->setPicture( isset($data['initial']['data']['page']) ? $data['initial']['data']['page']['logo'] : $source['data']['avatar']) ;
            }
            $this->getMapper()->insert($m_event_user);
            $ret++;
        }

        return $ret;
    }

    /**
     * Get events list for current user.
     *
     * @param array|int $event_id
     *
     *
     * @return array
     */
    public function read($event_id){
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        $ret = 0;
        if(null !== $event_id){
            foreach($event_id as $eid){
                $m_event_user = $this->getModel()
                    ->setUserId($user_id)
                    ->setEventId($eid);
                $res_event_user = $this->getMapper()->select($m_event_user);
                if($res_event_user->count() === 0){
                    $this->getMapper()->insert($m_event_user->setReadDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s')));
                    $ret++;
                }
                else if($res_event_user->current()->getReadDate() instanceof IsNull){
                    $this->getMapper()->update($m_event_user->setReadDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s')));
                    $ret++;
                }
            }
        }
        else{
            return $this->getMapper()->update($this->getModel()->setReadDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s')),
                  ['user_id' => $user_id, new IsNull('read_date')]);
        }


        return $ret;
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
