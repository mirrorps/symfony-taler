<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Factory;

use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
use MirrorPS\TalerBundle\Taler;
use Taler\Factory\Factory;

final class TalerClientFactory
{
    /** @var array<string, mixed> */
    private array $config;

    /** @param array<string, mixed> $config */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function create(): Taler
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
            'httpClient' => $httpClient,
        ];

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

        $client = Factory::create($factoryConfig);

        return new Taler($client);
    }
}
