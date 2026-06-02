# TalerBundle


Symfony bundle for [GNU Taler](https://taler.net/) payment integration via [`mirrorps/taler-php`](https://packagist.org/packages/mirrorps/taler-php).

## Requirements

- PHP >= 8.1
- Symfony 6.4 or 7.0+

## Installation

```bash
composer require mirrorps/symfony-taler
```

Then complete **[HTTP client setup](#http-client-setup-required-for-api-calls)**. Without it, API calls will not work.

## Configuration

Add your Taler merchant backend credentials in `config/packages/taler.yaml`:

```yaml
taler:
  base_url: 'https://backend.demo.taler.net/instances/sandbox'
  token: 'Bearer secret-token:your-api-token'
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
| `http_client` | No    | PSR-18 client service id; leave unset to use Symfony `psr18.http_client` (see below) |
| `logger`   | No       | PSR-3 logger service id, `null` to auto-wire Monolog `taler` channel, or `false` to disable |
| `debug_logging_enabled` | No | Enable sanitized HTTP debug logs in taler-php (default: `false`) |

### HTTP client setup

The bundle talks to the Taler backend over HTTP via [taler-php](https://packagist.org/packages/mirrorps/taler-php). It does **not** include an HTTP library - you must provide one.

**Step 1 - Install packages**

```bash
composer require symfony/http-client nyholm/psr7
```

**Step 2 - Enable the Framework HTTP client**

Add or update `config/packages/framework.yaml`:

```yaml
framework:
    http_client: true
```

**Done.** You do **not** need to set `http_client` in `config/packages/taler.yaml`. The bundle connects to Symfony’s `psr18.http_client` automatically (the same client your app can use elsewhere).

#### Custom PSR-18 client (optional)

Only if you already have your own `Psr\Http\Client\ClientInterface` service:

```yaml
# config/packages/taler.yaml
taler:
  base_url: 'https://backend.demo.taler.net/instances/sandbox'
  http_client: 'app.my_psr18_client'   # your service id
```

#### Without Symfony HttpClient (Guzzle, etc.)

Install a PSR-18 client **and** PSR-17 factories, then either rely on taler-php discovery or set `http_client` explicitly:

```bash
composer require guzzlehttp/guzzle php-http/guzzle7-adapter
```

### Logging (optional)

Logging is **optional**. The bundle does not require a logging package in your application. [taler-php](https://packagist.org/packages/mirrorps/taler-php) already depends on `psr/log` (interfaces only); without a PSR-3 **implementation** the SDK uses `NullLogger` and produces no log output.

Log formatting and HTTP trace sanitization live in taler-php only — this bundle forwards a logger service when you configure one.

To enable logging, install a PSR-3 implementation. Monolog via Symfony is recommended:

```bash
composer require symfony/monolog-bundle
```

The bundle prepends a dedicated `taler` Monolog channel and wires `monolog.logger.taler` into the client factory when `logger` is left unset (`null`). Alternatively, install any package that satisfies `psr/log-implementation` and set `logger` to your service id.

```yaml
# config/packages/taler.yaml
taler:
  base_url: 'https://backend.demo.taler.net/instances/sandbox'
  token: 'Bearer secret-token:your-api-token'
  debug_logging_enabled: '%kernel.debug%'
```

Use a custom logger service:

```yaml
taler:
  base_url: 'https://example.test/instances/shop'
  logger: 'app.custom_psr_logger'
  debug_logging_enabled: true
```

Disable logging entirely (default when Monolog is not installed, or explicitly):

```yaml
taler:
  logger: false
```

When `debug_logging_enabled` is `true`, ensure the logger handler accepts `DEBUG` level. Errors and protocol warnings from taler-php are logged at `error` / `warning` regardless of this flag, but only when a real logger is wired.

## Usage

The bundle registers services that can be injected via autowiring.

### Available Services

| Service           | Interface                  | Description                |
|-------------------|----------------------------|----------------------------|
| `OrderService`    | `OrderServiceInterface`    | Full order management      |
| `BankAccountService` | `BankAccountServiceInterface` | Bank account management |
| `WireTransfersService` | `WireTransfersServiceInterface` | Merchant wire transfers |
| `InstanceService` | `InstanceServiceInterface` | Instance management        |
| `ConfigService`   | `ConfigServiceInterface`   | Merchant config endpoint   |
| `DonauCharityService` | `DonauCharityServiceInterface` | Donau charity linking |
| `OtpDevicesService` | `OtpDevicesServiceInterface` | OTP devices (POS confirmation) |
| `TemplatesService` | `TemplatesServiceInterface` | Order templates (contract presets) |
| `TokenFamiliesService` | `TokenFamiliesServiceInterface` | Token families (discount / subscription) |
| `TwoFactorAuthService` | `TwoFactorAuthServiceInterface` | TAN challenge request / confirm |
| `WebhooksService` | `WebhooksServiceInterface` | Merchant webhooks (HTTP callbacks) |
| `InventoryService` | `InventoryServiceInterface` | Merchant inventory (categories, products, POS) |
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

        echo sprintf("Created order: %s\n", $response->order_id);
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

### BankAccountService

The `BankAccountServiceInterface` provides access to the Taler merchant Bank Accounts API.

```php
use MirrorPS\TalerBundle\Service\BankAccountServiceInterface;
use Taler\Api\BankAccounts\Dto\AccountAddDetails;
use Taler\Api\BankAccounts\Dto\AccountPatchDetails;
use Taler\Api\BankAccounts\Dto\BasicAuthFacadeCredentials;
use Taler\Api\BankAccounts\Dto\NoFacadeCredentials;
```

```php
class MyController
{
    public function manageBankAccounts(BankAccountServiceInterface $bankAccountService, string $hWire): void
    {
        $accounts = $bankAccountService->getAccounts();
        foreach ($accounts->accounts as $account) {
            echo sprintf("%s: %s\n", $account->h_wire, $account->payto_uri);
        }

        $created = $bankAccountService->createAccount(new AccountAddDetails(
            payto_uri: 'payto://iban/DE75512108001245126199?receiver-name=Sandbox',
            credit_facade_url: 'https://bank.example.test/facade',
            credit_facade_credentials: new BasicAuthFacadeCredentials(
                username: 'facade-user',
                password: 'facade-password',
            ),
        ));
        echo sprintf("Created bank account: %s\n", $created->h_wire);

        $account = $bankAccountService->getAccount($hWire);
        echo sprintf("%s: %s\n", $account->h_wire, $account->payto_uri);

        $bankAccountService->updateAccount($hWire, new AccountPatchDetails(
            credit_facade_credentials: new NoFacadeCredentials(),
        ));

        $bankAccountService->deleteAccount($hWire);
    }
}
```

### WireTransfersService

The `WireTransfersServiceInterface` wraps the Taler merchant **Wire Transfers** API (`private/transfers`). Use it to list incoming wire transfers and delete transfer records by serial ID.

```php
use MirrorPS\TalerBundle\Service\WireTransfersServiceInterface;
use Taler\Api\WireTransfers\Dto\GetTransfersRequest;

class MyController
{
    public function manageWireTransfers(WireTransfersServiceInterface $wireTransfers, string $tid): void
    {
        $list = $wireTransfers->getTransfers();
        foreach ($list->transfers as $transfer) {
            echo sprintf(
                "[%d] %s — %s (verified: %s)\n",
                $transfer->transfer_serial_id,
                $transfer->wtid,
                $transfer->credit_amount,
                $transfer->verified === true ? 'yes' : 'no',
            );
        }

        $filtered = $wireTransfers->getTransfers(new GetTransfersRequest(
            payto_uri: 'payto://iban/DE89370400440532013000?receiver-name=Example%20Merchant',
            after: '1700000000',
            limit: 20,
        ));

        $wireTransfers->deleteTransfer($tid);
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

```php
class MyController
{
    public function manageInstances(InstanceServiceInterface $instanceService, string $instanceId): void
    {
        $instances = $instanceService->getInstances();
        foreach ($instances->instances as $instance) {
            echo sprintf("Instance %s: %s\n", $instance->id, $instance->name);
        }

        $instance = $instanceService->getInstance($instanceId);
        echo sprintf("Name: %s\n", $instance->name);

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

        $instanceService->updateInstance($instanceId, new InstanceReconfigurationMessage(
            name: 'Coffee Shop Berlin',
            address: new \Taler\Api\Dto\Location(country: 'DE', town: 'Berlin'),
            jurisdiction: new \Taler\Api\Dto\Location(country: 'DE'),
            use_stefan: false,
            default_wire_transfer_delay: new RelativeTime(d_us: 0),
            default_pay_delay: new RelativeTime(d_us: 0),
        ));

        $challenge = $instanceService->deleteInstance($instanceId);
        $challenge = $instanceService->deleteInstance($instanceId, purge: true);
    }
}
```

> NOTE: 
> If your backend returns `404`, you are likely using a per-instance base URL such as `https://backend.demo.taler.net/instances/sandbox`.
> In that setup, use the single-instance private endpoint instead:
> `GET https://backend.demo.taler.net/instances/sandbox/private`

#### Authentication, tokens, and statistics

```php
class MyController
{
    public function manageInstanceExtras(InstanceServiceInterface $instanceService, string $instanceId): void
    {
        $instanceService->updateAuth(
            $instanceId,
            new InstanceAuthConfigToken(password: 'new-secret'),
        );

        $instanceService->forgotPassword(
            $instanceId,
            new InstanceAuthConfigToken(password: 'reset-secret'),
        );

        $token = $instanceService->getAccessToken($instanceId, new LoginTokenRequest(
            scope: 'readonly',
            duration: new RelativeTime(d_us: 3600000000),
            description: 'Backoffice session',
        ));

        $tokens = $instanceService->getAccessTokens(
            $instanceId,
            new GetAccessTokensRequest(limit: 20),
        );
        $instanceService->deleteAccessToken($instanceId);
        $instanceService->deleteAccessTokenBySerial($instanceId, 42);

        $kycStatus = $instanceService->getKycStatus(
            $instanceId,
            new GetKycStatusRequest(timeout_ms: 5000),
        );

        $instanceService->getMerchantStatisticsAmount(
            $instanceId,
            'revenue',
            new GetMerchantStatisticsAmountRequest(by: 'ANY'),
        );
        $instanceService->getMerchantStatisticsCounter(
            $instanceId,
            'orders',
            new GetMerchantStatisticsCounterRequest(by: 'BUCKET'),
        );
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

### DonauCharityService

The `DonauCharityServiceInterface` provides access to Donau charity link management.

```php
use MirrorPS\TalerBundle\Service\DonauCharityServiceInterface;
use Taler\Api\DonauCharity\Dto\PostDonauRequest;

class MyController
{
    public function manageDonauCharity(DonauCharityServiceInterface $donauService): void
    {
        $response = $donauService->getInstances();
        foreach ($response->donau_instances as $instance) {
            echo sprintf(
                "#%d %s (%s)\n",
                $instance->donau_instance_serial,
                $instance->charity_name,
                $instance->donau_url
            );
        }

        $challenge = $donauService->createDonauCharity(new PostDonauRequest(
            donau_url: 'https://donau.example.test',
            charity_id: 42,
        ));
        if ($challenge !== null) {
            echo "2FA challenge required.\n";
        }

        $donauService->deleteDonauCharityBySerial(42);
    }
}
```

### OtpDevicesService

The `OtpDevicesServiceInterface` wraps the Taler merchant OTP Devices API. Use it to register POS terminals or other devices that prove confirmation codes (TOTP) to the backend.

```php
use MirrorPS\TalerBundle\Service\OtpDevicesServiceInterface;
use Taler\Api\OtpDevices\Dto\GetOtpDeviceRequest;
use Taler\Api\OtpDevices\Dto\OtpDeviceAddDetails;
use Taler\Api\OtpDevices\Dto\OtpDevicePatchDetails;

class MyController
{
    public function manageOtpDevices(OtpDevicesServiceInterface $otpDevices, string $deviceId): void
    {
        $summary = $otpDevices->getOtpDevices();
        foreach ($summary->otp_devices as $entry) {
            echo sprintf("%s — %s\n", $entry->otp_device_id, $entry->device_description);
        }

        // otp_algorithm: 0|1|2 or NONE|TOTP_WITHOUT_PRICE|TOTP_WITH_PRICE (GNU Taler merchant API)
        $otpDevices->createOtpDevice(new OtpDeviceAddDetails(
            otp_device_id: 'pos-device-1',
            otp_device_description: 'Checkout counter',
            otp_key: 'JBSWY3DPEHPK3PXP',
            otp_algorithm: 1,
        ));

        $device = $otpDevices->getOtpDevice($deviceId);
        $otpDevices->getOtpDevice($deviceId, new GetOtpDeviceRequest(faketime: 1700000000));

        $otpDevices->updateOtpDevice($deviceId, new OtpDevicePatchDetails(
            otp_device_description: 'New checkout label',
            otp_algorithm: $device->otp_algorithm, // required when changing otp_key or similar fields
        ));

        $otpDevices->deleteOtpDevice($deviceId);
    }
}
```

### TemplatesService

The `TemplatesServiceInterface` wraps the Taler merchant Templates API. Templates define default contract fields (summary, amount, pay duration, and so on) for orders created from that template.

```php
use MirrorPS\TalerBundle\Service\TemplatesServiceInterface;
use Taler\Api\Dto\RelativeTime;
use Taler\Api\Templates\Dto\TemplateAddDetails;
use Taler\Api\Templates\Dto\TemplateContractDetails;
use Taler\Api\Templates\Dto\TemplatePatchDetails;

class MyController
{
    public function manageTemplates(TemplatesServiceInterface $templates, string $templateId): void
    {
        $summary = $templates->getTemplates();
        foreach ($summary->templates as $entry) {
            echo sprintf("%s — %s\n", $entry->template_id, $entry->template_description);
        }

        $template = $templates->getTemplate($templateId);
        echo sprintf("Description: %s\n", $template->template_description);

        $templates->createTemplate(new TemplateAddDetails(
            template_id: 'lunch-menu',
            template_description: 'Lunch special',
            template_contract: new TemplateContractDetails(
                minimum_age: 0,
                pay_duration: new RelativeTime(d_us: 3600000000),
                summary: 'Daily lunch',
                currency: 'EUR',
                amount: 'EUR:8.50',
            ),
            otp_id: null,
            editable_defaults: null,
        ));

        $templates->updateTemplate($templateId, new TemplatePatchDetails(
            template_description: 'Lunch special (updated)',
            template_contract: new TemplateContractDetails(
                minimum_age: 0,
                pay_duration: new RelativeTime(d_us: 'forever'),
                summary: 'Daily lunch',
                currency: 'EUR',
                amount: 'EUR:9.00',
            ),
        ));

        $templates->deleteTemplate($templateId);
    }
}
```

### TokenFamiliesService

The `TokenFamiliesServiceInterface` wraps the Taler merchant Token Families API. 

```php
use MirrorPS\TalerBundle\Service\TokenFamiliesServiceInterface;
use Taler\Api\Dto\RelativeTime;
use Taler\Api\Dto\Timestamp;
use Taler\Api\TokenFamilies\Dto\TokenFamilyCreateRequest;
use Taler\Api\TokenFamilies\Dto\TokenFamilyUpdateRequest;

class MyController
{
    public function manageTokenFamilies(TokenFamiliesServiceInterface $tokenFamilies, string $slug): void
    {
        $list = $tokenFamilies->getTokenFamilies();
        foreach ($list->token_families as $entry) {
            echo sprintf("%s — %s (%s)\n", $entry->slug, $entry->name, $entry->kind);
        }

        $details = $tokenFamilies->getTokenFamily($slug);
        echo sprintf("Issued: %d, used: %d\n", $details->issued, $details->used);

        // kind: discount|subscription; duration >= validity_granularity + start_offset
        $tokenFamilies->createTokenFamily(new TokenFamilyCreateRequest(
            slug: 'summer-discount',
            name: 'Summer sale',
            description: 'Seasonal discount tokens',
            valid_before: new Timestamp(t_s: 'never'),
            duration: new RelativeTime(d_us: 3_600_000_000),
            validity_granularity: new RelativeTime(d_us: 3_600_000_000),
            start_offset: new RelativeTime(d_us: 0),
            kind: 'discount',
        ));

        $tokenFamilies->updateTokenFamily($slug, new TokenFamilyUpdateRequest(
            name: 'Summer sale (updated)',
            description: 'Updated description',
            valid_after: new Timestamp(t_s: 0),
            valid_before: new Timestamp(t_s: 'never'),
        ));

        $tokenFamilies->deleteTokenFamily($slug);
    }
}
```

### WebhooksService

The `WebhooksServiceInterface` wraps the Taler merchant **Webhooks** API (`private/webhooks`). Webhooks let the backend invoke your HTTP endpoint when events occur (for example `order.paid`).

```php
use MirrorPS\TalerBundle\Service\WebhooksServiceInterface;
use Taler\Api\Dto\Url;
use Taler\Api\Webhooks\Dto\WebhookAddDetails;
use Taler\Api\Webhooks\Dto\WebhookDetails;
use Taler\Api\Webhooks\Dto\WebhookPatchDetails;
use Taler\Api\Webhooks\Dto\WebhookSummaryResponse;

class MyController
{
    public function manageWebhooks(WebhooksServiceInterface $webhooks, string $webhookId): void
    {
        $summary = $webhooks->getWebhooks();
        if ($summary instanceof WebhookSummaryResponse) {
            foreach ($summary->webhooks as $entry) {
                echo sprintf("%s — %s\n", $entry->webhook_id, $entry->event_type);
            }
        }

        $details = $webhooks->getWebhook($webhookId);
        if ($details instanceof WebhookDetails) {
            echo sprintf("%s %s\n", $details->http_method, $details->url);
        }

        $webhooks->createWebhook(new WebhookAddDetails(
            webhook_id: 'orders-paid',
            event_type: 'order.paid',
            url: Url::fromString('https://example.com/taler-webhook'),
            http_method: 'POST',
        ));

        $webhooks->updateWebhook($webhookId, new WebhookPatchDetails(
            event_type: 'order.paid',
            url: Url::fromString('https://example.com/taler-webhook-v2'),
            http_method: 'POST',
        ));

        $webhooks->deleteWebhook($webhookId);
    }
}
```

### InventoryService

The `InventoryServiceInterface` wraps the GNU Taler merchant **Inventory** API (`private/inventory`). Use it to manage product categories, stock, POS configuration, and short-lived quantity locks.

#### Categories and products

```php
use MirrorPS\TalerBundle\Service\InventoryServiceInterface;
use Taler\Api\Inventory\Dto\CategoryCreateRequest;
use Taler\Api\Inventory\Dto\CategoryCreatedResponse;
use Taler\Api\Inventory\Dto\GetProductsRequest;
use Taler\Api\Inventory\Dto\InventorySummaryResponse;
use Taler\Api\Inventory\Dto\ProductAddDetail;
use Taler\Api\Inventory\Dto\ProductDetail;
use Taler\Api\Inventory\Dto\ProductPatchDetail;

class MyController
{
    public function manageInventory(
        InventoryServiceInterface $inventory,
        int $categoryId,
        string $productId,
    ): void {
        $list = $inventory->getCategories();
        foreach ($list->categories as $entry) {
            echo sprintf(
                "Category %d: %s (%d products)\n",
                $entry->category_id,
                $entry->name,
                $entry->product_count
            );
        }

        $category = $inventory->getCategory($categoryId);
        echo $category->name . "\n";
        foreach ($category->products as $product) {
            echo $product->product_id . "\n";
        }

        $created = $inventory->createCategory(new CategoryCreateRequest(
            name: 'Beverages',
            name_i18n: ['de' => 'Getränke'],
        ));
        if ($created instanceof CategoryCreatedResponse) {
            echo 'Created category ID: ' . $created->category_id . "\n";
        }

        $inventory->updateCategory($categoryId, new CategoryCreateRequest(name: 'Drinks'));
        $inventory->deleteCategory($categoryId);

        $summary = $inventory->getProducts(new GetProductsRequest(limit: 20));
        if ($summary instanceof InventorySummaryResponse) {
            foreach ($summary->products as $entry) {
                echo $entry->product_id . ' (serial ' . $entry->product_serial . ")\n";
            }
        }

        $product = $inventory->getProduct($productId);
        if ($product instanceof ProductDetail) {
            echo $product->product_name . ' — ' . $product->price . "\n";
        }

        $inventory->createProduct(new ProductAddDetail(
            product_id: 'coffee-1kg',
            description: 'Arabica beans 1kg',
            unit: 'kg',
            price: 'EUR:12.50',
            total_stock: 100,
            product_name: 'Coffee Beans',
        ));

        $inventory->updateProduct($productId, new ProductPatchDetail(
            description: 'Arabica beans 1kg (fresh roast)',
            unit: 'kg',
            price: 'EUR:12.50',
            total_stock: 150,
        ));

        $inventory->deleteProduct($productId);
    }
}
```

#### POS configuration

```php
use MirrorPS\TalerBundle\Service\InventoryServiceInterface;

class MyController
{
    public function posConfig(InventoryServiceInterface $inventory): void
    {
        $pos = $inventory->getPos();

        foreach ($pos->categories as $cat) {
            echo $cat->id . ':' . $cat->name . "\n";
        }

        foreach ($pos->products as $p) {
            echo $p->product_id . ' => ' . $p->price . "\n";
        }
    }
}
```

#### Lock product quantity

```php
use MirrorPS\TalerBundle\Service\InventoryServiceInterface;
use Taler\Api\Dto\RelativeTime;
use Taler\Api\Inventory\Dto\LockRequest;

class MyController
{
    public function lockStock(InventoryServiceInterface $inventory): void
    {
        $inventory->lockProduct('coffee-1kg', new LockRequest(
            lock_uuid: '123e4567-e89b-12d3-a456-426614174000',
            duration: new RelativeTime(60_000_000),
            quantity: 2,
        ));
    }
}
```

### TwoFactorAuthService

The `TwoFactorAuthServiceInterface` wraps the GNU Taler merchant **Two-Factor Authentication** API (TAN challenges). Use it after another API returns a `ChallengeResponse`.

```php
use MirrorPS\TalerBundle\Service\TwoFactorAuthServiceInterface;
use Taler\Api\TwoFactorAuth\Dto\MerchantChallengeSolveRequest;

class MyController
{
    public function manageTwoFactorChallenge(
        TwoFactorAuthServiceInterface $twoFa,
        string $instanceId,
        string $challengeId,
        string $tan,
    ): void {
        $status = $twoFa->requestChallenge($instanceId, $challengeId);
        echo sprintf(
            "Solve before: %s, earliest retransmit: %s\n",
            (string) $status->solve_expiration->t_s,
            (string) $status->earliest_retransmission->t_s
        );

        $twoFa->confirmChallenge(
            $instanceId,
            $challengeId,
            new MerchantChallengeSolveRequest(tan: $tan),
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

## Testing

```bash
vendor/bin/phpunit
```

## License

MIT

## Funding

This project is funded through [NGI TALER Fund](https://nlnet.nl/taler), a fund established by [NLnet](https://nlnet.nl) with financial support from the European Commission's [Next Generation Internet](https://ngi.eu) program. Learn more at the [NLnet project page](https://nlnet.nl/project/TalerPHP).

[<img src="https://nlnet.nl/logo/banner.png" alt="NLnet foundation logo" width="20%" />](https://nlnet.nl)

