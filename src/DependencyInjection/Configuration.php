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
                ->booleanNode('debug_logging_enabled')
                    ->defaultFalse()
                    ->info('Enable sanitized HTTP request/response debug logs in taler-php (requires a PSR-3 logger at DEBUG level).')
                ->end()
                ->variableNode('logger')
                    ->defaultNull()
                    ->info('PSR-3 logger service id, null to auto-use monolog.logger.taler when Monolog is installed, or false to disable logging.')
                    ->validate()
                        ->ifTrue(static fn (mixed $value): bool => $value !== null && !\is_string($value) && $value !== false)
                        ->thenInvalid('The logger option must be a service id string, null, or false.')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
