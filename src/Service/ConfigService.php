<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use MirrorPS\TalerBundle\Taler;
use Taler\Api\Config\Dto\MerchantVersionResponse;

final class ConfigService implements ConfigServiceInterface
{
    public function __construct(private readonly Taler $taler)
    {
    }

    public function getConfig(array $headers = []): MerchantVersionResponse|array
    {
        return $this->taler->config()->getConfig($headers);
    }

    public function getConfigAsync(array $headers = []): mixed
    {
        return $this->taler->config()->getConfigAsync($headers);
    }
}
