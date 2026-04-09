<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\DependencyInjection;

use MirrorPS\TalerBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    private Processor $processor;
    private Configuration $configuration;

    protected function setUp(): void
    {
        $this->processor = new Processor();
        $this->configuration = new Configuration();
    }

    public function testMinimalConfig(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            ['base_url' => 'https://backend.demo.taler.net/instances/sandbox'],
        ]);

        self::assertSame('https://backend.demo.taler.net/instances/sandbox', $config['base_url']);
        self::assertNull($config['username']);
        self::assertNull($config['password']);
        self::assertNull($config['instance']);
        self::assertNull($config['token']);
        self::assertNull($config['scope']);
    }

    public function testFullCredentialConfig(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'base_url' => 'https://backend.demo.taler.net',
                'username' => 'merchant-user',
                'password' => 'merchant-pass',
                'instance' => 'shop-1',
                'scope' => 'write',
            ],
        ]);

        self::assertSame('https://backend.demo.taler.net', $config['base_url']);
        self::assertSame('merchant-user', $config['username']);
        self::assertSame('merchant-pass', $config['password']);
        self::assertSame('shop-1', $config['instance']);
        self::assertSame('write', $config['scope']);
    }

    public function testTokenConfig(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'base_url' => 'https://backend.demo.taler.net',
                'token' => 'Bearer secret123',
            ],
        ]);

        self::assertSame('Bearer secret123', $config['token']);
    }

    public function testBaseUrlIsRequired(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->processor->processConfiguration($this->configuration, [[]]);
    }

    public function testBaseUrlCannotBeEmpty(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->processor->processConfiguration($this->configuration, [
            ['base_url' => ''],
        ]);
    }
}
