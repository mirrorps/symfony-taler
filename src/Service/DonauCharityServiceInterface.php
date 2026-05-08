<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use Taler\Api\DonauCharity\Dto\DonauInstancesResponse;
use Taler\Api\DonauCharity\Dto\PostDonauRequest;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;

interface DonauCharityServiceInterface
{
    /**
     * @param array<string, string> $headers
     * @return DonauInstancesResponse|array<string, mixed>
     */
    public function getInstances(array $headers = []): DonauInstancesResponse|array;

    /**
     * @param array<string, string> $headers
     */
    public function getInstancesAsync(array $headers = []): mixed;

    /**
     * @param PostDonauRequest $request
     * @param array<string, string> $headers
     */
    public function createDonauCharity(PostDonauRequest $request, array $headers = []): ?ChallengeResponse;

    /**
     * @param PostDonauRequest $request
     * @param array<string, string> $headers
     */
    public function createDonauCharityAsync(PostDonauRequest $request, array $headers = []): mixed;

    /**
     * @param int $donauSerial
     * @param array<string, string> $headers
     */
    public function deleteDonauCharityBySerial(int $donauSerial, array $headers = []): void;

    /**
     * @param int $donauSerial
     * @param array<string, string> $headers
     */
    public function deleteDonauCharityBySerialAsync(int $donauSerial, array $headers = []): mixed;
}
