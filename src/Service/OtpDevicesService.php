<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use MirrorPS\TalerBundle\Taler;
use Taler\Api\OtpDevices\Dto\GetOtpDeviceRequest;
use Taler\Api\OtpDevices\Dto\OtpDeviceAddDetails;
use Taler\Api\OtpDevices\Dto\OtpDeviceDetails;
use Taler\Api\OtpDevices\Dto\OtpDevicePatchDetails;
use Taler\Api\OtpDevices\Dto\OtpDevicesSummaryResponse;

final class OtpDevicesService implements OtpDevicesServiceInterface
{
    public function __construct(private readonly Taler $taler)
    {
    }

    public function createOtpDevice(OtpDeviceAddDetails $details, array $headers = []): void
    {
        $this->taler->otpDevices()->createOtpDevice($details, $headers);
    }

    public function createOtpDeviceAsync(OtpDeviceAddDetails $details, array $headers = []): mixed
    {
        return $this->taler->otpDevices()->createOtpDeviceAsync($details, $headers);
    }

    public function updateOtpDevice(string $deviceId, OtpDevicePatchDetails $details, array $headers = []): void
    {
        $this->taler->otpDevices()->updateOtpDevice($deviceId, $details, $headers);
    }

    public function updateOtpDeviceAsync(string $deviceId, OtpDevicePatchDetails $details, array $headers = []): mixed
    {
        return $this->taler->otpDevices()->updateOtpDeviceAsync($deviceId, $details, $headers);
    }

    public function getOtpDevices(array $headers = []): OtpDevicesSummaryResponse|array
    {
        return $this->taler->otpDevices()->getOtpDevices($headers);
    }

    public function getOtpDevicesAsync(array $headers = []): mixed
    {
        return $this->taler->otpDevices()->getOtpDevicesAsync($headers);
    }

    public function getOtpDevice(string $deviceId, ?GetOtpDeviceRequest $request = null, array $headers = []): OtpDeviceDetails|array
    {
        return $this->taler->otpDevices()->getOtpDevice($deviceId, $request, $headers);
    }

    public function getOtpDeviceAsync(string $deviceId, ?GetOtpDeviceRequest $request = null, array $headers = []): mixed
    {
        return $this->taler->otpDevices()->getOtpDeviceAsync($deviceId, $request, $headers);
    }

    public function deleteOtpDevice(string $deviceId, array $headers = []): void
    {
        $this->taler->otpDevices()->deleteOtpDevice($deviceId, $headers);
    }

    public function deleteOtpDeviceAsync(string $deviceId, array $headers = []): mixed
    {
        return $this->taler->otpDevices()->deleteOtpDeviceAsync($deviceId, $headers);
    }
}
