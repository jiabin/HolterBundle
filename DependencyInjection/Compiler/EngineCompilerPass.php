<?php

namespace Jiabin\HolterBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class EngineCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('holter.manager')) {
            return;
        }

        $definition = $container->getDefinition('holter.manager');
        $taggedServices = $container->findTaggedServiceIds('holter.engine');
        foreach ($taggedServices as $id => $attributes) {
            $container->getDefinition($id)->addMethodCall('setId', array($id));
            $container->getDefinition($id)->addMethodCall('setManager', array(new Reference('holter.manager')));
            $definition->addMethodCall('addEngine', array(new Reference($id)));
        }
    }
}