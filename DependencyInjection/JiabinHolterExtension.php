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
        
        $this->addChecks($container, $config['checks']);
        $this->setDoctrine($container);
    }

    /**
     * Add checks
     * 
     * @param ContainerBuilder $container
     * @param array            $checks
     */
    private function addChecks(ContainerBuilder $container, $checks)
    {
        foreach ($checks as $name => $check) {
            $serviceId = 'holter.'.$name.'.check';
            
            $definition = new Definition($check['class'], array($name, $check['options']));
            $definition->addMethodCall('setCheckFactory', array(new Reference('holter.check_factory')));

            // Optional definition values
            $this->setOptionalDefinitionValues($definition, $check, array('label', 'icon'));
            
            $service = $container->setDefinition($serviceId, $definition);
            $service->addTag('holter.check');
        }
    }

    /**
     * Set optionalDefinitionValues
     * 
     * @param Definition $definition
     * @param array      $check
     * @param array      $fields
     */
    private function setOptionalDefinitionValues(Definition $definition, $check, $fields = array())
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $check)) {
                $definition->addMethodCall('set'.ucfirst($field), array($check['label']));
            }
        }
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
