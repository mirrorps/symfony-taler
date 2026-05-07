<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use MirrorPS\TalerBundle\Taler;
use Taler\Api\Instance\Dto\GetAccessTokensRequest;
use Taler\Api\Instance\Dto\GetKycStatusRequest;
use Taler\Api\Instance\Dto\GetMerchantStatisticsAmountRequest;
use Taler\Api\Instance\Dto\GetMerchantStatisticsCounterRequest;
use Taler\Api\Instance\Dto\InstanceAuthConfigExternal;
use Taler\Api\Instance\Dto\InstanceAuthConfigToken;
use Taler\Api\Instance\Dto\InstanceAuthConfigTokenOLD;
use Taler\Api\Instance\Dto\InstanceConfigurationMessage;
use Taler\Api\Instance\Dto\InstanceReconfigurationMessage;
use Taler\Api\Instance\Dto\InstancesResponse;
use Taler\Api\Instance\Dto\LoginTokenRequest;
use Taler\Api\Instance\Dto\LoginTokenSuccessResponse;
use Taler\Api\Instance\Dto\MerchantAccountKycRedirectsResponse;
use Taler\Api\Instance\Dto\MerchantStatisticsAmountResponse;
use Taler\Api\Instance\Dto\MerchantStatisticsCounterResponse;
use Taler\Api\Instance\Dto\QueryInstancesResponse;
use Taler\Api\Instance\Dto\TokenInfos;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;

final class InstanceService implements InstanceServiceInterface
{
    private Taler $taler;

    public function __construct(Taler $taler)
    {
        $this->taler = $taler;
    }

    public function getInstances(array $headers = []): InstancesResponse|array
    {
        return $this->taler->instance()->getInstances($headers);
    }

    public function getInstancesAsync(array $headers = []): mixed
    {
        return $this->taler->instance()->getInstancesAsync($headers);
    }

    public function getInstance(string $instanceId, array $headers = []): QueryInstancesResponse|array
    {
        return $this->taler->instance()->getInstance($instanceId, $headers);
    }

    public function getInstanceAsync(string $instanceId, array $headers = []): mixed
    {
        return $this->taler->instance()->getInstanceAsync($instanceId, $headers);
    }

    public function createInstance(InstanceConfigurationMessage $instanceConfiguration, array $headers = []): void
    {
        $this->taler->instance()->createInstance($instanceConfiguration, $headers);
    }

    public function createInstanceAsync(InstanceConfigurationMessage $instanceConfiguration, array $headers = []): mixed
    {
        return $this->taler->instance()->createInstanceAsync($instanceConfiguration, $headers);
    }

    public function updateInstance(string $instanceId, InstanceReconfigurationMessage $message, array $headers = []): void
    {
        $this->taler->instance()->updateInstance($instanceId, $message, $headers);
    }

    public function updateInstanceAsync(string $instanceId, InstanceReconfigurationMessage $message, array $headers = []): mixed
    {
        return $this->taler->instance()->updateInstanceAsync($instanceId, $message, $headers);
    }

    public function deleteInstance(string $instanceId, bool $purge = false, array $headers = []): ?ChallengeResponse
    {
        return $this->taler->instance()->deleteInstance($instanceId, $purge, $headers);
    }

    public function deleteInstanceAsync(string $instanceId, bool $purge = false, array $headers = []): mixed
    {
        return $this->taler->instance()->deleteInstanceAsync($instanceId, $purge, $headers);
    }

    public function updateAuth(
        string $instanceId,
        InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig,
        array $headers = []
    ): ?ChallengeResponse {
        return $this->taler->instance()->updateAuth($instanceId, $authConfig, $headers);
    }

    public function updateAuthAsync(
        string $instanceId,
        InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig,
        array $headers = []
    ): mixed {
        return $this->taler->instance()->updateAuthAsync($instanceId, $authConfig, $headers);
    }

    public function forgotPassword(
        string $instanceId,
        InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig,
        array $headers = []
    ): ?ChallengeResponse {
        return $this->taler->instance()->forgotPassword($instanceId, $authConfig, $headers);
    }

