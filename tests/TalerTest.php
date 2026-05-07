<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests;

use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\Config\ConfigClient;
use Taler\Api\Instance\InstanceClient;
use Taler\Api\Order\OrderClient;
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

    public function testGetClientReturnsFactory(): void
    {
        $client = $this->createMock(TalerClient::class);
        $taler = new Taler($client);

        self::assertSame($client, $taler->getClient());
    }
}
