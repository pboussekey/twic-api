<?php
/**
 * github.com/buse974/Dms (https://github.com/buse974/Dms).
 *
 * DmsServiceFactory
 */
namespace Application\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class DmsServiceFactory.
 */
class ConfFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return Conf
     *
     * @see \Zend\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Conf($container->get('config'));
    }
}
