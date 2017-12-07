<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class PostSubscription extends AbstractModel
{
    protected $id;
    protected $libelle;
    protected $post_id;
    protected $last_date;
    protected $action;
    protected $sub_post_id;
    protected $user_id;
    protected $data;

    protected $prefix = 'post_subscription';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getLibelle()
    {
        return $this->libelle;
    }

    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getPostId()
    {
        return $this->post_id;
    }

    public function setPostId($post_id)
    {
        $this->post_id = $post_id;

        return $this;
    }

    public function getLastDate()
    {
        return $this->last_date;
    }

    public function setLastDate($last_date)
    {
        $this->last_date = $last_date;

        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    public function getSubPostId()
    {
        return $this->sub_post_id;
    }

    public function setSubPostId($sub_post_id)
    {
        $this->sub_post_id = $sub_post_id;

        return $this;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
