<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use MirrorPS\TalerBundle\Taler;
use Taler\Api\DonauCharity\Dto\DonauInstancesResponse;
use Taler\Api\DonauCharity\Dto\PostDonauRequest;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;

final class DonauCharityService implements DonauCharityServiceInterface
{
    private Taler $taler;

    public function __construct(Taler $taler)
    {
        $this->taler = $taler;
    }

    public function getInstances(array $headers = []): DonauInstancesResponse|array
    {
        return $this->taler->donauCharity()->getInstances($headers);
    }

    public function getInstancesAsync(array $headers = []): mixed
    {
        return $this->taler->donauCharity()->getInstancesAsync($headers);
    }

    public function createDonauCharity(PostDonauRequest $request, array $headers = []): ?ChallengeResponse
    {
        return $this->taler->donauCharity()->createDonauCharity($request, $headers);
    }

    public function createDonauCharityAsync(PostDonauRequest $request, array $headers = []): mixed
    {
        return $this->taler->donauCharity()->createDonauCharityAsync($request, $headers);
    }

    public function deleteDonauCharityBySerial(int $donauSerial, array $headers = []): void
    {
        $this->taler->donauCharity()->deleteDonauCharityBySerial($donauSerial, $headers);
    }

    public function deleteDonauCharityBySerialAsync(int $donauSerial, array $headers = []): mixed
    {
        return $this->taler->donauCharity()->deleteDonauCharityBySerialAsync($donauSerial, $headers);
    }
}
