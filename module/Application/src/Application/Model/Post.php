<?php

namespace Application\Model;

use Application\Model\Base\Post as BasePost;

class Post extends BasePost
{
    protected $docs;
    protected $last_date;
    protected $comments;
    protected $mentions;
    protected $nbr_comments;
    protected $is_liked;
    protected $nbr_likes;
    protected $nbr_views;
    protected $nbr_sharings;
    protected $user;
    protected $subscription;
    protected $count;
    protected $page;
    protected $parent;
    protected $origin;
    protected $image;

    public function exchangeArray(array &$data)
    {
        if ($this->isRepeatRelational()) {
            return;
        }

        $this->parent = $this->requireModel('app_model_post', $data, 'parent');
        $this->origin = $this->requireModel('app_model_post', $data, 'origin');
        $this->user = $this->requireModel('app_model_user', $data);
        $this->page = $this->requireModel('app_model_page', $data);

        parent::exchangeArray($data);
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

    public function setMentions($mentions)
    {
        $this->mentions = $mentions;

        return $this;
    }

    public function getMentions()
    {
        return $this->mentions;
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

    public function setNbrSharings($nbr_sharings)
    {
        $this->nbr_sharings = $nbr_sharings;

        return $this;
    }

    public function getNbrSharings()
    {
        return $this->nbr_sharings;
    }

    public function setNbrViews($nbr_views)
    {
        $this->nbr_views = $nbr_views;

        return $this;
    }

    public function getNbrViews()
    {
        return $this->nbr_views;
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



    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
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
