<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use Taler\Api\Config\Dto\MerchantVersionResponse;

interface ConfigServiceInterface
{
    /**
     * @param array<string, string> $headers
     * @return MerchantVersionResponse|array<string, mixed>
     */
    public function getConfig(array $headers = []): MerchantVersionResponse|array;

    /**
     * @param array<string, string> $headers
     */
    public function getConfigAsync(array $headers = []): mixed;
}
