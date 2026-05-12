<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use MirrorPS\TalerBundle\Taler;
use Taler\Api\Wallet\Dto\StatusGotoResponse;
use Taler\Api\Wallet\Dto\StatusPaidResponse;
use Taler\Api\Wallet\Dto\StatusUnpaidResponse;

final class WalletService implements WalletServiceInterface
{
    public function __construct(
        private readonly Taler $taler,
    ) {
    }

    public function getOrder(string $orderId, array $params = [], array $headers = []): StatusPaidResponse|StatusGotoResponse|StatusUnpaidResponse
    {
        return $this->taler->wallet()->getOrder($orderId, $params, $headers);
    }

    public function getOrderAsync(string $orderId, array $params = [], array $headers = []): mixed
    {
        return $this->taler->wallet()->getOrderAsync($orderId, $params, $headers);
    }
}
