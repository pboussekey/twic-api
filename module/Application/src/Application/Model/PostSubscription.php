<?php

namespace Application\Model;

use Application\Model\Base\PostSubscription as BasePostSubscription;

class PostSubscription extends BasePostSubscription
{
    const ACTION_CREATE='create';
    const ACTION_UPDATE='update';
    const ACTION_COM='com';
    const ACTION_LIKE='like';
    const ACTION_TAG='tag';
}
