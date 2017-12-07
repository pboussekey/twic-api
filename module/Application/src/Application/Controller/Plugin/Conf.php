<?php
/**
 * github.com/buse974/Dms (https://github.com/buse974/Dms).
 *
 * Dms
 */
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Plugin Dms.
 */
class Conf extends AbstractPlugin
{
    /**
     * Option dms-conf
     *
     * @var array
     */
    protected $options;
    
    /**
     * Constructor
     *
     * @param array      $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }
    
    /**
     * Get Version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->options['version'];
    }
    
    /**
     * Get build commit
     *
     * @return string
     */
    public function getAll()
    {
        return $this->options;
    }
    
    /**
     * Get build commit
     *
     * @return string
     */
    public function getBuildCommit()
    {
        return $this->options['build-commit'];
    }
}
