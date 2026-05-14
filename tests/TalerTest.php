<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests;

use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\BankAccounts\BankAccountClient;
use Taler\Api\Config\ConfigClient;
use Taler\Api\DonauCharity\DonauCharityClient;
use Taler\Api\Instance\InstanceClient;
use Taler\Api\OtpDevices\OtpDevicesClient;
use Taler\Api\Order\OrderClient;
use Taler\Api\Templates\TemplatesClient;
use Taler\Api\TokenFamilies\TokenFamiliesClient;
use Taler\Api\TwoFactorAuth\TwoFactorAuthClient;
use Taler\Api\Wallet\WalletClient;
use Taler\Api\Webhooks\WebhooksClient;
use Taler\Taler as TalerClient;

final class TalerTest extends TestCase
{
    public function testOrdersReturnsOrderClient(): void
    {
        $orderClient = $this->createMock(OrderClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->expects(self::once())
            ->method('order')
            ->willReturn($orderClient);

        $taler = new Taler($client);

        self::assertSame($orderClient, $taler->orders());
    }

    public function testBankAccountsReturnsBankAccountClient(): void
    {
        $bankAccountClient = $this->createMock(BankAccountClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->expects(self::once())
            ->method('bankAccount')
            ->willReturn($bankAccountClient);

        $taler = new Taler($client);

        self::assertSame($bankAccountClient, $taler->bankAccounts());
    }

    public function testInstanceReturnsInstanceClient(): void
    {
        $instanceClient = $this->createMock(InstanceClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->expects(self::once())
            ->method('instance')
            ->willReturn($instanceClient);

        $taler = new Taler($client);

        self::assertSame($instanceClient, $taler->instance());
    }

    public function testConfigReturnsConfigClient(): void
    {
        $configClient = $this->createMock(ConfigClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->expects(self::once())
            ->method('configApi')
            ->willReturn($configClient);

        $taler = new Taler($client);

        self::assertSame($configClient, $taler->config());
    }

    public function testDonauCharityReturnsDonauCharityClient(): void
    {
        $donauCharityClient = $this->createMock(DonauCharityClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->expects(self::once())
            ->method('donauCharity')
            ->willReturn($donauCharityClient);

        $taler = new Taler($client);

        self::assertSame($donauCharityClient, $taler->donauCharity());
    }

    public function testOtpDevicesReturnsOtpDevicesClient(): void
    {
        $otpDevicesClient = $this->createMock(OtpDevicesClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->expects(self::once())
            ->method('otpDevices')
            ->willReturn($otpDevicesClient);

        $taler = new Taler($client);

        self::assertSame($otpDevicesClient, $taler->otpDevices());
    }

    public function testTemplatesReturnsTemplatesClient(): void
    {
        $templatesClient = $this->createMock(TemplatesClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->expects(self::once())
            ->method('templates')
            ->willReturn($templatesClient);

        $taler = new Taler($client);

        self::assertSame($templatesClient, $taler->templates());
    }

    public function testTokenFamiliesReturnsTokenFamiliesClient(): void
    {
        $tokenFamiliesClient = $this->createMock(TokenFamiliesClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->expects(self::once())
            ->method('tokenFamilies')
            ->willReturn($tokenFamiliesClient);

        $taler = new Taler($client);

        self::assertSame($tokenFamiliesClient, $taler->tokenFamilies());
    }

    public function testWalletReturnsWalletClient(): void
    {
        $walletClient = $this->createMock(WalletClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->expects(self::once())
            ->method('wallet')
            ->willReturn($walletClient);

        $taler = new Taler($client);

        self::assertSame($walletClient, $taler->wallet());
    }

    public function testTwoFactorAuthReturnsTwoFactorAuthClient(): void
    {
        $twoFactorAuthClient = $this->createMock(TwoFactorAuthClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->expects(self::once())
            ->method('twoFactorAuth')
            ->willReturn($twoFactorAuthClient);

        $taler = new Taler($client);

        self::assertSame($twoFactorAuthClient, $taler->twoFactorAuth());
    }

    public function testWebhooksReturnsWebhooksClient(): void
    {
        $webhooksClient = $this->createMock(WebhooksClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->expects(self::once())
            ->method('webhooks')
            ->willReturn($webhooksClient);

        $taler = new Taler($client);

        self::assertSame($webhooksClient, $taler->webhooks());
    }

    public function testGetClientReturnsFactory(): void
    {
        $client = $this->createMock(TalerClient::class);
        $taler = new Taler($client);

        self::assertSame($client, $taler->getClient());
    }
}
