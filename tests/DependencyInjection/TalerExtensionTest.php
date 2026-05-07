<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\DependencyInjection;

use MirrorPS\TalerBundle\DependencyInjection\TalerExtension;
use MirrorPS\TalerBundle\Factory\TalerClientFactory;
use MirrorPS\TalerBundle\Service\ConfigService;
use MirrorPS\TalerBundle\Service\ConfigServiceInterface;
use MirrorPS\TalerBundle\Service\InstanceService;
use MirrorPS\TalerBundle\Service\InstanceServiceInterface;
use MirrorPS\TalerBundle\Service\OrderService;
use MirrorPS\TalerBundle\Service\OrderServiceInterface;
use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TalerExtensionTest extends TestCase
{
    public function testServicesAreRegistered(): void
    {
        $container = new ContainerBuilder();
        $extension = new TalerExtension();

        $extension->load([
            [
                'base_url' => 'https://backend.demo.taler.net',
                'username' => 'user',
                'password' => 'pass',
                'instance' => 'default',
            ],
        ], $container);

        self::assertTrue($container->hasDefinition(TalerClientFactory::class));
        self::assertTrue($container->hasDefinition(Taler::class));
        self::assertTrue($container->hasAlias('taler'));
        self::assertTrue($container->hasDefinition(OrderService::class));
        self::assertTrue($container->hasAlias(OrderServiceInterface::class));
        self::assertTrue($container->hasDefinition(InstanceService::class));
        self::assertTrue($container->hasAlias(InstanceServiceInterface::class));
        self::assertTrue($container->hasDefinition(ConfigService::class));
        self::assertTrue($container->hasAlias(ConfigServiceInterface::class));
    }

    public function testTalerServiceUsesFactory(): void
    {
        $container = new ContainerBuilder();
        $extension = new TalerExtension();

        $extension->load([
            ['base_url' => 'https://backend.demo.taler.net'],
        ], $container);

        $definition = $container->getDefinition(Taler::class);
        $factory = $definition->getFactory();

        self::assertIsArray($factory);
        self::assertSame('create', $factory[1]);
    }
}
