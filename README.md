# TalerBundle

> **Note:** This package is under active development and is subject to frequent code changes.

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

## Usage

The bundle registers services that can be injected via autowiring.

### Available Services

| Service           | Interface                  | Description                |
|-------------------|----------------------------|----------------------------|
| `OrderService`    | `OrderServiceInterface`    | Full order management      |
| `BankAccountService` | `BankAccountServiceInterface` | Bank account management |
| `InstanceService` | `InstanceServiceInterface` | Instance management        |
| `ConfigService`   | `ConfigServiceInterface`   | Merchant config endpoint   |
| `DonauCharityService` | `DonauCharityServiceInterface` | Donau charity linking |
| `OtpDevicesService` | `OtpDevicesServiceInterface` | OTP devices (POS confirmation) |
| `TemplatesService` | `TemplatesServiceInterface` | Order templates (contract presets) |
| `TokenFamiliesService` | `TokenFamiliesServiceInterface` | Token families (discount / subscription) |
| `TwoFactorAuthService` | `TwoFactorAuthServiceInterface` | TAN challenge request / confirm |
| `WebhooksService` | `WebhooksServiceInterface` | Merchant webhooks (HTTP callbacks) |
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

### BankAccountService

The `BankAccountServiceInterface` provides access to the Taler merchant Bank Accounts API.

```php
use MirrorPS\TalerBundle\Service\BankAccountServiceInterface;
use Taler\Api\BankAccounts\Dto\AccountAddDetails;
use Taler\Api\BankAccounts\Dto\AccountPatchDetails;
use Taler\Api\BankAccounts\Dto\BasicAuthFacadeCredentials;
use Taler\Api\BankAccounts\Dto\NoFacadeCredentials;
```

#### List Bank Accounts

```php
class MyController
{
    public function listBankAccounts(BankAccountServiceInterface $bankAccountService): void
    {
        $accounts = $bankAccountService->getAccounts();

        foreach ($accounts->accounts as $account) {
            echo sprintf("%s: %s\n", $account->h_wire, $account->payto_uri);
        }
    }
}
```

#### Create a Bank Account

```php
class MyController
{
    public function createBankAccount(BankAccountServiceInterface $bankAccountService): void
    {
        $response = $bankAccountService->createAccount(new AccountAddDetails(
            payto_uri: 'payto://iban/DE75512108001245126199?receiver-name=Sandbox',
            credit_facade_url: 'https://bank.example.test/facade',
            credit_facade_credentials: new BasicAuthFacadeCredentials(
                username: 'facade-user',
                password: 'facade-password',
            ),
        ));

        echo sprintf("Created bank account: %s\n", $response->h_wire);
    }
}
```

#### Get a Bank Account

```php
class MyController
{
    public function showBankAccount(BankAccountServiceInterface $bankAccountService, string $hWire): void
    {
        $account = $bankAccountService->getAccount($hWire);

        echo sprintf("%s: %s\n", $account->h_wire, $account->payto_uri);
    }
}
```

#### Update a Bank Account

```php
class MyController
{
    public function updateBankAccount(BankAccountServiceInterface $bankAccountService, string $hWire): void
    {
        $bankAccountService->updateAccount($hWire, new AccountPatchDetails(
            credit_facade_credentials: new NoFacadeCredentials(),
        ));

        echo sprintf("Updated bank account: %s\n", $hWire);
    }
}
```

#### Delete a Bank Account

```php
class MyController
{
    public function deleteBankAccount(BankAccountServiceInterface $bankAccountService, string $hWire): void
    {
        $bankAccountService->deleteAccount($hWire);

        echo sprintf("Deleted bank account: %s\n", $hWire);
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

### DonauCharityService

The `DonauCharityServiceInterface` provides access to Donau charity link management.

```php
use MirrorPS\TalerBundle\Service\DonauCharityServiceInterface;
use Taler\Api\DonauCharity\Dto\PostDonauRequest;

