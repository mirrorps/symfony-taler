<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Service;

use MirrorPS\TalerBundle\Service\WireTransfersService;
use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\Dto\Timestamp;
use Taler\Api\WireTransfers\Dto\GetTransfersRequest;
use Taler\Api\WireTransfers\Dto\TransferDetails;
use Taler\Api\WireTransfers\Dto\TransfersList;
use Taler\Api\WireTransfers\WireTransfersClient;
use Taler\Taler as TalerClient;

final class WireTransfersServiceTest extends TestCase
{
    private WireTransfersClient $wireTransfersClient;
    private WireTransfersService $service;

    protected function setUp(): void
    {
        $this->wireTransfersClient = $this->createMock(WireTransfersClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->method('wireTransfers')->willReturn($this->wireTransfersClient);

        $taler = new Taler($client);
        $this->service = new WireTransfersService($taler);
    }

    public function testGetTransfersDelegatesToClient(): void
    {
        $expected = new TransfersList([
            new TransferDetails(
                credit_amount: 'EUR:10.00',
                wtid: 'WTID-1',
                payto_uri: 'payto://iban/DE89370400440532013000',
                exchange_url: 'https://exchange.example.com',
                transfer_serial_id: 1,
                execution_time: new Timestamp(t_s: 1700000000),
            ),
        ]);

        $this->wireTransfersClient->expects(self::once())
            ->method('getTransfers')
            ->with(null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getTransfers());
    }

    public function testGetTransfersPassesRequestAndHeaders(): void
    {
        $request = new GetTransfersRequest(limit: 10, offset: 0);
        $headers = ['X-Trace-Id' => 'wire-transfers-list'];
        $expected = new TransfersList([]);

        $this->wireTransfersClient->expects(self::once())
            ->method('getTransfers')
            ->with($request, $headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getTransfers($request, $headers));
    }

    public function testGetTransfersAsyncDelegatesToClient(): void
    {
        $request = new GetTransfersRequest(payto_uri: 'payto://iban/DE89370400440532013000');
        $expected = 'async-promise';

        $this->wireTransfersClient->expects(self::once())
            ->method('getTransfersAsync')
            ->with($request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getTransfersAsync($request));
    }

    public function testDeleteTransferDelegatesToClient(): void
    {
        $tid = '123';

        $this->wireTransfersClient->expects(self::once())
            ->method('deleteTransfer')
            ->with($tid, []);

        $this->service->deleteTransfer($tid);
    }

    public function testDeleteTransferPassesHeaders(): void
    {
        $tid = '456';
        $headers = ['Authorization' => 'Bearer token'];

        $this->wireTransfersClient->expects(self::once())
            ->method('deleteTransfer')
            ->with($tid, $headers);

        $this->service->deleteTransfer($tid, $headers);
    }

    public function testDeleteTransferAsyncDelegatesToClient(): void
    {
        $tid = '789';
        $expected = 'async-promise';

        $this->wireTransfersClient->expects(self::once())
            ->method('deleteTransferAsync')
            ->with($tid, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->deleteTransferAsync($tid));
    }
}
