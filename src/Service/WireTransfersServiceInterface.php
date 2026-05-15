<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use Taler\Api\WireTransfers\Dto\GetTransfersRequest;
use Taler\Api\WireTransfers\Dto\TransfersList;

interface WireTransfersServiceInterface
{
    /**
     * @param GetTransfersRequest|null $request
     * @param array<string, string> $headers
     * @return TransfersList|array<string, mixed>
     */
    public function getTransfers(?GetTransfersRequest $request = null, array $headers = []): TransfersList|array;

    /**
     * @param GetTransfersRequest|null $request
     * @param array<string, string> $headers
     */
    public function getTransfersAsync(?GetTransfersRequest $request = null, array $headers = []): mixed;

    /**
     * @param string $tid Transfer serial ID
     * @param array<string, string> $headers
     */
    public function deleteTransfer(string $tid, array $headers = []): void;

    /**
     * @param string $tid Transfer serial ID
     * @param array<string, string> $headers
     */
    public function deleteTransferAsync(string $tid, array $headers = []): mixed;
}