class MyController
{
    public function listDonauLinks(DonauCharityServiceInterface $donauService): void
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
    }

    public function addDonauLink(DonauCharityServiceInterface $donauService): void
    {
        $challenge = $donauService->createDonauCharity(new PostDonauRequest(
            donau_url: 'https://donau.example.test',
            charity_id: 42,
        ));

        if ($challenge !== null) {
            echo "2FA challenge required.\n";
        }
    }

    public function removeDonauLink(DonauCharityServiceInterface $donauService): void
    {
        $donauService->deleteDonauCharityBySerial(42);
    }
}
```

### OtpDevicesService

The `OtpDevicesServiceInterface` wraps the Taler merchant OTP Devices API. Use it to register POS terminals or other devices that prove confirmation codes (TOTP) to the backend.

#### List OTP devices

```php
use MirrorPS\TalerBundle\Service\OtpDevicesServiceInterface;

class MyController
{
    public function listOtpDevices(OtpDevicesServiceInterface $otpDevices): void
    {
        $summary = $otpDevices->getOtpDevices();

        foreach ($summary->otp_devices as $entry) {
            echo sprintf("%s — %s\n", $entry->otp_device_id, $entry->device_description);
        }
    }
}
```

#### Create an OTP device

```php
use MirrorPS\TalerBundle\Service\OtpDevicesServiceInterface;
use Taler\Api\OtpDevices\Dto\OtpDeviceAddDetails;

class MyController
{
    public function registerDevice(OtpDevicesServiceInterface $otpDevices): void
    {
        $details = new OtpDeviceAddDetails(
            otp_device_id: 'pos-device-1',
            otp_device_description: 'Checkout counter',
            otp_key: 'JBSWY3DPEHPK3PXP',
            otp_algorithm: 1,
        );

        $otpDevices->createOtpDevice($details);
    }
}
```

`otp_algorithm` may be integers `0`, `1`, `2` or strings `NONE`, `TOTP_WITHOUT_PRICE`, `TOTP_WITH_PRICE` (see GNU Taler merchant API documentation).

#### Get one device

```php
use MirrorPS\TalerBundle\Service\OtpDevicesServiceInterface;
use Taler\Api\OtpDevices\Dto\GetOtpDeviceRequest;

class MyController
{
    public function showDevice(OtpDevicesServiceInterface $otpDevices, string $deviceId): void
    {
        $device = $otpDevices->getOtpDevice($deviceId);

        echo sprintf("Description: %s\n", $device->device_description);
    }

    public function showDeviceWithQuery(OtpDevicesServiceInterface $otpDevices, string $deviceId): void
    {
        $request = new GetOtpDeviceRequest(
            faketime: 1700000000
        );

        $device = $otpDevices->getOtpDevice($deviceId, $request);
    }
}
```

#### Update an OTP device

```php
use MirrorPS\TalerBundle\Service\OtpDevicesServiceInterface;
use Taler\Api\OtpDevices\Dto\OtpDevicePatchDetails;

class MyController
{
    public function relabelDevice(OtpDevicesServiceInterface $otpDevices, string $deviceId): void
    {
        $current = $otpDevices->getOtpDevice($deviceId);

        $otpDevices->updateOtpDevice($deviceId, new OtpDevicePatchDetails(
            otp_device_description: 'New checkout label',
            otp_algorithm: $current->otp_algorithm,
        ));
    }
}
```

If you change `otp_key` or other fields, include `otp_algorithm` the same way unless you set an explicit new value.

#### Delete an OTP device

```php
use MirrorPS\TalerBundle\Service\OtpDevicesServiceInterface;

class MyController
{
    public function removeDevice(OtpDevicesServiceInterface $otpDevices, string $deviceId): void
    {
        $otpDevices->deleteOtpDevice($deviceId);
    }
}
```

### TemplatesService

The `TemplatesServiceInterface` wraps the Taler merchant Templates API. Templates define default contract fields (summary, amount, pay duration, and so on) for orders created from that template.

#### List templates

```php
use MirrorPS\TalerBundle\Service\TemplatesServiceInterface;

class MyController
{
    public function listTemplates(TemplatesServiceInterface $templates): void
    {
        $summary = $templates->getTemplates();

        foreach ($summary->templates as $entry) {
            echo sprintf("%s — %s\n", $entry->template_id, $entry->template_description);
        }
    }
}
```

#### Get one template

```php
use MirrorPS\TalerBundle\Service\TemplatesServiceInterface;

