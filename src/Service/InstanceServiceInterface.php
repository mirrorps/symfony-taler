<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

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

interface InstanceServiceInterface
{
    /**
     * @param array<string, string> $headers
     * @return InstancesResponse|array<string, mixed>
     */
    public function getInstances(array $headers = []): InstancesResponse|array;

    /**
     * @param array<string, string> $headers
     */
    public function getInstancesAsync(array $headers = []): mixed;

    /**
     * @param string $instanceId
     * @param array<string, string> $headers
     * @return QueryInstancesResponse|array<string, mixed>
     */
    public function getInstance(string $instanceId, array $headers = []): QueryInstancesResponse|array;

    /**
     * @param string $instanceId
     * @param array<string, string> $headers
     */
    public function getInstanceAsync(string $instanceId, array $headers = []): mixed;

    /**
     * @param InstanceConfigurationMessage $instanceConfiguration
     * @param array<string, string> $headers
     */
    public function createInstance(InstanceConfigurationMessage $instanceConfiguration, array $headers = []): void;

    /**
     * @param InstanceConfigurationMessage $instanceConfiguration
     * @param array<string, string> $headers
     */
    public function createInstanceAsync(InstanceConfigurationMessage $instanceConfiguration, array $headers = []): mixed;

    /**
     * @param string $instanceId
     * @param InstanceReconfigurationMessage $message
     * @param array<string, string> $headers
     */
    public function updateInstance(string $instanceId, InstanceReconfigurationMessage $message, array $headers = []): void;

    /**
     * @param string $instanceId
     * @param InstanceReconfigurationMessage $message
     * @param array<string, string> $headers
     */
    public function updateInstanceAsync(string $instanceId, InstanceReconfigurationMessage $message, array $headers = []): mixed;

    /**
     * @param string $instanceId
     * @param bool $purge
     * @param array<string, string> $headers
     */
    public function deleteInstance(string $instanceId, bool $purge = false, array $headers = []): ?ChallengeResponse;

    /**
     * @param string $instanceId
     * @param bool $purge
     * @param array<string, string> $headers
     */
    public function deleteInstanceAsync(string $instanceId, bool $purge = false, array $headers = []): mixed;

    /**
     * @param string $instanceId
     * @param InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig
     * @param array<string, string> $headers
     */
    public function updateAuth(
        string $instanceId,
        InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig,
        array $headers = []
    ): ?ChallengeResponse;

    /**
     * @param string $instanceId
     * @param InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig
     * @param array<string, string> $headers
     */
    public function updateAuthAsync(
        string $instanceId,
        InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig,
        array $headers = []
    ): mixed;

    /**
     * @param string $instanceId
     * @param InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig
     * @param array<string, string> $headers
     */
    public function forgotPassword(
        string $instanceId,
        InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig,
        array $headers = []
    ): ?ChallengeResponse;

    /**
     * @param string $instanceId
     * @param InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig
     * @param array<string, string> $headers
     */
    public function forgotPasswordAsync(
        string $instanceId,
        InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig,
        array $headers = []
    ): mixed;

    /**
     * @param string $instanceId
     * @param LoginTokenRequest $loginTokenRequest
     * @param array<string, string> $headers
     * @return LoginTokenSuccessResponse|ChallengeResponse|array<string, mixed>
     */
    public function getAccessToken(
        string $instanceId,
        LoginTokenRequest $loginTokenRequest,
        array $headers = []
    ): LoginTokenSuccessResponse|ChallengeResponse|array;

    /**
     * @param string $instanceId
     * @param LoginTokenRequest $loginTokenRequest
     * @param array<string, string> $headers
     */
    public function getAccessTokenAsync(
        string $instanceId,
        LoginTokenRequest $loginTokenRequest,
        array $headers = []
    ): mixed;

    /**
     * @param string $instanceId
     * @param GetAccessTokensRequest|null $request
     * @param array<string, string> $headers
     * @return TokenInfos|array<string, mixed>|null
     */
    public function getAccessTokens(
        string $instanceId,
        ?GetAccessTokensRequest $request = null,
        array $headers = []
    ): TokenInfos|array|null;

    /**
     * @param string $instanceId
     * @param GetAccessTokensRequest|null $request
     * @param array<string, string> $headers
     */
    public function getAccessTokensAsync(
        string $instanceId,
        ?GetAccessTokensRequest $request = null,
        array $headers = []
    ): mixed;

    /**
     * @param string $instanceId
     * @param array<string, string> $headers
     */
    public function deleteAccessToken(string $instanceId, array $headers = []): void;

    /**
     * @param string $instanceId
     * @param array<string, string> $headers
     */
    public function deleteAccessTokenAsync(string $instanceId, array $headers = []): mixed;

    /**
     * @param string $instanceId
     * @param int $serial
     * @param array<string, string> $headers
     */
    public function deleteAccessTokenBySerial(string $instanceId, int $serial, array $headers = []): void;

    /**
     * @param string $instanceId
     * @param int $serial
     * @param array<string, string> $headers
     */
    public function deleteAccessTokenBySerialAsync(string $instanceId, int $serial, array $headers = []): mixed;

    /**
     * @param string $instanceId
     * @param GetKycStatusRequest|null $request
     * @param array<string, string> $headers
     * @return MerchantAccountKycRedirectsResponse|array<string, mixed>|null
     */
    public function getKycStatus(
        string $instanceId,
        ?GetKycStatusRequest $request = null,
        array $headers = []
    ): MerchantAccountKycRedirectsResponse|array|null;

    /**
     * @param string $instanceId
     * @param GetKycStatusRequest|null $request
     * @param array<string, string> $headers
     */
    public function getKycStatusAsync(
        string $instanceId,
        ?GetKycStatusRequest $request = null,
        array $headers = []
    ): mixed;

    /**
     * @param string $instanceId
     * @param string $slug
     * @param GetMerchantStatisticsAmountRequest|null $request
     * @param array<string, string> $headers
     * @return MerchantStatisticsAmountResponse|array<string, mixed>
     */
    public function getMerchantStatisticsAmount(
        string $instanceId,
        string $slug,
        ?GetMerchantStatisticsAmountRequest $request = null,
        array $headers = []
    ): MerchantStatisticsAmountResponse|array;

    /**
     * @param string $instanceId
     * @param string $slug
     * @param GetMerchantStatisticsAmountRequest|null $request
     * @param array<string, string> $headers
     */
    public function getMerchantStatisticsAmountAsync(
        string $instanceId,
        string $slug,
        ?GetMerchantStatisticsAmountRequest $request = null,
        array $headers = []
    ): mixed;

    /**
     * @param string $instanceId
     * @param string $slug
     * @param GetMerchantStatisticsCounterRequest|null $request
     * @param array<string, string> $headers
     * @return MerchantStatisticsCounterResponse|array<string, mixed>
     */
    public function getMerchantStatisticsCounter(
        string $instanceId,
        string $slug,
        ?GetMerchantStatisticsCounterRequest $request = null,
        array $headers = []
    ): MerchantStatisticsCounterResponse|array;

    /**
     * @param string $instanceId
     * @param string $slug
     * @param GetMerchantStatisticsCounterRequest|null $request
     * @param array<string, string> $headers
     */
    public function getMerchantStatisticsCounterAsync(
        string $instanceId,
        string $slug,
        ?GetMerchantStatisticsCounterRequest $request = null,
        array $headers = []
    ): mixed;
}
