<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\DependencyInjection;

use MirrorPS\TalerBundle\Factory\TalerClientFactory;
use MirrorPS\TalerBundle\Service\ConfigService;
use MirrorPS\TalerBundle\Service\ConfigServiceInterface;
use MirrorPS\TalerBundle\Service\InstanceService;
use MirrorPS\TalerBundle\Service\InstanceServiceInterface;
use MirrorPS\TalerBundle\Service\OrderService;
use MirrorPS\TalerBundle\Service\OrderServiceInterface;
use MirrorPS\TalerBundle\Taler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

final class TalerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $factoryDefinition = new Definition(TalerClientFactory::class, [$config]);
        $container->setDefinition(TalerClientFactory::class, $factoryDefinition);

        $talerDefinition = new Definition(Taler::class);
        $talerDefinition->setFactory([new Reference(TalerClientFactory::class), 'create']);
        $container->setDefinition(Taler::class, $talerDefinition);
        $container->setAlias('taler', Taler::class)->setPublic(true);

        $orderServiceDefinition = new Definition(OrderService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(OrderService::class, $orderServiceDefinition);
        $container->setAlias(OrderServiceInterface::class, OrderService::class);

        $instanceServiceDefinition = new Definition(InstanceService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(InstanceService::class, $instanceServiceDefinition);
        $container->setAlias(InstanceServiceInterface::class, InstanceService::class);

        $configServiceDefinition = new Definition(ConfigService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(ConfigService::class, $configServiceDefinition);
        $container->setAlias(ConfigServiceInterface::class, ConfigService::class);
    }
}
