<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Service;

use MirrorPS\TalerBundle\Service\TwoFactorAuthService;
use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\Dto\Timestamp;
use Taler\Api\TwoFactorAuth\Dto\ChallengeRequestResponse;
use Taler\Api\TwoFactorAuth\Dto\MerchantChallengeSolveRequest;
use Taler\Api\TwoFactorAuth\TwoFactorAuthClient;
use Taler\Taler as TalerClient;

final class TwoFactorAuthServiceTest extends TestCase
{
    private TwoFactorAuthClient $twoFactorAuthClient;
    private TwoFactorAuthService $service;

    protected function setUp(): void
    {
        $this->twoFactorAuthClient = $this->createMock(TwoFactorAuthClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->method('twoFactorAuth')->willReturn($this->twoFactorAuthClient);

        $taler = new Taler($client);
        $this->service = new TwoFactorAuthService($taler);
    }

    public function testRequestChallengeDelegatesToClient(): void
    {
        $instanceId = 'sandbox';
        $challengeId = 'challenge-1';
        $body = ['foo' => 'bar'];
        $headers = ['X-Test' => '1'];
        $expected = new ChallengeRequestResponse(
            solve_expiration: new Timestamp(t_s: 100),
            earliest_retransmission: new Timestamp(t_s: 200),
        );

        $this->twoFactorAuthClient->expects(self::once())
            ->method('requestChallenge')
            ->with($instanceId, $challengeId, $body, $headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->requestChallenge($instanceId, $challengeId, $body, $headers));
    }

    public function testRequestChallengeAsyncDelegatesToClient(): void
    {
        $instanceId = 'sandbox';
        $challengeId = 'c2';
        $expected = 'promise';

        $this->twoFactorAuthClient->expects(self::once())
            ->method('requestChallengeAsync')
            ->with($instanceId, $challengeId, null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->requestChallengeAsync($instanceId, $challengeId));
    }

    public function testConfirmChallengeDelegatesToClient(): void
    {
        $instanceId = 'sandbox';
        $challengeId = 'c3';
        $solve = new MerchantChallengeSolveRequest(tan: '123456', validate: false);
        $headers = ['Accept' => 'application/json'];

        $this->twoFactorAuthClient->expects(self::once())
            ->method('confirmChallenge')
            ->with($instanceId, $challengeId, $solve, $headers);

        $this->service->confirmChallenge($instanceId, $challengeId, $solve, $headers);
    }

    public function testConfirmChallengeAsyncDelegatesToClient(): void
    {
        $instanceId = 'sandbox';
        $challengeId = 'c4';
        $solve = new MerchantChallengeSolveRequest(tan: '654321', validate: false);
        $expected = 'async-confirm';

        $this->twoFactorAuthClient->expects(self::once())
            ->method('confirmChallengeAsync')
            ->with($instanceId, $challengeId, $solve, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->confirmChallengeAsync($instanceId, $challengeId, $solve));
    }
}
