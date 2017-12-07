<?php

namespace Application\Model;

use Application\Model\Base\Post as BasePost;

class Post extends BasePost
{
    protected $docs;
    protected $last_date;
    protected $comments;
    protected $nbr_comments;
    protected $is_liked;
    protected $nbr_likes;
    protected $user;
    protected $subscription;
    protected $count;
    
    public function exchangeArray(array &$data)
    {
        if ($this->isRepeatRelational()) {
            return;
        }

        parent::exchangeArray($data);

        $this->user = $this->requireModel('app_model_user', $data);
    }

    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;

        return $this;
    }

    public function getSubscription()
    {
        return $this->subscription;
    }

    public function setDocs($docs)
    {
        $this->docs = $docs;

        return $this;
    }

    public function getDocs()
    {
        return $this->docs;
    }

    public function setLastDate($last_date)
    {
        $this->last_date = $last_date;

        return $this;
    }

    public function getLastDate()
    {
        return $this->last_date;
    }

    public function setNbrComments($nbr_comments)
    {
        $this->nbr_comments = $nbr_comments;

        return $this;
    }

    public function getNbrComments()
    {
        return $this->nbr_comments;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function setNbrLikes($nbr_likes)
    {
        $this->nbr_likes = $nbr_likes;

        return $this;
    }

    public function getNbrLikes()
    {
        return $this->nbr_likes;
    }

    public function setIsLiked($is_liked)
    {
        $this->is_liked = $is_liked;

        return $this;
    }

    public function getIsLiked()
    {
        return $this->is_liked;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }
    
    
    /**
     * Get count
     *
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set the value of Count
     *
     * @param mixed count
     *
     * @return self
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }
}