    public function forgotPasswordAsync(
        string $instanceId,
        InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig,
        array $headers = []
    ): mixed {
        return $this->taler->instance()->forgotPasswordAsync($instanceId, $authConfig, $headers);
    }

    public function getAccessToken(
        string $instanceId,
        LoginTokenRequest $loginTokenRequest,
        array $headers = []
    ): LoginTokenSuccessResponse|ChallengeResponse|array {
        return $this->taler->instance()->getAccessToken($instanceId, $loginTokenRequest, $headers);
    }

    public function getAccessTokenAsync(
        string $instanceId,
        LoginTokenRequest $loginTokenRequest,
        array $headers = []
    ): mixed {
        return $this->taler->instance()->getAccessTokenAsync($instanceId, $loginTokenRequest, $headers);
    }

    public function getAccessTokens(
        string $instanceId,
        ?GetAccessTokensRequest $request = null,
        array $headers = []
    ): TokenInfos|array|null {
        return $this->taler->instance()->getAccessTokens($instanceId, $request, $headers);
    }

    public function getAccessTokensAsync(
        string $instanceId,
        ?GetAccessTokensRequest $request = null,
        array $headers = []
    ): mixed {
        return $this->taler->instance()->getAccessTokensAsync($instanceId, $request, $headers);
    }

    public function deleteAccessToken(string $instanceId, array $headers = []): void
    {
        $this->taler->instance()->deleteAccessToken($instanceId, $headers);
    }

    public function deleteAccessTokenAsync(string $instanceId, array $headers = []): mixed
    {
        return $this->taler->instance()->deleteAccessTokenAsync($instanceId, $headers);
    }

    public function deleteAccessTokenBySerial(string $instanceId, int $serial, array $headers = []): void
    {
        $this->taler->instance()->deleteAccessTokenBySerial($instanceId, $serial, $headers);
    }

    public function deleteAccessTokenBySerialAsync(string $instanceId, int $serial, array $headers = []): mixed
    {
        return $this->taler->instance()->deleteAccessTokenBySerialAsync($instanceId, $serial, $headers);
    }

    public function getKycStatus(
        string $instanceId,
        ?GetKycStatusRequest $request = null,
        array $headers = []
    ): MerchantAccountKycRedirectsResponse|array|null {
        return $this->taler->instance()->getKycStatus($instanceId, $request, $headers);
    }

    public function getKycStatusAsync(
        string $instanceId,
        ?GetKycStatusRequest $request = null,
        array $headers = []
    ): mixed {
        return $this->taler->instance()->getKycStatusAsync($instanceId, $request, $headers);
    }

    public function getMerchantStatisticsAmount(
        string $instanceId,
        string $slug,
        ?GetMerchantStatisticsAmountRequest $request = null,
        array $headers = []
    ): MerchantStatisticsAmountResponse|array {
        return $this->taler->instance()->getMerchantStatisticsAmount($instanceId, $slug, $request, $headers);
    }

    public function getMerchantStatisticsAmountAsync(
        string $instanceId,
        string $slug,
        ?GetMerchantStatisticsAmountRequest $request = null,
        array $headers = []
    ): mixed {
        return $this->taler->instance()->getMerchantStatisticsAmountAsync($instanceId, $slug, $request, $headers);
    }

    public function getMerchantStatisticsCounter(
        string $instanceId,
        string $slug,
        ?GetMerchantStatisticsCounterRequest $request = null,
        array $headers = []
    ): MerchantStatisticsCounterResponse|array {
        return $this->taler->instance()->getMerchantStatisticsCounter($instanceId, $slug, $request, $headers);
    }

    public function getMerchantStatisticsCounterAsync(
        string $instanceId,
        string $slug,
        ?GetMerchantStatisticsCounterRequest $request = null,
        array $headers = []
    ): mixed {
        return $this->taler->instance()->getMerchantStatisticsCounterAsync($instanceId, $slug, $request, $headers);
    }
}
