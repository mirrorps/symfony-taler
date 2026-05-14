<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Service;

use MirrorPS\TalerBundle\Service\WebhooksService;
use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\Dto\Url;
use Taler\Api\Webhooks\Dto\WebhookAddDetails;
use Taler\Api\Webhooks\Dto\WebhookDetails;
use Taler\Api\Webhooks\Dto\WebhookPatchDetails;
use Taler\Api\Webhooks\Dto\WebhookSummaryResponse;
use Taler\Api\Webhooks\WebhooksClient;
use Taler\Taler as TalerClient;

final class WebhooksServiceTest extends TestCase
{
    private WebhooksClient $webhooksClient;
    private WebhooksService $service;

    protected function setUp(): void
    {
        $this->webhooksClient = $this->createMock(WebhooksClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->method('webhooks')->willReturn($this->webhooksClient);

        $taler = new Taler($client);
        $this->service = new WebhooksService($taler);
    }

    public function testCreateWebhookDelegatesToClient(): void
    {
        $details = new WebhookAddDetails(
            webhook_id: 'wh-1',
            event_type: 'order.paid',
            url: Url::fromString('https://example.com/hook'),
            http_method: 'POST',
        );

        $this->webhooksClient->expects(self::once())
            ->method('createWebhook')
            ->with($details, []);

        $this->service->createWebhook($details);
    }

    public function testCreateWebhookPassesHeaders(): void
    {
        $details = new WebhookAddDetails(
            webhook_id: 'wh-2',
            event_type: 'order.refunded',
            url: Url::fromString('https://example.com/r'),
            http_method: 'PUT',
            header_template: 'X-Test: {$order_id}',
        );
        $headers = ['X-Request-Id' => 'abc'];

        $this->webhooksClient->expects(self::once())
            ->method('createWebhook')
            ->with($details, $headers);

        $this->service->createWebhook($details, $headers);
    }

    public function testCreateWebhookAsyncDelegatesToClient(): void
    {
        $details = new WebhookAddDetails(
            webhook_id: 'wh-async',
            event_type: 'order.paid',
            url: Url::fromString('https://example.com/a'),
            http_method: 'GET',
        );
        $expected = 'promise';

        $this->webhooksClient->expects(self::once())
            ->method('createWebhookAsync')
            ->with($details, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createWebhookAsync($details));
    }

    public function testUpdateWebhookDelegatesToClient(): void
    {
        $id = 'wh-1';
        $details = new WebhookPatchDetails(
            event_type: 'order.paid',
            url: Url::fromString('https://example.com/new'),
            http_method: 'POST',
        );

        $this->webhooksClient->expects(self::once())
            ->method('updateWebhook')
            ->with($id, $details, []);

        $this->service->updateWebhook($id, $details);
    }

    public function testUpdateWebhookAsyncDelegatesToClient(): void
    {
        $id = 'wh-u';
        $details = new WebhookPatchDetails(
            event_type: 'e',
            url: Url::fromString('https://example.com/u'),
            http_method: 'DELETE',
        );
        $expected = 'promise';

        $this->webhooksClient->expects(self::once())
            ->method('updateWebhookAsync')
            ->with($id, $details, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->updateWebhookAsync($id, $details));
    }

    public function testGetWebhooksDelegatesToClient(): void
    {
        $expected = new WebhookSummaryResponse([]);

        $this->webhooksClient->expects(self::once())
            ->method('getWebhooks')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getWebhooks());
    }

    public function testGetWebhooksPassesHeaders(): void
    {
        $headers = ['Accept' => 'application/json'];
        $expected = new WebhookSummaryResponse([]);

        $this->webhooksClient->expects(self::once())
            ->method('getWebhooks')
            ->with($headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getWebhooks($headers));
    }

    public function testGetWebhooksAsyncDelegatesToClient(): void
    {
        $expected = 'promise';

        $this->webhooksClient->expects(self::once())
            ->method('getWebhooksAsync')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getWebhooksAsync());
    }

    public function testGetWebhookDelegatesToClient(): void
    {
        $id = 'wh-1';
        $expected = new WebhookDetails(
            event_type: 'order.paid',
            url: 'https://example.com/h',
            http_method: 'POST',
        );

        $this->webhooksClient->expects(self::once())
            ->method('getWebhook')
            ->with($id, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getWebhook($id));
    }

    public function testGetWebhookAsyncDelegatesToClient(): void
    {
        $id = 'wh-a';
        $expected = 'promise';

        $this->webhooksClient->expects(self::once())
            ->method('getWebhookAsync')
            ->with($id, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getWebhookAsync($id));
    }

    public function testDeleteWebhookDelegatesToClient(): void
    {
        $id = 'wh-del';

        $this->webhooksClient->expects(self::once())
            ->method('deleteWebhook')
            ->with($id, []);

        $this->service->deleteWebhook($id);
    }

    public function testDeleteWebhookAsyncDelegatesToClient(): void
    {
        $id = 'wh-del-a';
        $expected = 'promise';

        $this->webhooksClient->expects(self::once())
            ->method('deleteWebhookAsync')
            ->with($id, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->deleteWebhookAsync($id));
    }
}
