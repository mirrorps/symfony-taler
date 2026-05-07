# TalerBundle

Symfony bundle for [GNU Taler](https://taler.net/) payment integration via [`mirrorps/taler-php`](https://packagist.org/packages/mirrorps/taler-php).

## Requirements

- PHP >= 8.1
- Symfony 6.4 or 7.0+

## Installation

```bash
composer require mirrorps/symfony-taler
```

## Configuration

Add your Taler merchant backend credentials in `config/packages/taler.yaml`:

```yaml
taler:
  base_url: 'https://backend.demo.taler.net/instances/sandbox'
  token: 'secret-token:your-api-token'
```

Or use credential-based authentication:

```yaml
taler:
  base_url: 'https://backend.demo.taler.net/instances/sandbox'
  username: 'your-username'
  password: 'your-password'
  instance: 'sandbox'
  scope: 'write'
```

| Option     | Required | Description                                              |
|------------|----------|----------------------------------------------------------|
| `base_url` | Yes      | Merchant backend URL                                     |
| `token`    | No       | Bearer token (takes precedence over username/password)   |
| `username` | No       | Username for credential-based auth                       |
| `password` | No       | Password for credential-based auth                       |
| `instance` | No       | Merchant instance identifier                             |
| `scope`    | No       | Token permission scope                                   |

## Usage

The bundle registers services that can be injected via autowiring.

### Available Services

| Service           | Interface                  | Description                |
|-------------------|----------------------------|----------------------------|
| `OrderService`    | `OrderServiceInterface`    | Full order management      |
| `InstanceService` | `InstanceServiceInterface` | Instance management        |
| `ConfigService`   | `ConfigServiceInterface`   | Merchant config endpoint   |
| `Taler`           | -                          | Low-level client wrapper   |

### OrderService

The `OrderServiceInterface` provides access to the Taler merchant Order API.

#### List Orders

```php
use MirrorPS\TalerBundle\Service\OrderServiceInterface;
use Taler\Api\Order\Dto\GetOrdersRequest;

class MyController
{
    public function listOrders(OrderServiceInterface $orderService): void
    {
        // Get all orders (no filters)
        $history = $orderService->getOrders();

        foreach ($history->orders as $entry) {
            echo sprintf(
                "Order %s: %s - %s (paid: %s)\n",
                $entry->order_id,
                $entry->summary,
                $entry->amount,
                $entry->paid ? 'yes' : 'no'
            );
        }
    }
}
```

#### List Orders with Filters

```php
use MirrorPS\TalerBundle\Service\OrderServiceInterface;
use Taler\Api\Order\Dto\GetOrdersRequest;

class MyController
{
    public function listPaidOrders(OrderServiceInterface $orderService): void
    {
        $request = new GetOrdersRequest(
            paid: true,
            limit: 10,
        );

        $history = $orderService->getOrders($request);

        foreach ($history->orders as $entry) {
            echo sprintf(
                "[%s] %s - %s\n",
                $entry->order_id,
                $entry->summary,
                $entry->amount
            );
        }
    }
}
```

#### Get a Single Order

```php
use MirrorPS\TalerBundle\Service\OrderServiceInterface;
use Taler\Api\Order\Dto\CheckPaymentPaidResponse;
use Taler\Api\Order\Dto\CheckPaymentUnpaidResponse;
use Taler\Api\Order\Dto\CheckPaymentClaimedResponse;

class MyController
{
    public function checkOrder(OrderServiceInterface $orderService, string $orderId): void
    {
        $order = $orderService->getOrder($orderId);

        if ($order instanceof CheckPaymentPaidResponse) {
            echo sprintf("Order %s is paid. Refunded: %s\n", $orderId, $order->refunded ? 'yes' : 'no');
        } elseif ($order instanceof CheckPaymentUnpaidResponse) {
            echo sprintf("Order %s is unpaid. Pay URI: %s\n", $orderId, $order->taler_pay_uri);
        } elseif ($order instanceof CheckPaymentClaimedResponse) {
            echo sprintf("Order %s is claimed.\n", $orderId);
        }
    }
}
```

#### Get a Single Order with Query Parameters

```php
use MirrorPS\TalerBundle\Service\OrderServiceInterface;
use Taler\Api\Order\Dto\GetOrderRequest;

class MyController
{
    public function checkOrderWithSession(OrderServiceInterface $orderService, string $orderId): void
    {
        $request = new GetOrderRequest(
            session_id: 'my-session-id',
            timeout_ms: 5000,
        );

        $order = $orderService->getOrder($orderId, $request);

        echo sprintf("Order status: %s\n", $order->order_status ?? 'unknown');
    }
}
```

#### Create an Order

```php
use MirrorPS\TalerBundle\Service\OrderServiceInterface;
use Taler\Api\Order\Dto\Amount;
use Taler\Api\Order\Dto\OrderV0;
use Taler\Api\Order\Dto\PostOrderRequest;

class MyController
{
    public function createOrder(OrderServiceInterface $orderService): void
    {
        $order = new OrderV0(
            amount: new Amount('EUR:10.00'),
            summary: 'My product',
            fulfillment_url: 'https://example.com/thank-you',
        );

        $response = $orderService->createOrder(new PostOrderRequest(order: $order));

        echo sprintf("Created order: %s (token: %s)\n", $response->order_id, $response->token ?? 'none');
    }
}
```

#### Refund an Order

```php
use MirrorPS\TalerBundle\Service\OrderServiceInterface;
use Taler\Api\Order\Dto\RefundRequest;

class MyController
{
    public function refundOrder(OrderServiceInterface $orderService, string $orderId): void
    {
        $refundRequest = new RefundRequest(
            refund: 'EUR:5.00',
            reason: 'Customer requested refund',
        );

        $response = $orderService->refundOrder($orderId, $refundRequest);

        echo sprintf("Refund URI: %s\n", $response->taler_refund_uri);
    }
}
```

#### Delete an Order

```php
use MirrorPS\TalerBundle\Service\OrderServiceInterface;

class MyController
{
    public function deleteOrder(OrderServiceInterface $orderService, string $orderId): void
    {
        $orderService->deleteOrder($orderId);

        echo sprintf("Order %s deleted.\n", $orderId);
    }
}
```

#### Forget Order Fields

```php
use MirrorPS\TalerBundle\Service\OrderServiceInterface;
use Taler\Api\Order\Dto\ForgetRequest;

class MyController
{
    public function forgetOrderFields(OrderServiceInterface $orderService, string $orderId): void
    {
        $forgetRequest = new ForgetRequest(
            fields: ['$.merchant', '$.products'],
        );

        $orderService->forgetOrder($orderId, $forgetRequest);

        echo sprintf("Fields forgotten for order %s.\n", $orderId);
    }
}
```

### InstanceService

The `InstanceServiceInterface` provides access to the Taler merchant Instance Management API.

```php
use MirrorPS\TalerBundle\Service\InstanceServiceInterface;
use Taler\Api\Dto\RelativeTime;
use Taler\Api\Instance\Dto\GetAccessTokensRequest;
use Taler\Api\Instance\Dto\GetKycStatusRequest;
use Taler\Api\Instance\Dto\GetMerchantStatisticsAmountRequest;
use Taler\Api\Instance\Dto\GetMerchantStatisticsCounterRequest;
use Taler\Api\Instance\Dto\InstanceAuthConfigToken;
use Taler\Api\Instance\Dto\InstanceConfigurationMessage;
use Taler\Api\Instance\Dto\InstanceReconfigurationMessage;
use Taler\Api\Instance\Dto\LoginTokenRequest;
```

#### List All Instances

```php
class MyController
{
    public function listInstances(InstanceServiceInterface $instanceService): void
    {
        $instances = $instanceService->getInstances();

        foreach ($instances->instances as $instance) {
            echo sprintf("Instance %s: %s\n", $instance->id, $instance->name);
        }
    }
}
```

> NOTE: 
> If your backend returns `404`, you are likely using a per-instance base URL such as `https://backend.demo.taler.net/instances/sandbox`.
> In that setup, use the single-instance private endpoint instead:
> `GET https://backend.demo.taler.net/instances/sandbox/private`

#### Get a Single Instance

```php
class MyController
{
    public function showInstance(InstanceServiceInterface $instanceService, string $instanceId): void
    {
        $instance = $instanceService->getInstance($instanceId);

        echo sprintf("Name: %s\n", $instance->name);
    }
}
```

#### Create an Instance

```php
class MyController
{
    public function createInstance(InstanceServiceInterface $instanceService): void
    {
        $instanceService->createInstance(new InstanceConfigurationMessage(
            id: 'coffee-shop',
            name: 'Coffee Shop',
            auth: new InstanceAuthConfigToken(password: 'super-secret'),
            address: new \Taler\Api\Dto\Location(country: 'DE', town: 'Berlin'),
            jurisdiction: new \Taler\Api\Dto\Location(country: 'DE'),
            use_stefan: false,
            default_wire_transfer_delay: new RelativeTime(d_us: 0),
            default_pay_delay: new RelativeTime(d_us: 0),
        ));
    }
}
```

#### Update an Instance

```php
class MyController
{
    public function updateInstance(InstanceServiceInterface $instanceService): void
    {
        $instanceService->updateInstance('coffee-shop', new InstanceReconfigurationMessage(
            name: 'Coffee Shop Berlin',
            address: new \Taler\Api\Dto\Location(country: 'DE', town: 'Berlin'),
            jurisdiction: new \Taler\Api\Dto\Location(country: 'DE'),
            use_stefan: false,
            default_wire_transfer_delay: new RelativeTime(d_us: 0),
            default_pay_delay: new RelativeTime(d_us: 0),
        ));
    }
}
```

#### Update Instance Authentication

```php
class MyController
{
    public function updateAuth(InstanceServiceInterface $instanceService): void
    {
        $challenge = $instanceService->updateAuth(
            'coffee-shop',
            new InstanceAuthConfigToken(password: 'new-secret'),
        );
    }
}
```

#### Forgot Password

```php
class MyController
{
    public function forgotPassword(InstanceServiceInterface $instanceService): void
    {
        $challenge = $instanceService->forgotPassword(
            'coffee-shop',
            new InstanceAuthConfigToken(password: 'reset-secret'),
        );
    }
}
```

#### Retrieve an Access Token

```php
class MyController
{
    public function getToken(InstanceServiceInterface $instanceService): void
    {
        $token = $instanceService->getAccessToken('coffee-shop', new LoginTokenRequest(
            scope: 'readonly',
            duration: new RelativeTime(d_us: 3600000000),
            description: 'Backoffice session',
        ));
    }
}
```

#### List and Revoke Access Tokens

```php
class MyController
{
    public function manageTokens(InstanceServiceInterface $instanceService): void
    {
        $tokens = $instanceService->getAccessTokens(
            'coffee-shop',
            new GetAccessTokensRequest(limit: 20),
        );

        $instanceService->deleteAccessToken('coffee-shop');
        $instanceService->deleteAccessTokenBySerial('coffee-shop', 42);
    }
}
```

#### Check KYC Status

```php
class MyController
{
    public function checkKyc(InstanceServiceInterface $instanceService): void
    {
        $kycStatus = $instanceService->getKycStatus(
            'coffee-shop',
            new GetKycStatusRequest(timeout_ms: 5000),
        );
    }
}
```

#### Merchant Statistics

```php
class MyController
{
    public function viewStats(InstanceServiceInterface $instanceService): void
    {
        $amountStats = $instanceService->getMerchantStatisticsAmount(
            'coffee-shop',
            'revenue',
            new GetMerchantStatisticsAmountRequest(by: 'ANY'),
        );

        $counterStats = $instanceService->getMerchantStatisticsCounter(
            'coffee-shop',
            'orders',
            new GetMerchantStatisticsCounterRequest(by: 'BUCKET'),
        );
    }
}
```

#### Delete or Purge an Instance

```php
class MyController
{
    public function deleteInstance(InstanceServiceInterface $instanceService): void
    {
        $challenge = $instanceService->deleteInstance('coffee-shop');
        $challenge = $instanceService->deleteInstance('coffee-shop', purge: true);
    }
}
```

### ConfigService

The `ConfigServiceInterface` provides access to the public Merchant Config API.

```php
use MirrorPS\TalerBundle\Service\ConfigServiceInterface;

class MyController
{
    public function showConfig(ConfigServiceInterface $configService): void
    {
        $config = $configService->getConfig();

        echo sprintf(
            "Backend %s (%s), currency: %s\n",
            $config->name,
            $config->version,
            $config->currency
        );
    }
}
```

### Async Support

All methods support asynchronous execution by appending `Async` to the method name. Async methods return a promise that resolves to the same type as the synchronous variant.

```php
// Synchronous
$history = $orderService->getOrders($request);

// Asynchronous
$promise = $orderService->getOrdersAsync($request);
```

```php
// Synchronous
$response = $orderService->createOrder($postOrderRequest);

// Asynchronous
$promise = $orderService->createOrderAsync($postOrderRequest);
```

```php
// Synchronous
$instances = $instanceService->getInstances();

// Asynchronous
$promise = $instanceService->getInstancesAsync();
```

### Direct Client Access

For advanced use cases, you can access the underlying `taler-php` client directly:

```php
use MirrorPS\TalerBundle\Taler;

class MyController
{
    public function advanced(Taler $taler): void
    {
        // Access the OrderClient directly
        $orderClient = $taler->orders();

        // Access the InstanceClient directly
        $instanceClient = $taler->instance();

        // Access the ConfigClient directly
        $configClient = $taler->config();

        // Or get the full taler-php client
        $client = $taler->getClient();
    }
}
```

## Testing

```bash
vendor/bin/phpunit
```

## License

MIT
