<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Service;

use MirrorPS\TalerBundle\Service\InstanceService;
use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\Instance\Dto\GetAccessTokensRequest;
use Taler\Api\Instance\Dto\GetKycStatusRequest;
use Taler\Api\Instance\Dto\GetMerchantStatisticsAmountRequest;
use Taler\Api\Instance\Dto\GetMerchantStatisticsCounterRequest;
use Taler\Api\Instance\Dto\InstanceAuthConfigToken;
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
use Taler\Api\Instance\InstanceClient;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;
use Taler\Taler as TalerClient;

final class InstanceServiceTest extends TestCase
{
    private InstanceClient $instanceClient;
    private InstanceService $service;

    protected function setUp(): void
    {
        $this->instanceClient = $this->createMock(InstanceClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->method('instance')->willReturn($this->instanceClient);

        $taler = new Taler($client);
        $this->service = new InstanceService($taler);
    }

    // --- getInstances ---

    public function testGetInstancesDelegatesToClient(): void
    {
        $expected = $this->createMock(InstancesResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('getInstances')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getInstances());
    }

    public function testGetInstancesPassesHeaders(): void
    {
        $headers = ['X-Custom' => 'value'];
        $expected = $this->createMock(InstancesResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('getInstances')
            ->with($headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getInstances($headers));
    }

    public function testGetInstancesAsyncDelegatesToClient(): void
    {
        $expected = 'async-promise';

        $this->instanceClient->expects(self::once())
            ->method('getInstancesAsync')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getInstancesAsync());
    }

    // --- getInstance ---

    public function testGetInstanceDelegatesToClient(): void
    {
        $expected = $this->createMock(QueryInstancesResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('getInstance')
            ->with('default', [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getInstance('default'));
    }

    public function testGetInstancePassesHeaders(): void
    {
        $headers = ['Authorization' => 'Bearer token'];
        $expected = $this->createMock(QueryInstancesResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('getInstance')
            ->with('default', $headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getInstance('default', $headers));
    }

    public function testGetInstanceAsyncDelegatesToClient(): void
    {
        $expected = 'async-promise';

        $this->instanceClient->expects(self::once())
            ->method('getInstanceAsync')
            ->with('default', [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getInstanceAsync('default'));
    }

    // --- createInstance ---

    public function testCreateInstanceDelegatesToClient(): void
    {
        $config = $this->createMock(InstanceConfigurationMessage::class);

        $this->instanceClient->expects(self::once())
            ->method('createInstance')
            ->with($config, []);

        $this->service->createInstance($config);
    }

    public function testCreateInstancePassesHeaders(): void
    {
        $config = $this->createMock(InstanceConfigurationMessage::class);
        $headers = ['X-Custom' => 'value'];

        $this->instanceClient->expects(self::once())
            ->method('createInstance')
            ->with($config, $headers);

        $this->service->createInstance($config, $headers);
    }

    public function testCreateInstanceAsyncDelegatesToClient(): void
    {
        $config = $this->createMock(InstanceConfigurationMessage::class);
        $expected = 'async-promise';

        $this->instanceClient->expects(self::once())
            ->method('createInstanceAsync')
            ->with($config, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createInstanceAsync($config));
    }

    // --- updateInstance ---

    public function testUpdateInstanceDelegatesToClient(): void
    {
        $message = $this->createMock(InstanceReconfigurationMessage::class);

        $this->instanceClient->expects(self::once())
            ->method('updateInstance')
            ->with('coffee-shop', $message, []);

        $this->service->updateInstance('coffee-shop', $message);
    }

    public function testUpdateInstancePassesHeaders(): void
    {
        $message = $this->createMock(InstanceReconfigurationMessage::class);
        $headers = ['X-Request-Id' => '123'];

        $this->instanceClient->expects(self::once())
            ->method('updateInstance')
            ->with('coffee-shop', $message, $headers);

        $this->service->updateInstance('coffee-shop', $message, $headers);
    }

    public function testUpdateInstanceAsyncDelegatesToClient(): void
    {
        $message = $this->createMock(InstanceReconfigurationMessage::class);
        $expected = 'async-promise';

        $this->instanceClient->expects(self::once())
            ->method('updateInstanceAsync')
            ->with('coffee-shop', $message, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->updateInstanceAsync('coffee-shop', $message));
    }

    // --- deleteInstance ---

    public function testDeleteInstanceDelegatesToClient(): void
    {
        $this->instanceClient->expects(self::once())
            ->method('deleteInstance')
            ->with('coffee-shop', false, [])
            ->willReturn(null);

        self::assertNull($this->service->deleteInstance('coffee-shop'));
    }

    public function testDeleteInstanceWithPurge(): void
    {
        $this->instanceClient->expects(self::once())
            ->method('deleteInstance')
            ->with('coffee-shop', true, [])
            ->willReturn(null);

        self::assertNull($this->service->deleteInstance('coffee-shop', true));
    }

    public function testDeleteInstanceReturnsChallengeResponse(): void
    {
        $expected = $this->createMock(ChallengeResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('deleteInstance')
            ->with('coffee-shop', false, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->deleteInstance('coffee-shop'));
    }

    public function testDeleteInstanceAsyncDelegatesToClient(): void
    {
        $expected = 'async-promise';

        $this->instanceClient->expects(self::once())
            ->method('deleteInstanceAsync')
            ->with('coffee-shop', false, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->deleteInstanceAsync('coffee-shop'));
    }

    // --- updateAuth ---

    public function testUpdateAuthDelegatesToClient(): void
    {
        $authConfig = $this->createMock(InstanceAuthConfigToken::class);

        $this->instanceClient->expects(self::once())
            ->method('updateAuth')
            ->with('coffee-shop', $authConfig, [])
            ->willReturn(null);

        self::assertNull($this->service->updateAuth('coffee-shop', $authConfig));
    }

    public function testUpdateAuthReturnsChallengeResponse(): void
    {
        $authConfig = $this->createMock(InstanceAuthConfigToken::class);
        $expected = $this->createMock(ChallengeResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('updateAuth')
            ->with('coffee-shop', $authConfig, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->updateAuth('coffee-shop', $authConfig));
    }

    public function testUpdateAuthPassesHeaders(): void
    {
        $authConfig = $this->createMock(InstanceAuthConfigToken::class);
        $headers = ['X-Custom' => 'value'];

        $this->instanceClient->expects(self::once())
            ->method('updateAuth')
            ->with('coffee-shop', $authConfig, $headers)
            ->willReturn(null);

        self::assertNull($this->service->updateAuth('coffee-shop', $authConfig, $headers));
    }

    public function testUpdateAuthAsyncDelegatesToClient(): void
    {
        $authConfig = $this->createMock(InstanceAuthConfigToken::class);
        $expected = 'async-promise';

        $this->instanceClient->expects(self::once())
            ->method('updateAuthAsync')
            ->with('coffee-shop', $authConfig, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->updateAuthAsync('coffee-shop', $authConfig));
    }

    // --- forgotPassword ---

    public function testForgotPasswordDelegatesToClient(): void
    {
        $authConfig = $this->createMock(InstanceAuthConfigToken::class);

        $this->instanceClient->expects(self::once())
            ->method('forgotPassword')
            ->with('coffee-shop', $authConfig, [])
            ->willReturn(null);

        self::assertNull($this->service->forgotPassword('coffee-shop', $authConfig));
    }

    public function testForgotPasswordReturnsChallengeResponse(): void
    {
        $authConfig = $this->createMock(InstanceAuthConfigToken::class);
        $expected = $this->createMock(ChallengeResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('forgotPassword')
            ->with('coffee-shop', $authConfig, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->forgotPassword('coffee-shop', $authConfig));
    }

    public function testForgotPasswordAsyncDelegatesToClient(): void
    {
        $authConfig = $this->createMock(InstanceAuthConfigToken::class);
        $expected = 'async-promise';

        $this->instanceClient->expects(self::once())
            ->method('forgotPasswordAsync')
            ->with('coffee-shop', $authConfig, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->forgotPasswordAsync('coffee-shop', $authConfig));
    }

    // --- getAccessToken ---

    public function testGetAccessTokenDelegatesToClient(): void
    {
        $request = $this->createMock(LoginTokenRequest::class);
        $expected = $this->createMock(LoginTokenSuccessResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('getAccessToken')
            ->with('coffee-shop', $request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getAccessToken('coffee-shop', $request));
    }

    public function testGetAccessTokenReturnsChallengeResponse(): void
    {
        $request = $this->createMock(LoginTokenRequest::class);
        $expected = $this->createMock(ChallengeResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('getAccessToken')
            ->with('coffee-shop', $request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getAccessToken('coffee-shop', $request));
    }

    public function testGetAccessTokenPassesHeaders(): void
    {
        $request = $this->createMock(LoginTokenRequest::class);
        $headers = ['X-Custom' => 'value'];
        $expected = $this->createMock(LoginTokenSuccessResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('getAccessToken')
            ->with('coffee-shop', $request, $headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getAccessToken('coffee-shop', $request, $headers));
    }

    public function testGetAccessTokenAsyncDelegatesToClient(): void
    {
        $request = $this->createMock(LoginTokenRequest::class);
        $expected = 'async-promise';

        $this->instanceClient->expects(self::once())
            ->method('getAccessTokenAsync')
            ->with('coffee-shop', $request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getAccessTokenAsync('coffee-shop', $request));
    }

    // --- getAccessTokens ---

    public function testGetAccessTokensDelegatesToClient(): void
    {
        $request = new GetAccessTokensRequest(limit: 20);
        $expected = $this->createMock(TokenInfos::class);

        $this->instanceClient->expects(self::once())
            ->method('getAccessTokens')
            ->with('coffee-shop', $request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getAccessTokens('coffee-shop', $request));
    }

    public function testGetAccessTokensWithNullRequest(): void
    {
        $expected = $this->createMock(TokenInfos::class);

        $this->instanceClient->expects(self::once())
            ->method('getAccessTokens')
            ->with('coffee-shop', null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getAccessTokens('coffee-shop'));
    }

    public function testGetAccessTokensReturnsNull(): void
    {
        $this->instanceClient->expects(self::once())
            ->method('getAccessTokens')
            ->with('coffee-shop', null, [])
            ->willReturn(null);

        self::assertNull($this->service->getAccessTokens('coffee-shop'));
    }

    public function testGetAccessTokensAsyncDelegatesToClient(): void
    {
        $expected = 'async-promise';

        $this->instanceClient->expects(self::once())
            ->method('getAccessTokensAsync')
            ->with('coffee-shop', null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getAccessTokensAsync('coffee-shop'));
    }

    // --- deleteAccessToken ---

    public function testDeleteAccessTokenDelegatesToClient(): void
    {
        $this->instanceClient->expects(self::once())
            ->method('deleteAccessToken')
            ->with('coffee-shop', []);

        $this->service->deleteAccessToken('coffee-shop');
    }

    public function testDeleteAccessTokenPassesHeaders(): void
    {
        $headers = ['Authorization' => 'Bearer token'];

        $this->instanceClient->expects(self::once())
            ->method('deleteAccessToken')
            ->with('coffee-shop', $headers);

        $this->service->deleteAccessToken('coffee-shop', $headers);
    }

    public function testDeleteAccessTokenAsyncDelegatesToClient(): void
    {
        $expected = 'async-promise';

        $this->instanceClient->expects(self::once())
            ->method('deleteAccessTokenAsync')
            ->with('coffee-shop', [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->deleteAccessTokenAsync('coffee-shop'));
    }

    // --- deleteAccessTokenBySerial ---

    public function testDeleteAccessTokenBySerialDelegatesToClient(): void
    {
        $this->instanceClient->expects(self::once())
            ->method('deleteAccessTokenBySerial')
            ->with('coffee-shop', 42, []);

        $this->service->deleteAccessTokenBySerial('coffee-shop', 42);
    }

    public function testDeleteAccessTokenBySerialPassesHeaders(): void
    {
        $headers = ['X-Custom' => 'value'];

        $this->instanceClient->expects(self::once())
            ->method('deleteAccessTokenBySerial')
            ->with('coffee-shop', 42, $headers);

        $this->service->deleteAccessTokenBySerial('coffee-shop', 42, $headers);
    }

    public function testDeleteAccessTokenBySerialAsyncDelegatesToClient(): void
    {
        $expected = 'async-promise';

        $this->instanceClient->expects(self::once())
            ->method('deleteAccessTokenBySerialAsync')
            ->with('coffee-shop', 42, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->deleteAccessTokenBySerialAsync('coffee-shop', 42));
    }

    // --- getKycStatus ---

    public function testGetKycStatusDelegatesToClient(): void
    {
        $request = $this->createMock(GetKycStatusRequest::class);
        $expected = $this->createMock(MerchantAccountKycRedirectsResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('getKycStatus')
            ->with('coffee-shop', $request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getKycStatus('coffee-shop', $request));
    }

    public function testGetKycStatusWithNullRequest(): void
    {
        $this->instanceClient->expects(self::once())
            ->method('getKycStatus')
            ->with('coffee-shop', null, [])
            ->willReturn(null);

        self::assertNull($this->service->getKycStatus('coffee-shop'));
    }

    public function testGetKycStatusAsyncDelegatesToClient(): void
    {
        $expected = 'async-promise';

        $this->instanceClient->expects(self::once())
            ->method('getKycStatusAsync')
            ->with('coffee-shop', null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getKycStatusAsync('coffee-shop'));
    }

    // --- getMerchantStatisticsAmount ---

    public function testGetMerchantStatisticsAmountDelegatesToClient(): void
    {
        $request = $this->createMock(GetMerchantStatisticsAmountRequest::class);
        $expected = $this->createMock(MerchantStatisticsAmountResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('getMerchantStatisticsAmount')
            ->with('coffee-shop', 'revenue', $request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getMerchantStatisticsAmount('coffee-shop', 'revenue', $request));
    }

    public function testGetMerchantStatisticsAmountWithNullRequest(): void
    {
        $expected = $this->createMock(MerchantStatisticsAmountResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('getMerchantStatisticsAmount')
            ->with('coffee-shop', 'revenue', null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getMerchantStatisticsAmount('coffee-shop', 'revenue'));
    }

    public function testGetMerchantStatisticsAmountPassesHeaders(): void
    {
        $headers = ['X-Custom' => 'value'];
        $expected = $this->createMock(MerchantStatisticsAmountResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('getMerchantStatisticsAmount')
            ->with('coffee-shop', 'revenue', null, $headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getMerchantStatisticsAmount('coffee-shop', 'revenue', null, $headers));
    }

    public function testGetMerchantStatisticsAmountAsyncDelegatesToClient(): void
    {
        $expected = 'async-promise';

        $this->instanceClient->expects(self::once())
            ->method('getMerchantStatisticsAmountAsync')
            ->with('coffee-shop', 'revenue', null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getMerchantStatisticsAmountAsync('coffee-shop', 'revenue'));
    }

    // --- getMerchantStatisticsCounter ---

    public function testGetMerchantStatisticsCounterDelegatesToClient(): void
    {
        $request = $this->createMock(GetMerchantStatisticsCounterRequest::class);
        $expected = $this->createMock(MerchantStatisticsCounterResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('getMerchantStatisticsCounter')
            ->with('coffee-shop', 'orders', $request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getMerchantStatisticsCounter('coffee-shop', 'orders', $request));
    }

    public function testGetMerchantStatisticsCounterWithNullRequest(): void
    {
        $expected = $this->createMock(MerchantStatisticsCounterResponse::class);

        $this->instanceClient->expects(self::once())
            ->method('getMerchantStatisticsCounter')
            ->with('coffee-shop', 'orders', null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getMerchantStatisticsCounter('coffee-shop', 'orders'));
    }

    public function testGetMerchantStatisticsCounterAsyncDelegatesToClient(): void
    {
        $expected = 'async-promise';

        $this->instanceClient->expects(self::once())
            ->method('getMerchantStatisticsCounterAsync')
            ->with('coffee-shop', 'orders', null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getMerchantStatisticsCounterAsync('coffee-shop', 'orders'));
    }
}
