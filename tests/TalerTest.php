<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests;

use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
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

    public function testGetClientReturnsFactory(): void
    {
        $client = $this->createMock(TalerClient::class);
        $taler = new Taler($client);

        self::assertSame($client, $taler->getClient());
    }
}
