<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Factory;

use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
use MirrorPS\TalerBundle\Taler;
use Psr\Log\LoggerInterface;
use Taler\Factory\Factory;

final class TalerClientFactory
{
    /** @var array<string, mixed> */
    private array $config;

    public function __construct(
        array $config,
        private readonly ?LoggerInterface $logger = null,
    ) {
        $this->config = $config;
    }

    public function create(): Taler
    {
        return new Taler(Factory::create($this->buildFactoryOptions()));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildFactoryOptions(): array
    {
        $httpClient = GuzzleAdapter::createWithConfig([
            'timeout' => 10.0,
            'connect_timeout' => 5.0,
            'allow_redirects' => [
                'max' => 3,
                'protocols' => ['https'],
                'referer' => false,
            ],
        ]);

        $factoryConfig = [
            'base_url' => $this->config['base_url'],
            'client' => $httpClient,
            'debugLoggingEnabled' => (bool) ($this->config['debug_logging_enabled'] ?? false),
        ];

        if ($this->logger !== null) {
            $factoryConfig['logger'] = $this->logger;
        }

        if ($this->config['token'] !== null) {
            $factoryConfig['token'] = $this->config['token'];
        } else {
            if ($this->config['username'] !== null) {
                $factoryConfig['username'] = $this->config['username'];
            }
            if ($this->config['password'] !== null) {
                $factoryConfig['password'] = $this->config['password'];
            }
            if ($this->config['instance'] !== null) {
                $factoryConfig['instance'] = $this->config['instance'];
            }
            if ($this->config['scope'] !== null) {
                $factoryConfig['scope'] = $this->config['scope'];
            }
        }

        return $factoryConfig;
    }
}
