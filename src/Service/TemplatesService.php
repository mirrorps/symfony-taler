<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use MirrorPS\TalerBundle\Taler;
use Taler\Api\Templates\Dto\TemplateAddDetails;
use Taler\Api\Templates\Dto\TemplateDetails;
use Taler\Api\Templates\Dto\TemplatePatchDetails;
use Taler\Api\Templates\Dto\TemplatesSummaryResponse;

final class TemplatesService implements TemplatesServiceInterface
{
    public function __construct(private readonly Taler $taler)
    {
    }

    public function createTemplate(TemplateAddDetails $details, array $headers = []): void
    {
        $this->taler->templates()->createTemplate($details, $headers);
    }

    public function createTemplateAsync(TemplateAddDetails $details, array $headers = []): mixed
    {
        return $this->taler->templates()->createTemplateAsync($details, $headers);
    }

    public function updateTemplate(string $templateId, TemplatePatchDetails $details, array $headers = []): void
    {
        $this->taler->templates()->updateTemplate($templateId, $details, $headers);
    }

    public function updateTemplateAsync(string $templateId, TemplatePatchDetails $details, array $headers = []): mixed
    {
        return $this->taler->templates()->updateTemplateAsync($templateId, $details, $headers);
    }

    public function getTemplates(array $headers = []): TemplatesSummaryResponse|array
    {
        return $this->taler->templates()->getTemplates($headers);
    }

    public function getTemplatesAsync(array $headers = []): mixed
    {
        return $this->taler->templates()->getTemplatesAsync($headers);
    }

    public function getTemplate(string $templateId, array $headers = []): TemplateDetails|array
    {
        return $this->taler->templates()->getTemplate($templateId, $headers);
    }

    public function getTemplateAsync(string $templateId, array $headers = []): mixed
    {
        return $this->taler->templates()->getTemplateAsync($templateId, $headers);
    }

    public function deleteTemplate(string $templateId, array $headers = []): void
    {
        $this->taler->templates()->deleteTemplate($templateId, $headers);
    }

    public function deleteTemplateAsync(string $templateId, array $headers = []): mixed
    {
        return $this->taler->templates()->deleteTemplateAsync($templateId, $headers);
    }
}
