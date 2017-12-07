<?php
/**
 * github.com/buse974/Dms (https://github.com/buse974/Dms).
 *
 * videoArchive
 */
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Plugin videoArchive
 */
class videoArchive extends AbstractPlugin
{

    /**
     * Option dms-conf
     *
     * @var \Application\Service\VideoArchive
     */
    protected $video_archive;

    /**
     * Constructor
     *
     * @param \Application\Service\VideoArchive $video_archive
     */
    public function __construct(\Application\Service\VideoArchive $video_archive)
    {
        $this->video_archive = $video_archive;
    }

    /**
     * check Status
     *
     * @param string $json
     */
    public function checkStatus($json)
    {
        return $this->video_archive->checkStatus(json_decode($json, true));
    }
}
