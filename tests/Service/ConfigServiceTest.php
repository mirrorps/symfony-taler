<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Service;

use MirrorPS\TalerBundle\Service\ConfigService;
use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\Config\ConfigClient;
use Taler\Api\Config\Dto\MerchantVersionResponse;
use Taler\Taler as TalerClient;

final class ConfigServiceTest extends TestCase
{
    private ConfigClient $configClient;
    private ConfigService $service;

    protected function setUp(): void
    {
        $this->configClient = $this->createMock(ConfigClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->method('configApi')->willReturn($this->configClient);

        $taler = new Taler($client);
        $this->service = new ConfigService($taler);
    }

    public function testGetConfigDelegatesToClient(): void
    {
        $expected = $this->createMock(MerchantVersionResponse::class);

        $this->configClient->expects(self::once())
            ->method('getConfig')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getConfig());
    }

    public function testGetConfigPassesHeaders(): void
    {
        $headers = ['X-Custom' => 'value'];
        $expected = $this->createMock(MerchantVersionResponse::class);

        $this->configClient->expects(self::once())
            ->method('getConfig')
            ->with($headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getConfig($headers));
    }

    public function testGetConfigAsyncDelegatesToClient(): void
    {
        $expected = 'async-promise';

        $this->configClient->expects(self::once())
            ->method('getConfigAsync')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getConfigAsync());
    }
}
