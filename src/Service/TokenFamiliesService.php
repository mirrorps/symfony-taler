<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use MirrorPS\TalerBundle\Taler;
use Taler\Api\TokenFamilies\Dto\TokenFamiliesList;
use Taler\Api\TokenFamilies\Dto\TokenFamilyCreateRequest;
use Taler\Api\TokenFamilies\Dto\TokenFamilyDetails;
use Taler\Api\TokenFamilies\Dto\TokenFamilyUpdateRequest;

final class TokenFamiliesService implements TokenFamiliesServiceInterface
{
    public function __construct(private readonly Taler $taler)
    {
    }

    public function createTokenFamily(TokenFamilyCreateRequest $request, array $headers = []): void
    {
        $this->taler->tokenFamilies()->createTokenFamily($request, $headers);
    }

    public function createTokenFamilyAsync(TokenFamilyCreateRequest $request, array $headers = []): mixed
    {
        return $this->taler->tokenFamilies()->createTokenFamilyAsync($request, $headers);
    }

    public function updateTokenFamily(string $slug, TokenFamilyUpdateRequest $request, array $headers = []): void
    {
        $this->taler->tokenFamilies()->updateTokenFamily($slug, $request, $headers);
    }

    public function updateTokenFamilyAsync(string $slug, TokenFamilyUpdateRequest $request, array $headers = []): mixed
    {
        return $this->taler->tokenFamilies()->updateTokenFamilyAsync($slug, $request, $headers);
    }

    public function getTokenFamilies(array $headers = []): TokenFamiliesList|array
    {
        return $this->taler->tokenFamilies()->getTokenFamilies($headers);
    }

    public function getTokenFamiliesAsync(array $headers = []): mixed
    {
        return $this->taler->tokenFamilies()->getTokenFamiliesAsync($headers);
    }

    public function getTokenFamily(string $slug, array $headers = []): TokenFamilyDetails|array
    {
        return $this->taler->tokenFamilies()->getTokenFamily($slug, $headers);
    }

    public function getTokenFamilyAsync(string $slug, array $headers = []): mixed
    {
        return $this->taler->tokenFamilies()->getTokenFamilyAsync($slug, $headers);
    }

    public function deleteTokenFamily(string $slug, array $headers = []): void
    {
        $this->taler->tokenFamilies()->deleteTokenFamily($slug, $headers);
    }

    public function deleteTokenFamilyAsync(string $slug, array $headers = []): mixed
    {
        return $this->taler->tokenFamilies()->deleteTokenFamilyAsync($slug, $headers);
    }
}
