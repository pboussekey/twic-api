<?php

namespace Application\Model;

use Application\Model\Base\ItemUser as BaseItemUser;

class ItemUser extends BaseItemUser
{
    protected $submission;
    protected $group;

    public function exchangeArray(array &$data)
    {
        parent::exchangeArray($data);

        $this->submission = $this->requireModel('app_model_submission', $data);
        $this->group = $this->requireModel('app_model_group', $data);
    }

    /**
     * Get the value of Submission
     *
     * @return mixed
     */
    public function getSubmission()
    {
        return $this->submission;
    }

    /**
     * Set the value of Submission
     *
     * @param mixed submission
     *
     * @return self
     */
    public function setSubmission($submission)
    {
        $this->submission = $submission;

        return $this;
    }


    /**
     * Get the value of Group
     *
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set the value of Group
     *
     * @param mixed group
     *
     * @return self
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }
}
