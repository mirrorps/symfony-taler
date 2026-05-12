<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use Taler\Api\TokenFamilies\Dto\TokenFamiliesList;
use Taler\Api\TokenFamilies\Dto\TokenFamilyCreateRequest;
use Taler\Api\TokenFamilies\Dto\TokenFamilyDetails;
use Taler\Api\TokenFamilies\Dto\TokenFamilyUpdateRequest;

interface TokenFamiliesServiceInterface
{
    /**
     * @param array<string, string> $headers
     */
    public function createTokenFamily(TokenFamilyCreateRequest $request, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function createTokenFamilyAsync(TokenFamilyCreateRequest $request, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     */
    public function updateTokenFamily(string $slug, TokenFamilyUpdateRequest $request, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function updateTokenFamilyAsync(string $slug, TokenFamilyUpdateRequest $request, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     * @return TokenFamiliesList|array<string, mixed>
     */
    public function getTokenFamilies(array $headers = []): TokenFamiliesList|array;

    /**
     * @param array<string, string> $headers
     */
    public function getTokenFamiliesAsync(array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     * @return TokenFamilyDetails|array<string, mixed>
     */
    public function getTokenFamily(string $slug, array $headers = []): TokenFamilyDetails|array;

    /**
     * @param array<string, string> $headers
     */
    public function getTokenFamilyAsync(string $slug, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     */
    public function deleteTokenFamily(string $slug, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function deleteTokenFamilyAsync(string $slug, array $headers = []): mixed;
}
