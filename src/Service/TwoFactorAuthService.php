<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use MirrorPS\TalerBundle\Taler;
use Taler\Api\TwoFactorAuth\Dto\ChallengeRequestResponse;
use Taler\Api\TwoFactorAuth\Dto\MerchantChallengeSolveRequest;

final class TwoFactorAuthService implements TwoFactorAuthServiceInterface
{
    public function __construct(
        private readonly Taler $taler,
    ) {
    }

    public function requestChallenge(
        string $instanceId,
        string $challengeId,
        ?array $requestBody = null,
        array $headers = []
    ): ChallengeRequestResponse {
        return $this->taler->twoFactorAuth()->requestChallenge($instanceId, $challengeId, $requestBody, $headers);
    }

    public function requestChallengeAsync(
        string $instanceId,
        string $challengeId,
        ?array $requestBody = null,
        array $headers = []
    ): mixed {
        return $this->taler->twoFactorAuth()->requestChallengeAsync($instanceId, $challengeId, $requestBody, $headers);
    }

    public function confirmChallenge(
        string $instanceId,
        string $challengeId,
        MerchantChallengeSolveRequest $requestBody,
        array $headers = []
    ): void {
        $this->taler->twoFactorAuth()->confirmChallenge($instanceId, $challengeId, $requestBody, $headers);
    }

    public function confirmChallengeAsync(
        string $instanceId,
        string $challengeId,
        MerchantChallengeSolveRequest $requestBody,
        array $headers = []
    ): mixed {
        return $this->taler->twoFactorAuth()->confirmChallengeAsync($instanceId, $challengeId, $requestBody, $headers);
    }
}
