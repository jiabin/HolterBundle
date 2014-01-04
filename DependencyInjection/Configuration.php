<?php

namespace Jiabin\HolterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('jiabin_Holter');

        $rootNode
            ->children()
                ->scalarNode('db_driver')
                    ->isRequired()
                    ->validate()
                    ->ifNotInArray(array('mongodb', 'orm'))
                        ->thenInvalid('Invalid database driver "%s"')
                    ->end()
                ->end()
                ->arrayNode('checks')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('label')->end()
                            ->scalarNode('icon')->end()
                            ->arrayNode('options')
                                ->prototype('variable')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
