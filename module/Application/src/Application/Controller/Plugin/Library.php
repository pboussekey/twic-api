<?php
/**
 * Library
 */
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Plugin Library
 */
class Library extends AbstractPlugin
{

    /**
     * @var \Application\Service\Library
     */
    protected $library;

    /**
     * Constructor
     *
     * @param \Application\Service\Library $library
     */
    public function __construct(\Application\Service\Library $library)
    {
        $this->library = $library;
    }
    
    public function updateBoxId($id, $box_id)
    {
        return $this->library->updateBoxId($id, $box_id);
    }
}
