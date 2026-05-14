<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use Taler\Api\Webhooks\Dto\WebhookAddDetails;
use Taler\Api\Webhooks\Dto\WebhookDetails;
use Taler\Api\Webhooks\Dto\WebhookPatchDetails;
use Taler\Api\Webhooks\Dto\WebhookSummaryResponse;

interface WebhooksServiceInterface
{
    /**
     * @param array<string, string> $headers
     */
    public function createWebhook(WebhookAddDetails $details, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function createWebhookAsync(WebhookAddDetails $details, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     */
    public function updateWebhook(string $webhookId, WebhookPatchDetails $details, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function updateWebhookAsync(string $webhookId, WebhookPatchDetails $details, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     * @return WebhookSummaryResponse|array<string, mixed>
     */
    public function getWebhooks(array $headers = []): WebhookSummaryResponse|array;

    /**
     * @param array<string, string> $headers
     */
    public function getWebhooksAsync(array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     * @return WebhookDetails|array<string, mixed>
     */
    public function getWebhook(string $webhookId, array $headers = []): WebhookDetails|array;

    /**
     * @param array<string, string> $headers
     */
    public function getWebhookAsync(string $webhookId, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     */
    public function deleteWebhook(string $webhookId, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function deleteWebhookAsync(string $webhookId, array $headers = []): mixed;
}
