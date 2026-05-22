<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\DependencyInjection\Compiler;

use MirrorPS\TalerBundle\Factory\TalerClientFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Wires monolog.logger.taler after all extensions are loaded (Monolog is not
 * visible during TalerExtension::load() via hasExtension()).
 */
final class TalerLoggerCompilerPass implements CompilerPassInterface
{
    private const MONOLOG_LOGGER_SERVICE = 'monolog.logger.taler';
    private const AUTO_WIRE_PARAMETER = 'mirrorps_taler.auto_wire_logger';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter(self::AUTO_WIRE_PARAMETER) || !$container->getParameter(self::AUTO_WIRE_PARAMETER)) {
            return;
        }

        if (!$container->hasDefinition(TalerClientFactory::class)) {
            return;
        }

        $factoryDefinition = $container->getDefinition(TalerClientFactory::class);

        if (\count($factoryDefinition->getArguments()) > 1) {
            return;
        }

        if (!$container->hasDefinition(self::MONOLOG_LOGGER_SERVICE)) {
            return;
        }

        $factoryDefinition->addArgument(new Reference(self::MONOLOG_LOGGER_SERVICE));
    }
}
