<?php

namespace Jiabin\HolterBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class JiabinHolterExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('doctrine/' . $config['db_driver'] . '.xml');

        $checkClasses = array_merge($config['check_classes'], $this->getDefaultCheckClasses());
        
        $this->addCheckClasses($container, $checkClasses);
        $this->setDoctrine($container);
    }

    /**
     * Get default check classes
     * 
     * @return array
     */
    private function getDefaultCheckClasses()
    {
        return array(
            'Jiabin\HolterBundle\Check\HttpCheck',
            'Jiabin\HolterBundle\Check\MongoCheck'
        );
    }

    /**
     * Add checks
     * 
     * @param ContainerBuilder $container
     * @param array            $checkClasses
     */
    private function addCheckClasses(ContainerBuilder $container, $checkClasses)
    {
        $checkFactory = $container->getDefinition('holter.check_factory');
        foreach ($checkClasses as $class) {
            $type = $class::$type;
            $checkFactory->addMethodCall('addType', array($type, $class));
        }

        // Load checks
        $checkFactory->addMethodCall('loadChecks', array());
    }

    /**
     * Set doctrine
     * 
     * @param ContainerBuilder $container
     */
    private function setDoctrine(ContainerBuilder $container)
    {
        $om = $container->getParameter('holter.doctrine_object_manager');

        $eventSubscriber = $container->getDefinition('holter.event_subscriber');
        $eventSubscriber->replaceArgument(0, new Reference($om));

        $checkFactory = $container->getDefinition('holter.check_factory');
        $checkFactory->replaceArgument(0, new Reference($om));
    }
}
