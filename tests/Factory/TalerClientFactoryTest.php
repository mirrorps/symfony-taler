<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Factory;

use MirrorPS\TalerBundle\Factory\TalerClientFactory;
use PHPUnit\Framework\TestCase;

final class TalerClientFactoryTest extends TestCase
{
    public function testConstructorAcceptsConfig(): void
    {
        $config = [
            'base_url' => 'https://backend.demo.taler.net',
            'username' => 'user',
            'password' => 'pass',
            'instance' => 'default',
            'token' => null,
            'scope' => null,
        ];

        $factory = new TalerClientFactory($config);

        self::assertInstanceOf(TalerClientFactory::class, $factory);
    }
}
