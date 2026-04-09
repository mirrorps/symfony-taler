<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle;

use MirrorPS\TalerBundle\DependencyInjection\TalerExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class TalerBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return $this->extension ??= new TalerExtension();
    }
}
