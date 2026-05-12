<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Service;

use MirrorPS\TalerBundle\Service\TokenFamiliesService;
use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\Dto\RelativeTime;
use Taler\Api\Dto\Timestamp;
use Taler\Api\TokenFamilies\Dto\TokenFamiliesList;
use Taler\Api\TokenFamilies\Dto\TokenFamilyCreateRequest;
use Taler\Api\TokenFamilies\Dto\TokenFamilyDetails;
use Taler\Api\TokenFamilies\Dto\TokenFamilyUpdateRequest;
use Taler\Api\TokenFamilies\TokenFamiliesClient;
use Taler\Taler as TalerClient;

final class TokenFamiliesServiceTest extends TestCase
{
    private TokenFamiliesClient $tokenFamiliesClient;
    private TokenFamiliesService $service;

    protected function setUp(): void
    {
        $this->tokenFamiliesClient = $this->createMock(TokenFamiliesClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->method('tokenFamilies')->willReturn($this->tokenFamiliesClient);

        $taler = new Taler($client);
        $this->service = new TokenFamiliesService($taler);
    }

    public function testCreateTokenFamilyDelegatesToClient(): void
    {
        $request = new TokenFamilyCreateRequest(
            slug: 'loyalty-1',
            name: 'Loyalty',
            description: 'Points',
            valid_before: new Timestamp(t_s: 'never'),
            duration: new RelativeTime(d_us: 3600000000),
            validity_granularity: new RelativeTime(d_us: 0),
            start_offset: new RelativeTime(d_us: 0),
            kind: 'discount',
        );

        $this->tokenFamiliesClient->expects(self::once())
            ->method('createTokenFamily')
            ->with($request, []);

        $this->service->createTokenFamily($request);
    }

    public function testCreateTokenFamilyPassesHeaders(): void
    {
        $request = new TokenFamilyCreateRequest(
            slug: 'sub-1',
            name: 'Sub',
            description: 'Monthly',
            valid_before: new Timestamp(t_s: 2000000000),
            duration: new RelativeTime(d_us: 'forever'),
            validity_granularity: new RelativeTime(d_us: 1000),
            start_offset: new RelativeTime(d_us: 0),
            kind: 'subscription',
        );
        $headers = ['X-Request-Id' => 'req-1'];

        $this->tokenFamiliesClient->expects(self::once())
            ->method('createTokenFamily')
            ->with($request, $headers);

        $this->service->createTokenFamily($request, $headers);
    }

    public function testCreateTokenFamilyAsyncDelegatesToClient(): void
    {
        $request = new TokenFamilyCreateRequest(
            slug: 'async-1',
            name: 'A',
            description: 'B',
            valid_before: new Timestamp(t_s: 1),
            duration: new RelativeTime(d_us: 1),
            validity_granularity: new RelativeTime(d_us: 1),
            start_offset: new RelativeTime(d_us: 0),
            kind: 'discount',
        );
        $expected = 'promise';

        $this->tokenFamiliesClient->expects(self::once())
            ->method('createTokenFamilyAsync')
            ->with($request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createTokenFamilyAsync($request));
    }

    public function testUpdateTokenFamilyDelegatesToClient(): void
    {
        $slug = 'loyalty-1';
        $request = new TokenFamilyUpdateRequest(
            name: 'Loyalty+',
            description: 'More',
            valid_after: new Timestamp(t_s: 0),
            valid_before: new Timestamp(t_s: 'never'),
        );

        $this->tokenFamiliesClient->expects(self::once())
            ->method('updateTokenFamily')
            ->with($slug, $request, []);

        $this->service->updateTokenFamily($slug, $request);
    }

    public function testUpdateTokenFamilyAsyncDelegatesToClient(): void
    {
        $slug = 'u-1';
        $request = new TokenFamilyUpdateRequest(
            name: 'N',
            description: 'D',
            valid_after: new Timestamp(t_s: 1),
            valid_before: new Timestamp(t_s: 2),
        );
        $expected = 'promise';

        $this->tokenFamiliesClient->expects(self::once())
            ->method('updateTokenFamilyAsync')
            ->with($slug, $request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->updateTokenFamilyAsync($slug, $request));
    }

    public function testGetTokenFamiliesDelegatesToClient(): void
    {
        $expected = new TokenFamiliesList([]);

        $this->tokenFamiliesClient->expects(self::once())
            ->method('getTokenFamilies')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getTokenFamilies());
    }

    public function testGetTokenFamiliesAsyncDelegatesToClient(): void
    {
        $expected = 'promise';

        $this->tokenFamiliesClient->expects(self::once())
            ->method('getTokenFamiliesAsync')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getTokenFamiliesAsync());
    }

    public function testGetTokenFamilyDelegatesToClient(): void
    {
        $slug = 'x';
        $expected = new TokenFamilyDetails(
            slug: $slug,
            name: 'N',
            description: 'D',
            valid_after: new Timestamp(t_s: 0),
            valid_before: new Timestamp(t_s: 1),
            duration: new RelativeTime(d_us: 0),
            validity_granularity: new RelativeTime(d_us: 0),
            start_offset: new RelativeTime(d_us: 0),
            kind: 'discount',
            issued: 0,
            used: 0,
        );

        $this->tokenFamiliesClient->expects(self::once())
            ->method('getTokenFamily')
            ->with($slug, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getTokenFamily($slug));
    }

    public function testGetTokenFamilyAsyncDelegatesToClient(): void
    {
        $slug = 'y';
        $expected = 'promise';

        $this->tokenFamiliesClient->expects(self::once())
            ->method('getTokenFamilyAsync')
            ->with($slug, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getTokenFamilyAsync($slug));
    }

    public function testDeleteTokenFamilyDelegatesToClient(): void
    {
        $slug = 'z';

        $this->tokenFamiliesClient->expects(self::once())
            ->method('deleteTokenFamily')
            ->with($slug, []);

        $this->service->deleteTokenFamily($slug);
    }

    public function testDeleteTokenFamilyAsyncDelegatesToClient(): void
    {
        $slug = 'z2';
        $expected = 'promise';

        $this->tokenFamiliesClient->expects(self::once())
            ->method('deleteTokenFamilyAsync')
            ->with($slug, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->deleteTokenFamilyAsync($slug));
    }
}
