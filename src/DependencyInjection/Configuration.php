<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('taler');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('base_url')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->info('The Taler merchant backend URL.')
                ->end()
                ->scalarNode('username')
                    ->defaultNull()
                    ->info('Username for credential-based authentication.')
                ->end()
                ->scalarNode('password')
                    ->defaultNull()
                    ->info('Password for credential-based authentication.')
                ->end()
                ->scalarNode('instance')
                    ->defaultNull()
                    ->info('The merchant backend instance ID.')
                ->end()
                ->scalarNode('token')
                    ->defaultNull()
                    ->info('Pre-existing auth token. Takes precedence over username/password.')
                ->end()
                ->scalarNode('scope')
                    ->defaultNull()
                    ->info('Token permission scope (readonly, write, all, order-simple, order-pos, order-mgmt, order-full).')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
