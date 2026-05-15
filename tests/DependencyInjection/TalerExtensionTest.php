<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\DependencyInjection;

use MirrorPS\TalerBundle\DependencyInjection\TalerExtension;
use MirrorPS\TalerBundle\Factory\TalerClientFactory;
use MirrorPS\TalerBundle\Service\BankAccountService;
use MirrorPS\TalerBundle\Service\BankAccountServiceInterface;
use MirrorPS\TalerBundle\Service\ConfigService;
use MirrorPS\TalerBundle\Service\ConfigServiceInterface;
use MirrorPS\TalerBundle\Service\DonauCharityService;
use MirrorPS\TalerBundle\Service\DonauCharityServiceInterface;
use MirrorPS\TalerBundle\Service\InstanceService;
use MirrorPS\TalerBundle\Service\InstanceServiceInterface;
use MirrorPS\TalerBundle\Service\OtpDevicesService;
use MirrorPS\TalerBundle\Service\OtpDevicesServiceInterface;
use MirrorPS\TalerBundle\Service\OrderService;
use MirrorPS\TalerBundle\Service\OrderServiceInterface;
use MirrorPS\TalerBundle\Service\TemplatesService;
use MirrorPS\TalerBundle\Service\TemplatesServiceInterface;
use MirrorPS\TalerBundle\Service\TokenFamiliesService;
use MirrorPS\TalerBundle\Service\TokenFamiliesServiceInterface;
use MirrorPS\TalerBundle\Service\TwoFactorAuthService;
use MirrorPS\TalerBundle\Service\TwoFactorAuthServiceInterface;
use MirrorPS\TalerBundle\Service\WalletService;
use MirrorPS\TalerBundle\Service\WalletServiceInterface;
use MirrorPS\TalerBundle\Service\WebhooksService;
use MirrorPS\TalerBundle\Service\WebhooksServiceInterface;
use MirrorPS\TalerBundle\Service\WireTransfersService;
use MirrorPS\TalerBundle\Service\WireTransfersServiceInterface;
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
        self::assertTrue($container->hasDefinition(BankAccountService::class));
        self::assertTrue($container->hasAlias(BankAccountServiceInterface::class));
        self::assertTrue($container->hasDefinition(InstanceService::class));
        self::assertTrue($container->hasAlias(InstanceServiceInterface::class));
        self::assertTrue($container->hasDefinition(ConfigService::class));
        self::assertTrue($container->hasAlias(ConfigServiceInterface::class));
        self::assertTrue($container->hasDefinition(DonauCharityService::class));
        self::assertTrue($container->hasAlias(DonauCharityServiceInterface::class));
        self::assertTrue($container->hasDefinition(OtpDevicesService::class));
        self::assertTrue($container->hasAlias(OtpDevicesServiceInterface::class));
        self::assertTrue($container->hasDefinition(TemplatesService::class));
        self::assertTrue($container->hasAlias(TemplatesServiceInterface::class));
        self::assertTrue($container->hasDefinition(TokenFamiliesService::class));
        self::assertTrue($container->hasAlias(TokenFamiliesServiceInterface::class));
        self::assertTrue($container->hasDefinition(WalletService::class));
        self::assertTrue($container->hasAlias(WalletServiceInterface::class));
        self::assertTrue($container->hasDefinition(TwoFactorAuthService::class));
        self::assertTrue($container->hasAlias(TwoFactorAuthServiceInterface::class));
        self::assertTrue($container->hasDefinition(WebhooksService::class));
        self::assertTrue($container->hasAlias(WebhooksServiceInterface::class));
        self::assertTrue($container->hasDefinition(WireTransfersService::class));
        self::assertTrue($container->hasAlias(WireTransfersServiceInterface::class));
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
