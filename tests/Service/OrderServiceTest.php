<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Service;

use MirrorPS\TalerBundle\Service\OrderService;
use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\Order\Dto\CheckPaymentClaimedResponse;
use Taler\Api\Order\Dto\CheckPaymentPaidResponse;
use Taler\Api\Order\Dto\CheckPaymentUnpaidResponse;
use Taler\Api\Order\Dto\ForgetRequest;
use Taler\Api\Order\Dto\GetOrderRequest;
use Taler\Api\Order\Dto\GetOrdersRequest;
use Taler\Api\Order\Dto\MerchantRefundResponse;
use Taler\Api\Order\Dto\OrderHistory;
use Taler\Api\Order\Dto\Amount;
use Taler\Api\Order\Dto\OrderV0;
use Taler\Api\Order\Dto\PostOrderRequest;
use Taler\Api\Order\Dto\PostOrderResponse;
use Taler\Api\Order\Dto\RefundRequest;
use Taler\Api\Order\OrderClient;
use Taler\Taler as TalerClient;

final class OrderServiceTest extends TestCase
{
    private OrderClient $orderClient;
    private OrderService $service;

    protected function setUp(): void
    {
        $this->orderClient = $this->createMock(OrderClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->method('order')->willReturn($this->orderClient);

        $taler = new Taler($client);
        $this->service = new OrderService($taler);
    }

    public function testGetOrdersDelegatesToClient(): void
    {
        $request = new GetOrdersRequest(paid: true, limit: 10);
        $expected = new OrderHistory([]);

        $this->orderClient->expects(self::once())
            ->method('getOrders')
            ->with($request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOrders($request));
    }

    public function testGetOrdersWithNullRequest(): void
    {
        $expected = new OrderHistory([]);

        $this->orderClient->expects(self::once())
            ->method('getOrders')
            ->with(null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOrders());
    }

    public function testGetOrdersWithArrayRequest(): void
    {
        $request = ['paid' => 'yes'];
        $expected = new OrderHistory([]);

        $this->orderClient->expects(self::once())
            ->method('getOrders')
            ->with($request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOrders($request));
    }

    public function testGetOrdersPassesHeaders(): void
    {
        $headers = ['X-Custom' => 'value'];
        $expected = new OrderHistory([]);

        $this->orderClient->expects(self::once())
            ->method('getOrders')
            ->with(null, $headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOrders(null, $headers));
    }

    public function testGetOrdersAsyncDelegatesToClient(): void
    {
        $request = new GetOrdersRequest(limit: 5);
        $expected = 'async-promise';

        $this->orderClient->expects(self::once())
            ->method('getOrdersAsync')
            ->with($request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOrdersAsync($request));
    }

    public function testGetOrderDelegatesToClientWithPaidResponse(): void
    {
        $orderId = 'order-123';
        $request = new GetOrderRequest(session_id: 'sess-1');
        $expected = $this->createMock(CheckPaymentPaidResponse::class);

        $this->orderClient->expects(self::once())
            ->method('getOrder')
            ->with($orderId, $request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOrder($orderId, $request));
    }

    public function testGetOrderWithUnpaidResponse(): void
    {
        $orderId = 'order-456';
        $expected = $this->createMock(CheckPaymentUnpaidResponse::class);

        $this->orderClient->expects(self::once())
            ->method('getOrder')
            ->with($orderId, null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOrder($orderId));
    }

    public function testGetOrderWithClaimedResponse(): void
    {
        $orderId = 'order-789';
        $expected = $this->createMock(CheckPaymentClaimedResponse::class);

        $this->orderClient->expects(self::once())
            ->method('getOrder')
            ->with($orderId, null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOrder($orderId));
    }

    public function testGetOrderPassesHeaders(): void
    {
        $orderId = 'order-abc';
        $headers = ['Authorization' => 'Bearer token'];
        $expected = $this->createMock(CheckPaymentPaidResponse::class);

        $this->orderClient->expects(self::once())
            ->method('getOrder')
            ->with($orderId, null, $headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOrder($orderId, null, $headers));
    }

    public function testGetOrderAsyncDelegatesToClient(): void
    {
        $orderId = 'order-async';
        $request = new GetOrderRequest(timeout_ms: 5000);
        $expected = 'async-promise';

        $this->orderClient->expects(self::once())
            ->method('getOrderAsync')
            ->with($orderId, $request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOrderAsync($orderId, $request));
    }

    public function testCreateOrderDelegatesToClient(): void
    {
        $postOrderRequest = new PostOrderRequest(
            order: new OrderV0(amount: new Amount('EUR:10.00'), summary: 'Test order', fulfillment_url: 'https://example.com/thanks'),
        );
        $expected = new PostOrderResponse(order_id: 'new-order-1', token: 'tok-123');

        $this->orderClient->expects(self::once())
            ->method('createOrder')
            ->with($postOrderRequest, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createOrder($postOrderRequest));
    }

    public function testCreateOrderPassesHeaders(): void
    {
        $postOrderRequest = new PostOrderRequest(
            order: new OrderV0(amount: new Amount('EUR:5.00'), summary: 'Another order', fulfillment_url: 'https://example.com/thanks'),
        );
        $headers = ['X-Custom' => 'value'];
        $expected = new PostOrderResponse(order_id: 'new-order-2');

        $this->orderClient->expects(self::once())
            ->method('createOrder')
            ->with($postOrderRequest, $headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createOrder($postOrderRequest, $headers));
    }

    public function testCreateOrderAsyncDelegatesToClient(): void
    {
        $postOrderRequest = new PostOrderRequest(
            order: new OrderV0(amount: new Amount('EUR:1.00'), summary: 'Async order', fulfillment_url: 'https://example.com/thanks'),
        );
        $expected = 'async-promise';

        $this->orderClient->expects(self::once())
            ->method('createOrderAsync')
            ->with($postOrderRequest, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createOrderAsync($postOrderRequest));
    }

    public function testRefundOrderDelegatesToClient(): void
    {
        $orderId = 'order-refund';
        $refundRequest = new RefundRequest(refund: 'EUR:5.00', reason: 'Customer request');
        $expected = new MerchantRefundResponse(
            taler_refund_uri: 'taler://refund/example',
            h_contract: 'abc123',
        );

        $this->orderClient->expects(self::once())
            ->method('refundOrder')
            ->with($orderId, $refundRequest, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->refundOrder($orderId, $refundRequest));
    }

    public function testRefundOrderPassesHeaders(): void
    {
        $orderId = 'order-refund-2';
        $refundRequest = new RefundRequest(refund: 'EUR:2.00', reason: 'Defective');
        $headers = ['X-Request-Id' => '123'];
        $expected = new MerchantRefundResponse(
            taler_refund_uri: 'taler://refund/example2',
            h_contract: 'def456',
        );

        $this->orderClient->expects(self::once())
            ->method('refundOrder')
            ->with($orderId, $refundRequest, $headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->refundOrder($orderId, $refundRequest, $headers));
    }

    public function testRefundOrderAsyncDelegatesToClient(): void
    {
        $orderId = 'order-refund-async';
        $refundRequest = new RefundRequest(refund: 'EUR:3.00', reason: 'Async refund');
        $expected = 'async-promise';

        $this->orderClient->expects(self::once())
            ->method('refundOrderAsync')
            ->with($orderId, $refundRequest, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->refundOrderAsync($orderId, $refundRequest));
    }

    public function testDeleteOrderDelegatesToClient(): void
    {
        $orderId = 'order-delete';

        $this->orderClient->expects(self::once())
            ->method('deleteOrder')
            ->with($orderId, []);

        $this->service->deleteOrder($orderId);
    }

    public function testDeleteOrderPassesHeaders(): void
    {
        $orderId = 'order-delete-2';
        $headers = ['Authorization' => 'Bearer token'];

        $this->orderClient->expects(self::once())
            ->method('deleteOrder')
            ->with($orderId, $headers);

        $this->service->deleteOrder($orderId, $headers);
    }

    public function testDeleteOrderAsyncDelegatesToClient(): void
    {
        $orderId = 'order-delete-async';
        $expected = 'async-promise';

        $this->orderClient->expects(self::once())
            ->method('deleteOrderAsync')
            ->with($orderId, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->deleteOrderAsync($orderId));
    }

    public function testForgetOrderDelegatesToClient(): void
    {
        $orderId = 'order-forget';
        $forgetRequest = new ForgetRequest(fields: ['$.merchant', '$.products']);

        $this->orderClient->expects(self::once())
            ->method('forgetOrder')
            ->with($orderId, $forgetRequest, []);

        $this->service->forgetOrder($orderId, $forgetRequest);
    }

    public function testForgetOrderPassesHeaders(): void
    {
        $orderId = 'order-forget-2';
        $forgetRequest = new ForgetRequest(fields: ['$.merchant']);
        $headers = ['X-Trace' => 'abc'];

        $this->orderClient->expects(self::once())
            ->method('forgetOrder')
            ->with($orderId, $forgetRequest, $headers);

        $this->service->forgetOrder($orderId, $forgetRequest, $headers);
    }

    public function testForgetOrderAsyncDelegatesToClient(): void
    {
        $orderId = 'order-forget-async';
        $forgetRequest = new ForgetRequest(fields: ['$.merchant']);
        $expected = 'async-promise';

        $this->orderClient->expects(self::once())
            ->method('forgetOrderAsync')
            ->with($orderId, $forgetRequest, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->forgetOrderAsync($orderId, $forgetRequest));
    }
}
