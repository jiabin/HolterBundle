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
        $loader->load('engines.xml');
        $loader->load('doctrine/' . $config['db_driver'] . '.xml');

        $this->setDoctrine($container);
    }

    /**
     * Set doctrine
     *
     * @param ContainerBuilder $container
     */
    private function setDoctrine(ContainerBuilder $container)
    {
        $om = $container->getParameter('holter.doctrine_object_manager');

        $holterManager = $container->getDefinition('holter.manager');
        $holterManager->replaceArgument(0, new Reference($om));

        $eventSubscriber = $container->getDefinition('holter.event_subscriber');
        $eventSubscriber->replaceArgument(0, new Reference($om));

        // $checkFactory = $container->getDefinition('holter.check_factory');
        // $checkFactory->replaceArgument(0, new Reference($om));
    }
}
