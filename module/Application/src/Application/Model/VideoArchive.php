<?php

namespace Application\Model;

use Application\Model\Base\VideoArchive as BaseVideoArchive;

class VideoArchive extends BaseVideoArchive
{
    const FINISHED = 'finished';
    const ONGOING = 'ongoing';
    const NOTSTARTED = 'notstarted';

    const ARV_AVAILABLE = 'available';
    const ARV_EXPIRED = 'expired';
    const ARV_FAILED = 'failed';
    const ARV_STARTED = 'started';
    const ARV_STOPPED = 'stopped';
    const ARV_UPLOAD = 'uploaded';
    const ARV_SKIPPED = 'skipped';
}
