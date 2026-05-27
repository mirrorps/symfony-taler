<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\DependencyInjection\Compiler;

use MirrorPS\TalerBundle\Factory\TalerClientFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Wires Symfony's psr18.http_client when available (requires framework.http_client
 * and psr/http-client). taler-php PSR discovery alone is insufficient without
 * additional PSR-17 factory packages (e.g. nyholm/psr7).
 */
final class TalerHttpClientCompilerPass implements CompilerPassInterface
{
    private const PSR18_HTTP_CLIENT_SERVICE = 'psr18.http_client';
    private const AUTO_WIRE_PARAMETER = 'mirrorps_taler.auto_wire_http_client';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter(self::AUTO_WIRE_PARAMETER) || !$container->getParameter(self::AUTO_WIRE_PARAMETER)) {
            return;
        }

        if (!$container->hasDefinition(TalerClientFactory::class)) {
            return;
        }

        $factoryDefinition = $container->getDefinition(TalerClientFactory::class);
        $arguments = $factoryDefinition->getArguments();

        if (isset($arguments[1]) && $arguments[1] instanceof Reference) {
            return;
        }

        if (!$container->hasDefinition(self::PSR18_HTTP_CLIENT_SERVICE)) {
            return;
        }

        $factoryDefinition->setArgument(1, new Reference(self::PSR18_HTTP_CLIENT_SERVICE));
    }
}
