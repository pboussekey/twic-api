<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class TaskShare extends AbstractModel
{
    protected $task_id;
    protected $user_id;

    protected $prefix = 'task_share';

    public function getTaskId()
    {
        return $this->task_id;
    }

    public function setTaskId($task_id)
    {
        $this->task_id = $task_id;

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
}
