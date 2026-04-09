<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle;

use Taler\Api\Order\OrderClient;
use Taler\Taler as TalerClient;

final class Taler
{
    private TalerClient $client;

    public function __construct(TalerClient $client)
    {
        $this->client = $client;
    }

    public function orders(): OrderClient
    {
        return $this->client->order();
    }

    public function getClient(): TalerClient
    {
        return $this->client;
    }
}
