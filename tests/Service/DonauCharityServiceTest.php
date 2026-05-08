<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Service;

use MirrorPS\TalerBundle\Service\DonauCharityService;
use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\DonauCharity\DonauCharityClient;
use Taler\Api\DonauCharity\Dto\DonauInstance;
use Taler\Api\DonauCharity\Dto\DonauInstancesResponse;
use Taler\Api\DonauCharity\Dto\PostDonauRequest;
use Taler\Api\TwoFactorAuth\Dto\Challenge;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;
use Taler\Taler as TalerClient;

final class DonauCharityServiceTest extends TestCase
{
    private DonauCharityClient $donauCharityClient;
    private DonauCharityService $service;

    protected function setUp(): void
    {
        $this->donauCharityClient = $this->createMock(DonauCharityClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->method('donauCharity')->willReturn($this->donauCharityClient);

        $taler = new Taler($client);
        $this->service = new DonauCharityService($taler);
    }

    public function testGetInstancesDelegatesToClient(): void
    {
        $expected = new DonauInstancesResponse([
            new DonauInstance(
                donau_instance_serial: 5,
                donau_url: 'https://donau.example.test',
                charity_name: 'Open Source Charity',
                charity_pub_key: 'pub',
                charity_id: 12,
                charity_max_per_year: 'EUR:1000',
                charity_receipts_to_date: 'EUR:100',
                current_year: 2026,
            ),
        ]);

        $this->donauCharityClient->expects(self::once())
            ->method('getInstances')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getInstances());
    }

    public function testGetInstancesPassesHeaders(): void
    {
        $headers = ['X-Request-Id' => 'req-1'];
        $expected = ['donau_instances' => []];

        $this->donauCharityClient->expects(self::once())
            ->method('getInstances')
            ->with($headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getInstances($headers));
    }

    public function testGetInstancesAsyncDelegatesToClient(): void
    {
        $expected = 'async-promise';

        $this->donauCharityClient->expects(self::once())
            ->method('getInstancesAsync')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getInstancesAsync());
    }

    public function testCreateDonauCharityDelegatesToClient(): void
    {
        $request = new PostDonauRequest(
            donau_url: 'https://donau.example.test',
            charity_id: 42,
        );
        $expected = new ChallengeResponse(
            challenges: [new Challenge(challenge_id: 'challenge-1', tan_channel: 'email', tan_info: 'a***@example.test')],
            combi_and: false,
        );

        $this->donauCharityClient->expects(self::once())
            ->method('createDonauCharity')
            ->with($request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createDonauCharity($request));
    }

    public function testCreateDonauCharityPassesHeaders(): void
    {
        $request = new PostDonauRequest(
            donau_url: 'https://donau.example.test',
            charity_id: 7,
        );
        $headers = ['Authorization' => 'Bearer token'];

        $this->donauCharityClient->expects(self::once())
            ->method('createDonauCharity')
            ->with($request, $headers)
            ->willReturn(null);

        self::assertNull($this->service->createDonauCharity($request, $headers));
    }

    public function testCreateDonauCharityAsyncDelegatesToClient(): void
    {
        $request = new PostDonauRequest(
            donau_url: 'https://donau.example.test',
            charity_id: 11,
        );
        $expected = 'async-promise';

        $this->donauCharityClient->expects(self::once())
            ->method('createDonauCharityAsync')
            ->with($request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createDonauCharityAsync($request));
    }

    public function testDeleteDonauCharityBySerialDelegatesToClient(): void
    {
        $donauSerial = 13;

        $this->donauCharityClient->expects(self::once())
            ->method('deleteDonauCharityBySerial')
            ->with($donauSerial, []);

        $this->service->deleteDonauCharityBySerial($donauSerial);
    }

    public function testDeleteDonauCharityBySerialPassesHeaders(): void
    {
        $donauSerial = 8;
        $headers = ['X-Custom' => 'value'];

        $this->donauCharityClient->expects(self::once())
            ->method('deleteDonauCharityBySerial')
            ->with($donauSerial, $headers);

        $this->service->deleteDonauCharityBySerial($donauSerial, $headers);
    }

    public function testDeleteDonauCharityBySerialAsyncDelegatesToClient(): void
    {
        $donauSerial = 21;
        $expected = 'async-promise';

        $this->donauCharityClient->expects(self::once())
            ->method('deleteDonauCharityBySerialAsync')
            ->with($donauSerial, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->deleteDonauCharityBySerialAsync($donauSerial));
    }
}
