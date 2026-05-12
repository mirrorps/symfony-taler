<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use Taler\Api\OtpDevices\Dto\GetOtpDeviceRequest;
use Taler\Api\OtpDevices\Dto\OtpDeviceAddDetails;
use Taler\Api\OtpDevices\Dto\OtpDeviceDetails;
use Taler\Api\OtpDevices\Dto\OtpDevicePatchDetails;
use Taler\Api\OtpDevices\Dto\OtpDevicesSummaryResponse;

interface OtpDevicesServiceInterface
{
    /**
     * @param array<string, string> $headers
     */
    public function createOtpDevice(OtpDeviceAddDetails $details, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function createOtpDeviceAsync(OtpDeviceAddDetails $details, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     */
    public function updateOtpDevice(string $deviceId, OtpDevicePatchDetails $details, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function updateOtpDeviceAsync(string $deviceId, OtpDevicePatchDetails $details, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     * @return OtpDevicesSummaryResponse|array<string, mixed>
     */
    public function getOtpDevices(array $headers = []): OtpDevicesSummaryResponse|array;

    /**
     * @param array<string, string> $headers
     */
    public function getOtpDevicesAsync(array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     * @return OtpDeviceDetails|array<string, mixed>
     */
    public function getOtpDevice(string $deviceId, ?GetOtpDeviceRequest $request = null, array $headers = []): OtpDeviceDetails|array;

    /**
     * @param array<string, string> $headers
     */
    public function getOtpDeviceAsync(string $deviceId, ?GetOtpDeviceRequest $request = null, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     */
    public function deleteOtpDevice(string $deviceId, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function deleteOtpDeviceAsync(string $deviceId, array $headers = []): mixed;
}
