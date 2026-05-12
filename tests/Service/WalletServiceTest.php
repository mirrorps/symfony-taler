<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Service;

use MirrorPS\TalerBundle\Service\WalletService;
use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\Wallet\Dto\StatusGotoResponse;
use Taler\Api\Wallet\Dto\StatusPaidResponse;
use Taler\Api\Wallet\Dto\StatusUnpaidResponse;
use Taler\Api\Wallet\WalletClient;
use Taler\Taler as TalerClient;

final class WalletServiceTest extends TestCase
{
    private WalletClient $walletClient;
    private WalletService $service;

    protected function setUp(): void
    {
        $this->walletClient = $this->createMock(WalletClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->method('wallet')->willReturn($this->walletClient);

        $taler = new Taler($client);
        $this->service = new WalletService($taler);
    }

    public function testGetOrderDelegatesToClientWithPaidResponse(): void
    {
        $orderId = 'public-order-1';
        $params = ['session_id' => 'sess-1'];
        $headers = ['Accept' => 'application/json'];
        $expected = new StatusPaidResponse(
            refunded: false,
            refund_pending: false,
            refund_amount: 'EUR:0',
            refund_taken: 'EUR:0',
        );

        $this->walletClient->expects(self::once())
            ->method('getOrder')
            ->with($orderId, $params, $headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOrder($orderId, $params, $headers));
    }

    public function testGetOrderWithGotoResponse(): void
    {
        $orderId = 'public-order-2';
        $expected = new StatusGotoResponse(public_reorder_url: 'https://merchant.example/reorder');

        $this->walletClient->expects(self::once())
            ->method('getOrder')
            ->with($orderId, [], [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOrder($orderId));
    }

    public function testGetOrderWithUnpaidResponse(): void
    {
        $orderId = 'public-order-3';
        $expected = new StatusUnpaidResponse(
            taler_pay_uri: 'taler://pay/test',
            fulfillment_url: 'https://merchant.example/ok',
            already_paid_order_id: null,
        );

        $this->walletClient->expects(self::once())
            ->method('getOrder')
            ->with($orderId, [], [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOrder($orderId));
    }

    public function testGetOrderAsyncDelegatesToClient(): void
    {
        $orderId = 'public-order-async';
        $params = ['foo' => 'bar'];
        $expected = 'async-promise';

        $this->walletClient->expects(self::once())
            ->method('getOrderAsync')
            ->with($orderId, $params, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOrderAsync($orderId, $params));
    }
}
