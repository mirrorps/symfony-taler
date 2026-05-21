<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle;

use Taler\Api\BankAccounts\BankAccountClient;
use Taler\Api\Config\ConfigClient;
use Taler\Api\DonauCharity\DonauCharityClient;
use Taler\Api\Inventory\InventoryClient;
use Taler\Api\Instance\InstanceClient;
use Taler\Api\OtpDevices\OtpDevicesClient;
use Taler\Api\Order\OrderClient;
use Taler\Api\Templates\TemplatesClient;
use Taler\Api\TokenFamilies\TokenFamiliesClient;
use Taler\Api\TwoFactorAuth\TwoFactorAuthClient;
use Taler\Api\Wallet\WalletClient;
use Taler\Api\Webhooks\WebhooksClient;
use Taler\Api\WireTransfers\WireTransfersClient;
use Taler\Taler as TalerClient;

final class Taler
{
    private TalerClient $client;

    public function __construct(TalerClient $client)
    {
        $this->client = $client;
    }

    public function orders(): OrderClient
    {
        return $this->client->order();
    }

    public function bankAccounts(): BankAccountClient
    {
        return $this->client->bankAccount();
    }

    public function inventory(): InventoryClient
    {
        return $this->client->inventory();
    }

    public function instance(): InstanceClient
    {
        return $this->client->instance();
    }

    public function config(): ConfigClient
    {
        return $this->client->configApi();
    }

    public function donauCharity(): DonauCharityClient
    {
        return $this->client->donauCharity();
    }

    public function otpDevices(): OtpDevicesClient
    {
        return $this->client->otpDevices();
    }

    public function templates(): TemplatesClient
    {
        return $this->client->templates();
    }

    public function tokenFamilies(): TokenFamiliesClient
    {
        return $this->client->tokenFamilies();
    }

    public function wallet(): WalletClient
    {
        return $this->client->wallet();
    }

    public function twoFactorAuth(): TwoFactorAuthClient
    {
        return $this->client->twoFactorAuth();
    }

    public function webhooks(): WebhooksClient
    {
        return $this->client->webhooks();
    }

    public function wireTransfers(): WireTransfersClient
    {
        return $this->client->wireTransfers();
    }

    public function getClient(): TalerClient
    {
        return $this->client;
    }
}
