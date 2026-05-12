<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use Taler\Api\Templates\Dto\TemplateAddDetails;
use Taler\Api\Templates\Dto\TemplateDetails;
use Taler\Api\Templates\Dto\TemplatePatchDetails;
use Taler\Api\Templates\Dto\TemplatesSummaryResponse;

interface TemplatesServiceInterface
{
    /**
     * @param array<string, string> $headers
     */
    public function createTemplate(TemplateAddDetails $details, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function createTemplateAsync(TemplateAddDetails $details, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     */
    public function updateTemplate(string $templateId, TemplatePatchDetails $details, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function updateTemplateAsync(string $templateId, TemplatePatchDetails $details, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     * @return TemplatesSummaryResponse|array<string, mixed>
     */
    public function getTemplates(array $headers = []): TemplatesSummaryResponse|array;

    /**
     * @param array<string, string> $headers
     */
    public function getTemplatesAsync(array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     * @return TemplateDetails|array<string, mixed>
     */
    public function getTemplate(string $templateId, array $headers = []): TemplateDetails|array;

    /**
     * @param array<string, string> $headers
     */
    public function getTemplateAsync(string $templateId, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     */
    public function deleteTemplate(string $templateId, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function deleteTemplateAsync(string $templateId, array $headers = []): mixed;
}
