<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle;

use MirrorPS\TalerBundle\DependencyInjection\Compiler\TalerLoggerCompilerPass;
use MirrorPS\TalerBundle\DependencyInjection\TalerExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class TalerBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return $this->extension ??= new TalerExtension();
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(
            new TalerLoggerCompilerPass(),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            -20,
        );
    }
}