class MyController
{
    public function showTemplate(TemplatesServiceInterface $templates, string $templateId): void
    {
        $template = $templates->getTemplate($templateId);

        echo sprintf("Description: %s\n", $template->template_description);
    }
}
```

#### Create a template

```php
use MirrorPS\TalerBundle\Service\TemplatesServiceInterface;
use Taler\Api\Dto\RelativeTime;
use Taler\Api\Templates\Dto\TemplateAddDetails;
use Taler\Api\Templates\Dto\TemplateContractDetails;

class MyController
{
    public function addTemplate(TemplatesServiceInterface $templates): void
    {
        $details = new TemplateAddDetails(
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
        );

        $templates->createTemplate($details);
    }
}
```

#### Update a template

```php
use MirrorPS\TalerBundle\Service\TemplatesServiceInterface;
use Taler\Api\Dto\RelativeTime;
use Taler\Api\Templates\Dto\TemplateContractDetails;
use Taler\Api\Templates\Dto\TemplatePatchDetails;

class MyController
{
    public function patchTemplate(TemplatesServiceInterface $templates, string $templateId): void
    {
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
    }
}
```

#### Delete a template

```php
use MirrorPS\TalerBundle\Service\TemplatesServiceInterface;

class MyController
{
    public function removeTemplate(TemplatesServiceInterface $templates, string $templateId): void
    {
        $templates->deleteTemplate($templateId);
    }
}
```

### TokenFamiliesService

The `TokenFamiliesServiceInterface` wraps the Taler merchant Token Families API. 

#### List token families

```php
use MirrorPS\TalerBundle\Service\TokenFamiliesServiceInterface;

class MyController
{
    public function listTokenFamilies(TokenFamiliesServiceInterface $tokenFamilies): void
    {
        $list = $tokenFamilies->getTokenFamilies();

        foreach ($list->token_families as $entry) {
            echo sprintf("%s — %s (%s)\n", $entry->slug, $entry->name, $entry->kind);
        }
    }
}
```

#### Get one token family

```php
use MirrorPS\TalerBundle\Service\TokenFamiliesServiceInterface;

class MyController
{
    public function showTokenFamily(TokenFamiliesServiceInterface $tokenFamilies, string $slug): void
    {
        $details = $tokenFamilies->getTokenFamily($slug);

        echo sprintf("Issued: %d, used: %d\n", $details->issued, $details->used);
    }
}
```

#### Create a token family

```php
use MirrorPS\TalerBundle\Service\TokenFamiliesServiceInterface;
use Taler\Api\Dto\RelativeTime;
use Taler\Api\Dto\Timestamp;
use Taler\Api\TokenFamilies\Dto\TokenFamilyCreateRequest;

class MyController
{
    public function addTokenFamily(TokenFamiliesServiceInterface $tokenFamilies): void
    {
        $request = new TokenFamilyCreateRequest(
            slug: 'summer-discount',
            name: 'Summer sale',
            description: 'Seasonal discount tokens',
            valid_before: new Timestamp(t_s: 'never'),
            // duration must be >= validity_granularity + start_offset; granularity must be a fixed step (1m, 1h, 1d, …).
            duration: new RelativeTime(d_us: 3_600_000_000),
            validity_granularity: new RelativeTime(d_us: 3_600_000_000),
            start_offset: new RelativeTime(d_us: 0),
            kind: 'discount',
        );

        $tokenFamilies->createTokenFamily($request);
    }
}
```

`kind` must be `discount` or `subscription`. Optional fields on `TokenFamilyCreateRequest` include `description_i18n`, `extra_data`, and `valid_after`.

#### Update a token family

```php
use MirrorPS\TalerBundle\Service\TokenFamiliesServiceInterface;
use Taler\Api\Dto\Timestamp;
use Taler\Api\TokenFamilies\Dto\TokenFamilyUpdateRequest;

class MyController
{
    public function patchTokenFamily(TokenFamiliesServiceInterface $tokenFamilies, string $slug): void
    {
        $tokenFamilies->updateTokenFamily($slug, new TokenFamilyUpdateRequest(
            name: 'Summer sale (updated)',
            description: 'Updated description',
            valid_after: new Timestamp(t_s: 0),
            valid_before: new Timestamp(t_s: 'never'),
        ));
    }
}
```

#### Delete a token family

```php
use MirrorPS\TalerBundle\Service\TokenFamiliesServiceInterface;

