<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Service;

use MirrorPS\TalerBundle\Service\OtpDevicesService;
use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\OtpDevices\Dto\GetOtpDeviceRequest;
use Taler\Api\OtpDevices\Dto\OtpDeviceAddDetails;
use Taler\Api\OtpDevices\Dto\OtpDeviceDetails;
use Taler\Api\OtpDevices\Dto\OtpDevicePatchDetails;
use Taler\Api\OtpDevices\Dto\OtpDevicesSummaryResponse;
use Taler\Api\OtpDevices\OtpDevicesClient;
use Taler\Taler as TalerClient;

final class OtpDevicesServiceTest extends TestCase
{
    private OtpDevicesClient $otpDevicesClient;
    private OtpDevicesService $service;

    protected function setUp(): void
    {
        $this->otpDevicesClient = $this->createMock(OtpDevicesClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->method('otpDevices')->willReturn($this->otpDevicesClient);

        $taler = new Taler($client);
        $this->service = new OtpDevicesService($taler);
    }

    public function testCreateOtpDeviceDelegatesToClient(): void
    {
        $details = new OtpDeviceAddDetails(
            otp_device_id: 'pos-1',
            otp_device_description: 'POS terminal',
            otp_key: 'JBSWY3DPEHPK3PXP',
            otp_algorithm: 1,
        );

        $this->otpDevicesClient->expects(self::once())
            ->method('createOtpDevice')
            ->with($details, []);

        $this->service->createOtpDevice($details);
    }

    public function testCreateOtpDevicePassesHeaders(): void
    {
        $details = new OtpDeviceAddDetails(
            otp_device_id: 'pos-2',
            otp_device_description: 'POS',
            otp_key: 'JBSWY3DPEHPK3PXP',
            otp_algorithm: 'TOTP_WITHOUT_PRICE',
        );
        $headers = ['X-Trace-Id' => 'otp-create'];

        $this->otpDevicesClient->expects(self::once())
            ->method('createOtpDevice')
            ->with($details, $headers);

        $this->service->createOtpDevice($details, $headers);
    }

    public function testCreateOtpDeviceAsyncDelegatesToClient(): void
    {
        $details = new OtpDeviceAddDetails(
            otp_device_id: 'pos-async',
            otp_device_description: 'POS',
            otp_key: 'JBSWY3DPEHPK3PXP',
            otp_algorithm: 2,
        );
        $expected = 'async-promise';

        $this->otpDevicesClient->expects(self::once())
            ->method('createOtpDeviceAsync')
            ->with($details, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createOtpDeviceAsync($details));
    }

    public function testUpdateOtpDeviceDelegatesToClient(): void
    {
        $deviceId = 'pos-1';
        $details = new OtpDevicePatchDetails(otp_device_description: 'Updated label');

        $this->otpDevicesClient->expects(self::once())
            ->method('updateOtpDevice')
            ->with($deviceId, $details, []);

        $this->service->updateOtpDevice($deviceId, $details);
    }

    public function testUpdateOtpDevicePassesHeaders(): void
    {
        $deviceId = 'pos-1';
        $details = new OtpDevicePatchDetails(
            otp_device_description: 'Updated',
            otp_key: 'NEWKEYBASE32',
            otp_algorithm: 1,
        );
        $headers = ['X-Custom' => '1'];

        $this->otpDevicesClient->expects(self::once())
            ->method('updateOtpDevice')
            ->with($deviceId, $details, $headers);

        $this->service->updateOtpDevice($deviceId, $details, $headers);
    }

    public function testUpdateOtpDeviceAsyncDelegatesToClient(): void
    {
        $deviceId = 'pos-async';
        $details = new OtpDevicePatchDetails(otp_device_description: 'Async');
        $expected = 'async-promise';

        $this->otpDevicesClient->expects(self::once())
            ->method('updateOtpDeviceAsync')
            ->with($deviceId, $details, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->updateOtpDeviceAsync($deviceId, $details));
    }

    public function testGetOtpDevicesDelegatesToClient(): void
    {
        $expected = new OtpDevicesSummaryResponse([]);

        $this->otpDevicesClient->expects(self::once())
            ->method('getOtpDevices')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOtpDevices());
    }

    public function testGetOtpDevicesPassesHeaders(): void
    {
        $headers = ['Accept' => 'application/json'];
        $expected = new OtpDevicesSummaryResponse([]);

        $this->otpDevicesClient->expects(self::once())
            ->method('getOtpDevices')
            ->with($headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOtpDevices($headers));
    }

    public function testGetOtpDevicesAsyncDelegatesToClient(): void
    {
        $expected = 'async-promise';

        $this->otpDevicesClient->expects(self::once())
            ->method('getOtpDevicesAsync')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOtpDevicesAsync());
    }

    public function testGetOtpDeviceDelegatesToClient(): void
    {
        $deviceId = 'pos-1';
        $expected = new OtpDeviceDetails(
            device_description: 'POS',
            otp_algorithm: 1,
            otp_timestamp: 1700000000,
            otp_ctr: null,
            otp_code: '123456',
        );

        $this->otpDevicesClient->expects(self::once())
            ->method('getOtpDevice')
            ->with($deviceId, null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOtpDevice($deviceId));
    }

    public function testGetOtpDevicePassesRequestAndHeaders(): void
    {
        $deviceId = 'pos-2';
        $request = new GetOtpDeviceRequest(faketime: 1700000001, price: 'EUR:1.23');
        $headers = ['X-Trace' => 'get-device'];
        $expected = new OtpDeviceDetails(
            device_description: 'POS',
            otp_algorithm: 'TOTP_WITH_PRICE',
            otp_timestamp: 1700000001,
        );

        $this->otpDevicesClient->expects(self::once())
            ->method('getOtpDevice')
            ->with($deviceId, $request, $headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOtpDevice($deviceId, $request, $headers));
    }

    public function testGetOtpDeviceAsyncDelegatesToClient(): void
    {
        $deviceId = 'pos-async';
        $expected = 'async-promise';

        $this->otpDevicesClient->expects(self::once())
            ->method('getOtpDeviceAsync')
            ->with($deviceId, null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getOtpDeviceAsync($deviceId));
    }

    public function testDeleteOtpDeviceDelegatesToClient(): void
    {
        $deviceId = 'pos-del';

        $this->otpDevicesClient->expects(self::once())
            ->method('deleteOtpDevice')
            ->with($deviceId, []);

        $this->service->deleteOtpDevice($deviceId);
    }

    public function testDeleteOtpDevicePassesHeaders(): void
    {
        $deviceId = 'pos-del-2';
        $headers = ['Authorization' => 'Bearer token'];

        $this->otpDevicesClient->expects(self::once())
            ->method('deleteOtpDevice')
            ->with($deviceId, $headers);

        $this->service->deleteOtpDevice($deviceId, $headers);
    }

    public function testDeleteOtpDeviceAsyncDelegatesToClient(): void
    {
        $deviceId = 'pos-del-async';
        $expected = 'async-promise';

        $this->otpDevicesClient->expects(self::once())
            ->method('deleteOtpDeviceAsync')
            ->with($deviceId, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->deleteOtpDeviceAsync($deviceId));
    }
}
