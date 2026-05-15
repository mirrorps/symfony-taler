<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use MirrorPS\TalerBundle\Taler;
use Taler\Api\WireTransfers\Dto\GetTransfersRequest;
use Taler\Api\WireTransfers\Dto\TransfersList;

final class WireTransfersService implements WireTransfersServiceInterface
{
    public function __construct(private readonly Taler $taler)
    {
    }

    public function getTransfers(?GetTransfersRequest $request = null, array $headers = []): TransfersList|array
    {
        return $this->taler->wireTransfers()->getTransfers($request, $headers);
    }

    public function getTransfersAsync(?GetTransfersRequest $request = null, array $headers = []): mixed
    {
        return $this->taler->wireTransfers()->getTransfersAsync($request, $headers);
    }

    public function deleteTransfer(string $tid, array $headers = []): void
    {
        $this->taler->wireTransfers()->deleteTransfer($tid, $headers);
    }

    public function deleteTransferAsync(string $tid, array $headers = []): mixed
    {
        return $this->taler->wireTransfers()->deleteTransferAsync($tid, $headers);
    }
}
