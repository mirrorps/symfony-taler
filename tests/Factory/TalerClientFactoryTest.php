<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Factory;

use MirrorPS\TalerBundle\Factory\TalerClientFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Log\NullLogger;

final class TalerClientFactoryTest extends TestCase
{
    public function testConstructorAcceptsConfig(): void
    {
        $config = $this->baseConfig();

        $factory = new TalerClientFactory($config);

        self::assertInstanceOf(TalerClientFactory::class, $factory);
    }

    public function testBuildFactoryOptionsPassesLoggerAndDebugFlags(): void
    {
        $config = $this->baseConfig();
        $config['debug_logging_enabled'] = true;

        $factory = new TalerClientFactory($config, null, new NullLogger());
        $options = $this->extractFactoryOptions($factory);

        self::assertTrue($options['debugLoggingEnabled']);
        self::assertInstanceOf(NullLogger::class, $options['logger']);
        self::assertArrayNotHasKey('client', $options);
        self::assertArrayNotHasKey('httpClient', $options);
    }

    public function testBuildFactoryOptionsPassesInjectedHttpClient(): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $factory = new TalerClientFactory($this->baseConfig(), $httpClient);
        $options = $this->extractFactoryOptions($factory);

        self::assertSame($httpClient, $options['client']);
    }

    public function testBuildFactoryOptionsOmitsClientForSdkDiscovery(): void
    {
        $factory = new TalerClientFactory($this->baseConfig());
        $options = $this->extractFactoryOptions($factory);

        self::assertFalse($options['debugLoggingEnabled']);
        self::assertArrayNotHasKey('client', $options);
        self::assertArrayNotHasKey('logger', $options);
    }

    /**
     * @return array<string, mixed>
     */
    private function baseConfig(): array
    {
        return [
            'base_url' => 'https://backend.demo.taler.net',
            'username' => 'user',
            'password' => 'pass',
            'instance' => 'default',
            'token' => null,
            'scope' => null,
            'debug_logging_enabled' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function extractFactoryOptions(TalerClientFactory $factory): array
    {
        $method = new \ReflectionMethod(TalerClientFactory::class, 'buildFactoryOptions');
        $method->setAccessible(true);

        return $method->invoke($factory);
    }
}
