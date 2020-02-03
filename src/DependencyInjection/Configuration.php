<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\ExecuteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Configuration define the config structure accepted by the bundle.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('sculpin_execute');
        $root = $builder->getRootNode();

        $root
            ->children()
               ->arrayNode('environment')
                   ->useAttributeAsKey('name', false)
                   ->arrayPrototype()
                       ->children()
                           ->scalarNode('name')->end()
                           ->scalarNode('value')->end()
                       ->end()
                   ->end()
               ->end()
           ->end();

        return $builder;
    }
}
