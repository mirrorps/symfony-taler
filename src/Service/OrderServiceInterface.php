<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

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

interface OrderServiceInterface
{
    /**
     * @param GetOrdersRequest|array<string, scalar>|null $request
     * @param array<string, string> $headers
     * @return OrderHistory|array<string, mixed>
     */
    public function getOrders(GetOrdersRequest|array|null $request = null, array $headers = []): OrderHistory|array;

    /**
     * @param GetOrdersRequest|array<string, scalar>|null $request
     * @param array<string, string> $headers
     */
    public function getOrdersAsync(GetOrdersRequest|array|null $request = null, array $headers = []): mixed;

    /**
     * @param string $orderId
     * @param GetOrderRequest|array<string, scalar>|null $request
     * @param array<string, string> $headers
     * @return CheckPaymentPaidResponse|CheckPaymentClaimedResponse|CheckPaymentUnpaidResponse|ChallengeResponse|array<string, mixed>
     */
    public function getOrder(string $orderId, GetOrderRequest|array|null $request = null, array $headers = []): CheckPaymentPaidResponse|CheckPaymentClaimedResponse|CheckPaymentUnpaidResponse|ChallengeResponse|array;

    /**
     * @param string $orderId
     * @param GetOrderRequest|array<string, scalar>|null $request
     * @param array<string, string> $headers
     */
    public function getOrderAsync(string $orderId, GetOrderRequest|array|null $request = null, array $headers = []): mixed;

    /**
     * @param PostOrderRequest $postOrderRequest
     * @param array<string, string> $headers
     * @return PostOrderResponse|array<string, mixed>
     */
    public function createOrder(PostOrderRequest $postOrderRequest, array $headers = []): PostOrderResponse|array;

    /**
     * @param PostOrderRequest $postOrderRequest
     * @param array<string, string> $headers
     */
    public function createOrderAsync(PostOrderRequest $postOrderRequest, array $headers = []): mixed;

    /**
     * @param string $orderId
     * @param RefundRequest $refundRequest
     * @param array<string, string> $headers
     * @return MerchantRefundResponse|array<string, mixed>
     */
    public function refundOrder(string $orderId, RefundRequest $refundRequest, array $headers = []): MerchantRefundResponse|array;

    /**
     * @param string $orderId
     * @param RefundRequest $refundRequest
     * @param array<string, string> $headers
     */
    public function refundOrderAsync(string $orderId, RefundRequest $refundRequest, array $headers = []): mixed;

    /**
     * @param string $orderId
     * @param array<string, string> $headers
     */
    public function deleteOrder(string $orderId, array $headers = []): void;

    /**
     * @param string $orderId
     * @param array<string, string> $headers
     */
    public function deleteOrderAsync(string $orderId, array $headers = []): mixed;

    /**
     * @param string $orderId
     * @param ForgetRequest $forgetRequest
     * @param array<string, string> $headers
     */
    public function forgetOrder(string $orderId, ForgetRequest $forgetRequest, array $headers = []): void;

    /**
     * @param string $orderId
     * @param ForgetRequest $forgetRequest
     * @param array<string, string> $headers
     */
    public function forgetOrderAsync(string $orderId, ForgetRequest $forgetRequest, array $headers = []): mixed;
}