class MyController
{
    public function removeTokenFamily(TokenFamiliesServiceInterface $tokenFamilies, string $slug): void
    {
        $tokenFamilies->deleteTokenFamily($slug);
    }
}
```

### WebhooksService

The `WebhooksServiceInterface` wraps the Taler merchant **Webhooks** API (`private/webhooks`). Webhooks let the backend invoke your HTTP endpoint when events occur (for example `order.paid`).

#### List webhooks

```php
use MirrorPS\TalerBundle\Service\WebhooksServiceInterface;
use Taler\Api\Webhooks\Dto\WebhookSummaryResponse;

class MyController
{
    public function listWebhooks(WebhooksServiceInterface $webhooks): void
    {
        $summary = $webhooks->getWebhooks();
        if (!$summary instanceof WebhookSummaryResponse) {
            return;
        }

        foreach ($summary->webhooks as $entry) {
            echo sprintf("%s — %s\n", $entry->webhook_id, $entry->event_type);
        }
    }
}
```

#### Get one webhook

```php
use MirrorPS\TalerBundle\Service\WebhooksServiceInterface;
use Taler\Api\Webhooks\Dto\WebhookDetails;

class MyController
{
    public function showWebhook(WebhooksServiceInterface $webhooks, string $webhookId): void
    {
        $details = $webhooks->getWebhook($webhookId);
        if (!$details instanceof WebhookDetails) {
            return;
        }

        echo sprintf("%s %s\n", $details->http_method, $details->url);
    }
}
```

#### Create a webhook

```php
use MirrorPS\TalerBundle\Service\WebhooksServiceInterface;
use Taler\Api\Dto\Url;
use Taler\Api\Webhooks\Dto\WebhookAddDetails;

class MyController
{
    public function addWebhook(WebhooksServiceInterface $webhooks): void
    {
        $details = new WebhookAddDetails(
            webhook_id: 'orders-paid',
            event_type: 'order.paid',
            url: Url::fromString('https://example.com/taler-webhook'),
            http_method: 'POST',
        );

        $webhooks->createWebhook($details);
    }
}
```

#### Update a webhook

```php
use MirrorPS\TalerBundle\Service\WebhooksServiceInterface;
use Taler\Api\Dto\Url;
use Taler\Api\Webhooks\Dto\WebhookPatchDetails;

class MyController
{
    public function patchWebhook(WebhooksServiceInterface $webhooks, string $webhookId): void
    {
        $webhooks->updateWebhook($webhookId, new WebhookPatchDetails(
            event_type: 'order.paid',
            url: Url::fromString('https://example.com/taler-webhook-v2'),
            http_method: 'POST',
        ));
    }
}
```

#### Delete a webhook

```php
use MirrorPS\TalerBundle\Service\WebhooksServiceInterface;

class MyController
{
    public function removeWebhook(WebhooksServiceInterface $webhooks, string $webhookId): void
    {
        $webhooks->deleteWebhook($webhookId);
    }
}
```

### TwoFactorAuthService

The `TwoFactorAuthServiceInterface` wraps the GNU Taler merchant **Two-Factor Authentication** API (TAN challenges). Use it after another API returns a `ChallengeResponse`.

#### Request a TAN for a challenge

```php
use MirrorPS\TalerBundle\Service\TwoFactorAuthServiceInterface;

class MyController
{
    public function requestTan(TwoFactorAuthServiceInterface $twoFa, string $instanceId, string $challengeId): void
    {
        $status = $twoFa->requestChallenge($instanceId, $challengeId);

        echo sprintf(
            "Solve before: %s, earliest retransmit: %s\n",
            (string) $status->solve_expiration->t_s,
            (string) $status->earliest_retransmission->t_s
        );
    }
}
```

#### Confirm a challenge with the TAN

```php
use MirrorPS\TalerBundle\Service\TwoFactorAuthServiceInterface;
use Taler\Api\TwoFactorAuth\Dto\MerchantChallengeSolveRequest;

class MyController
{
    public function submitTan(
        TwoFactorAuthServiceInterface $twoFa,
        string $instanceId,
        string $challengeId,
        string $tan,
    ): void {
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

