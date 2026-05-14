<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use Taler\Api\TwoFactorAuth\Dto\ChallengeRequestResponse;
use Taler\Api\TwoFactorAuth\Dto\MerchantChallengeSolveRequest;

interface TwoFactorAuthServiceInterface
{
    /**
     * Request TAN transmission for a challenge (POST …/challenge/{challengeId}).
     *
     * @param array<string, mixed>|null $requestBody Optional JSON object body (empty object if null)
     * @param array<string, string>     $headers     Optional HTTP headers
     */
    public function requestChallenge(
        string $instanceId,
        string $challengeId,
        ?array $requestBody = null,
        array $headers = []
    ): ChallengeRequestResponse;

    /**
     * @param array<string, mixed>|null $requestBody
     * @param array<string, string>     $headers
     */
    public function requestChallengeAsync(
        string $instanceId,
        string $challengeId,
        ?array $requestBody = null,
        array $headers = []
    ): mixed;

    /**
     * Confirm a challenge with the given TAN (POST …/challenge/{challengeId}/confirm).
     *
     * @param array<string, string> $headers Optional HTTP headers
     */
    public function confirmChallenge(
        string $instanceId,
        string $challengeId,
        MerchantChallengeSolveRequest $requestBody,
        array $headers = []
    ): void;

    /**
     * @param array<string, string> $headers
     */
    public function confirmChallengeAsync(
        string $instanceId,
        string $challengeId,
        MerchantChallengeSolveRequest $requestBody,
        array $headers = []
    ): mixed;
}
