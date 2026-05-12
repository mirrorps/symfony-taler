<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use Taler\Api\Wallet\Dto\StatusGotoResponse;
use Taler\Api\Wallet\Dto\StatusPaidResponse;
use Taler\Api\Wallet\Dto\StatusUnpaidResponse;

interface WalletServiceInterface
{
    /**
     * Public wallet-facing order status (paid, goto reorder URL, or unpaid with pay URI).
     *
     * @param array<string, string> $params  Query string parameters for the GET request
     * @param array<string, string> $headers Optional HTTP headers
     *
     * @return StatusPaidResponse|StatusGotoResponse|StatusUnpaidResponse
     */
    public function getOrder(string $orderId, array $params = [], array $headers = []): StatusPaidResponse|StatusGotoResponse|StatusUnpaidResponse;

    /**
     * @param array<string, string> $params  Query string parameters for the GET request
     * @param array<string, string> $headers Optional HTTP headers
     */
    public function getOrderAsync(string $orderId, array $params = [], array $headers = []): mixed;
}
