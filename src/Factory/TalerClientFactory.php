<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Factory;

use MirrorPS\TalerBundle\Taler;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Taler\Factory\Factory;

final class TalerClientFactory
{
    /** @var array<string, mixed> */
    private array $config;

    public function __construct(
        array $config,
        private readonly ?ClientInterface $httpClient = null,
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
        $factoryConfig = [
            'base_url' => $this->config['base_url'],
            'debugLoggingEnabled' => (bool) ($this->config['debug_logging_enabled'] ?? false),
        ];

        if ($this->httpClient !== null) {
            $factoryConfig['client'] = $this->httpClient;
        }

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
