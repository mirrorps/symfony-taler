<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Service;

use MirrorPS\TalerBundle\Service\TemplatesService;
use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\Dto\RelativeTime;
use Taler\Api\Templates\Dto\TemplateAddDetails;
use Taler\Api\Templates\Dto\TemplateContractDetails;
use Taler\Api\Templates\Dto\TemplateDetails;
use Taler\Api\Templates\Dto\TemplatePatchDetails;
use Taler\Api\Templates\Dto\TemplatesSummaryResponse;
use Taler\Api\Templates\TemplatesClient;
use Taler\Taler as TalerClient;

final class TemplatesServiceTest extends TestCase
{
    private TemplatesClient $templatesClient;
    private TemplatesService $service;

    protected function setUp(): void
    {
        $this->templatesClient = $this->createMock(TemplatesClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->method('templates')->willReturn($this->templatesClient);

        $taler = new Taler($client);
        $this->service = new TemplatesService($taler);
    }

    public function testCreateTemplateDelegatesToClient(): void
    {
        $details = new TemplateAddDetails(
            template_id: 'tpl-1',
            template_description: 'Coffee',
            template_contract: new TemplateContractDetails(
                minimum_age: 0,
                pay_duration: new RelativeTime(d_us: 3600000000),
                summary: 'Cup',
                currency: 'EUR',
                amount: 'EUR:2.50',
            ),
        );

        $this->templatesClient->expects(self::once())
            ->method('createTemplate')
            ->with($details, []);

        $this->service->createTemplate($details);
    }

    public function testCreateTemplatePassesHeaders(): void
    {
        $details = new TemplateAddDetails(
            template_id: 'tpl-2',
            template_description: 'Tea',
            template_contract: new TemplateContractDetails(
                minimum_age: 18,
                pay_duration: new RelativeTime(d_us: 'forever'),
            ),
            otp_id: 'pos-1',
        );
        $headers = ['X-Request-Id' => 'abc'];

        $this->templatesClient->expects(self::once())
            ->method('createTemplate')
            ->with($details, $headers);

        $this->service->createTemplate($details, $headers);
    }

    public function testCreateTemplateAsyncDelegatesToClient(): void
    {
        $details = new TemplateAddDetails(
            template_id: 'tpl-async',
            template_description: 'Async',
            template_contract: new TemplateContractDetails(
                minimum_age: 0,
                pay_duration: new RelativeTime(d_us: 0),
            ),
        );
        $expected = 'promise';

        $this->templatesClient->expects(self::once())
            ->method('createTemplateAsync')
            ->with($details, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createTemplateAsync($details));
    }

    public function testUpdateTemplateDelegatesToClient(): void
    {
        $id = 'tpl-1';
        $details = new TemplatePatchDetails(
            template_description: 'Updated',
            template_contract: new TemplateContractDetails(
                minimum_age: 0,
                pay_duration: new RelativeTime(d_us: 1000),
            ),
        );

        $this->templatesClient->expects(self::once())
            ->method('updateTemplate')
            ->with($id, $details, []);

        $this->service->updateTemplate($id, $details);
    }

    public function testUpdateTemplateAsyncDelegatesToClient(): void
    {
        $id = 'tpl-u';
        $details = new TemplatePatchDetails(
            template_description: 'U',
            template_contract: new TemplateContractDetails(
                minimum_age: 0,
                pay_duration: new RelativeTime(d_us: 0),
            ),
        );
        $expected = 'promise';

        $this->templatesClient->expects(self::once())
            ->method('updateTemplateAsync')
            ->with($id, $details, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->updateTemplateAsync($id, $details));
    }

    public function testGetTemplatesDelegatesToClient(): void
    {
        $expected = new TemplatesSummaryResponse([]);

        $this->templatesClient->expects(self::once())
            ->method('getTemplates')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getTemplates());
    }

    public function testGetTemplatesPassesHeaders(): void
    {
        $headers = ['Accept' => 'application/json'];
        $expected = new TemplatesSummaryResponse([]);

        $this->templatesClient->expects(self::once())
            ->method('getTemplates')
            ->with($headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getTemplates($headers));
    }

    public function testGetTemplatesAsyncDelegatesToClient(): void
    {
        $expected = 'promise';

        $this->templatesClient->expects(self::once())
            ->method('getTemplatesAsync')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getTemplatesAsync());
    }

    public function testGetTemplateDelegatesToClient(): void
    {
        $id = 'tpl-1';
        $expected = new TemplateDetails(
            template_description: 'D',
            template_contract: new TemplateContractDetails(
                minimum_age: 0,
                pay_duration: new RelativeTime(d_us: 0),
            ),
        );

        $this->templatesClient->expects(self::once())
            ->method('getTemplate')
            ->with($id, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getTemplate($id));
    }

    public function testGetTemplateAsyncDelegatesToClient(): void
    {
        $id = 'tpl-a';
        $expected = 'promise';

        $this->templatesClient->expects(self::once())
            ->method('getTemplateAsync')
            ->with($id, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getTemplateAsync($id));
    }

    public function testDeleteTemplateDelegatesToClient(): void
    {
        $id = 'tpl-del';

        $this->templatesClient->expects(self::once())
            ->method('deleteTemplate')
            ->with($id, []);

        $this->service->deleteTemplate($id);
    }

    public function testDeleteTemplateAsyncDelegatesToClient(): void
    {
        $id = 'tpl-del-a';
        $expected = 'promise';

        $this->templatesClient->expects(self::once())
            ->method('deleteTemplateAsync')
            ->with($id, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->deleteTemplateAsync($id));
    }
}
