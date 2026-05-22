<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\DependencyInjection;

use MirrorPS\TalerBundle\Factory\TalerClientFactory;
use MirrorPS\TalerBundle\Service\BankAccountService;
use MirrorPS\TalerBundle\Service\BankAccountServiceInterface;
use MirrorPS\TalerBundle\Service\ConfigService;
use MirrorPS\TalerBundle\Service\ConfigServiceInterface;
use MirrorPS\TalerBundle\Service\DonauCharityService;
use MirrorPS\TalerBundle\Service\DonauCharityServiceInterface;
use MirrorPS\TalerBundle\Service\InstanceService;
use MirrorPS\TalerBundle\Service\InstanceServiceInterface;
use MirrorPS\TalerBundle\Service\InventoryService;
use MirrorPS\TalerBundle\Service\InventoryServiceInterface;
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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;

final class TalerExtension extends Extension implements PrependExtensionInterface
{
    private const MONOLOG_CHANNEL = 'taler';

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('monolog')) {
            return;
        }

        $container->prependExtensionConfig('monolog', [
            'channels' => [self::MONOLOG_CHANNEL],
        ]);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $factoryArguments = [$config];
        $explicitLoggerReference = $this->resolveExplicitLoggerReference($config);
        if ($explicitLoggerReference !== null) {
            $factoryArguments[] = $explicitLoggerReference;
        }

        $container->setParameter(
            'mirrorps_taler.auto_wire_logger',
            ($config['logger'] ?? null) === null,
        );

        $factoryDefinition = new Definition(TalerClientFactory::class, $factoryArguments);
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

        $bankAccountServiceDefinition = new Definition(BankAccountService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(BankAccountService::class, $bankAccountServiceDefinition);
        $container->setAlias(BankAccountServiceInterface::class, BankAccountService::class);

        $instanceServiceDefinition = new Definition(InstanceService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(InstanceService::class, $instanceServiceDefinition);
        $container->setAlias(InstanceServiceInterface::class, InstanceService::class);

        $inventoryServiceDefinition = new Definition(InventoryService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(InventoryService::class, $inventoryServiceDefinition);
        $container->setAlias(InventoryServiceInterface::class, InventoryService::class);

        $configServiceDefinition = new Definition(ConfigService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(ConfigService::class, $configServiceDefinition);
        $container->setAlias(ConfigServiceInterface::class, ConfigService::class);

        $donauCharityServiceDefinition = new Definition(DonauCharityService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(DonauCharityService::class, $donauCharityServiceDefinition);
        $container->setAlias(DonauCharityServiceInterface::class, DonauCharityService::class);

        $otpDevicesServiceDefinition = new Definition(OtpDevicesService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(OtpDevicesService::class, $otpDevicesServiceDefinition);
        $container->setAlias(OtpDevicesServiceInterface::class, OtpDevicesService::class);

        $templatesServiceDefinition = new Definition(TemplatesService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(TemplatesService::class, $templatesServiceDefinition);
        $container->setAlias(TemplatesServiceInterface::class, TemplatesService::class);

        $tokenFamiliesServiceDefinition = new Definition(TokenFamiliesService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(TokenFamiliesService::class, $tokenFamiliesServiceDefinition);
        $container->setAlias(TokenFamiliesServiceInterface::class, TokenFamiliesService::class);

        $walletServiceDefinition = new Definition(WalletService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(WalletService::class, $walletServiceDefinition);
        $container->setAlias(WalletServiceInterface::class, WalletService::class);

        $twoFactorAuthServiceDefinition = new Definition(TwoFactorAuthService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(TwoFactorAuthService::class, $twoFactorAuthServiceDefinition);
        $container->setAlias(TwoFactorAuthServiceInterface::class, TwoFactorAuthService::class);

        $webhooksServiceDefinition = new Definition(WebhooksService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(WebhooksService::class, $webhooksServiceDefinition);
        $container->setAlias(WebhooksServiceInterface::class, WebhooksService::class);

        $wireTransfersServiceDefinition = new Definition(WireTransfersService::class, [
            new Reference(Taler::class),
        ]);
        $container->setDefinition(WireTransfersService::class, $wireTransfersServiceDefinition);
        $container->setAlias(WireTransfersServiceInterface::class, WireTransfersService::class);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function resolveExplicitLoggerReference(array $config): ?Reference
    {
        if (($config['logger'] ?? null) === false) {
            return null;
        }

        if (isset($config['logger']) && \is_string($config['logger']) && $config['logger'] !== '') {
            return new Reference($config['logger']);
        }

        return null;
    }
}
