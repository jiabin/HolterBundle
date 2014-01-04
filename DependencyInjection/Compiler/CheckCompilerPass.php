<?php

namespace Jiabin\HolterBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CheckCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('holter.check_factory')) {
            return;
        }

        $definition = $container->getDefinition('holter.check_factory');

        foreach ($container->findTaggedServiceIds('holter.check') as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addCheck', array(new Reference($id)));
            }
        }
    }
}