<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use MirrorPS\TalerBundle\Taler;
use Taler\Api\Order\Dto\CheckPaymentClaimedResponse;
use Taler\Api\Order\Dto\CheckPaymentPaidResponse;
use Taler\Api\Order\Dto\CheckPaymentUnpaidResponse;
use Taler\Api\Order\Dto\ForgetRequest;
use Taler\Api\Order\Dto\GetOrderRequest;
use Taler\Api\Order\Dto\GetOrdersRequest;
use Taler\Api\Order\Dto\MerchantRefundResponse;
use Taler\Api\Order\Dto\OrderHistory;
use Taler\Api\Order\Dto\PostOrderRequest;
use Taler\Api\Order\Dto\PostOrderResponse;
use Taler\Api\Order\Dto\RefundRequest;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;

final class OrderService implements OrderServiceInterface
{
    private Taler $taler;

    public function __construct(Taler $taler)
    {
        $this->taler = $taler;
    }

    public function getOrders(GetOrdersRequest|array|null $request = null, array $headers = []): OrderHistory|array
    {
        return $this->taler->orders()->getOrders($request, $headers);
    }

    public function getOrdersAsync(GetOrdersRequest|array|null $request = null, array $headers = []): mixed
    {
        return $this->taler->orders()->getOrdersAsync($request, $headers);
    }

    public function getOrder(string $orderId, GetOrderRequest|array|null $request = null, array $headers = []): CheckPaymentPaidResponse|CheckPaymentClaimedResponse|CheckPaymentUnpaidResponse|ChallengeResponse|array
    {
        return $this->taler->orders()->getOrder($orderId, $request, $headers);
    }

    public function getOrderAsync(string $orderId, GetOrderRequest|array|null $request = null, array $headers = []): mixed
    {
        return $this->taler->orders()->getOrderAsync($orderId, $request, $headers);
    }

    public function createOrder(PostOrderRequest $postOrderRequest, array $headers = []): PostOrderResponse|array
    {
        return $this->taler->orders()->createOrder($postOrderRequest, $headers);
    }

    public function createOrderAsync(PostOrderRequest $postOrderRequest, array $headers = []): mixed
    {
        return $this->taler->orders()->createOrderAsync($postOrderRequest, $headers);
    }

    public function refundOrder(string $orderId, RefundRequest $refundRequest, array $headers = []): MerchantRefundResponse|array
    {
        return $this->taler->orders()->refundOrder($orderId, $refundRequest, $headers);
    }

    public function refundOrderAsync(string $orderId, RefundRequest $refundRequest, array $headers = []): mixed
    {
        return $this->taler->orders()->refundOrderAsync($orderId, $refundRequest, $headers);
    }

    public function deleteOrder(string $orderId, array $headers = []): void
    {
        $this->taler->orders()->deleteOrder($orderId, $headers);
    }

    public function deleteOrderAsync(string $orderId, array $headers = []): mixed
    {
        return $this->taler->orders()->deleteOrderAsync($orderId, $headers);
    }

    public function forgetOrder(string $orderId, ForgetRequest $forgetRequest, array $headers = []): void
    {
        $this->taler->orders()->forgetOrder($orderId, $forgetRequest, $headers);
    }

    public function forgetOrderAsync(string $orderId, ForgetRequest $forgetRequest, array $headers = []): mixed
    {
        return $this->taler->orders()->forgetOrderAsync($orderId, $forgetRequest, $headers);
    }
}
