<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use MirrorPS\TalerBundle\Taler;
use Taler\Api\Webhooks\Dto\WebhookAddDetails;
use Taler\Api\Webhooks\Dto\WebhookDetails;
use Taler\Api\Webhooks\Dto\WebhookPatchDetails;
use Taler\Api\Webhooks\Dto\WebhookSummaryResponse;

final class WebhooksService implements WebhooksServiceInterface
{
    public function __construct(private readonly Taler $taler)
    {
    }

    public function createWebhook(WebhookAddDetails $details, array $headers = []): void
    {
        $this->taler->webhooks()->createWebhook($details, $headers);
    }

    public function createWebhookAsync(WebhookAddDetails $details, array $headers = []): mixed
    {
        return $this->taler->webhooks()->createWebhookAsync($details, $headers);
    }

    public function updateWebhook(string $webhookId, WebhookPatchDetails $details, array $headers = []): void
    {
        $this->taler->webhooks()->updateWebhook($webhookId, $details, $headers);
    }

    public function updateWebhookAsync(string $webhookId, WebhookPatchDetails $details, array $headers = []): mixed
    {
        return $this->taler->webhooks()->updateWebhookAsync($webhookId, $details, $headers);
    }

    public function getWebhooks(array $headers = []): WebhookSummaryResponse|array
    {
        return $this->taler->webhooks()->getWebhooks($headers);
    }

    public function getWebhooksAsync(array $headers = []): mixed
    {
        return $this->taler->webhooks()->getWebhooksAsync($headers);
    }

    public function getWebhook(string $webhookId, array $headers = []): WebhookDetails|array
    {
        return $this->taler->webhooks()->getWebhook($webhookId, $headers);
    }

    public function getWebhookAsync(string $webhookId, array $headers = []): mixed
    {
        return $this->taler->webhooks()->getWebhookAsync($webhookId, $headers);
    }

    public function deleteWebhook(string $webhookId, array $headers = []): void
    {
        $this->taler->webhooks()->deleteWebhook($webhookId, $headers);
    }

    public function deleteWebhookAsync(string $webhookId, array $headers = []): mixed
    {
        return $this->taler->webhooks()->deleteWebhookAsync($webhookId, $headers);
    }
}
