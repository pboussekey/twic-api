<?php

namespace Application\Model;

use Application\Model\Base\PageUser as BasePageUser;

class PageUser extends BasePageUser
{
    const ROLE_ADMIN='admin';
    const ROLE_USER='user';

    const STATE_PENDING='pending';
    const STATE_MEMBER='member';
    const STATE_INVITED='invited';
    const STATE_REJECTED='rejected';
}
