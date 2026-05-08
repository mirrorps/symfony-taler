<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Service;

use MirrorPS\TalerBundle\Service\BankAccountService;
use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\BankAccounts\BankAccountClient;
use Taler\Api\BankAccounts\Dto\AccountAddDetails;
use Taler\Api\BankAccounts\Dto\AccountAddResponse;
use Taler\Api\BankAccounts\Dto\AccountPatchDetails;
use Taler\Api\BankAccounts\Dto\AccountsSummaryResponse;
use Taler\Api\BankAccounts\Dto\BankAccountDetail;
use Taler\Api\BankAccounts\Dto\BankAccountEntry;
use Taler\Taler as TalerClient;

final class BankAccountServiceTest extends TestCase
{
    private BankAccountClient $bankAccountClient;
    private BankAccountService $service;

    protected function setUp(): void
    {
        $this->bankAccountClient = $this->createMock(BankAccountClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->method('bankAccount')->willReturn($this->bankAccountClient);

        $taler = new Taler($client);
        $this->service = new BankAccountService($taler);
    }

    public function testCreateAccountDelegatesToClient(): void
    {
        $details = new AccountAddDetails('payto://iban/DE75512108001245126199?receiver-name=Sandbox');
        $expected = new AccountAddResponse(h_wire: 'hash-1', salt: 'salt-1');

        $this->bankAccountClient->expects(self::once())
            ->method('createAccount')
            ->with($details, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createAccount($details));
    }

    public function testCreateAccountPassesHeaders(): void
    {
        $details = new AccountAddDetails('payto://iban/DE75512108001245126199?receiver-name=Sandbox');
        $headers = ['X-Trace-Id' => 'bank-account-create'];
        $expected = new AccountAddResponse(h_wire: 'hash-2', salt: 'salt-2');

        $this->bankAccountClient->expects(self::once())
            ->method('createAccount')
            ->with($details, $headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createAccount($details, $headers));
    }

    public function testCreateAccountAsyncDelegatesToClient(): void
    {
        $details = new AccountAddDetails('payto://iban/DE75512108001245126199?receiver-name=Sandbox');
        $expected = 'async-promise';

        $this->bankAccountClient->expects(self::once())
            ->method('createAccountAsync')
            ->with($details, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createAccountAsync($details));
    }

    public function testGetAccountsDelegatesToClient(): void
    {
        $expected = new AccountsSummaryResponse([
            new BankAccountEntry(
                payto_uri: 'payto://iban/DE75512108001245126199?receiver-name=Sandbox',
                h_wire: 'hash-1',
                active: true,
            ),
        ]);

        $this->bankAccountClient->expects(self::once())
            ->method('getAccounts')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getAccounts());
    }

    public function testGetAccountsPassesHeaders(): void
    {
        $headers = ['X-Custom' => 'value'];
        $expected = new AccountsSummaryResponse([]);

        $this->bankAccountClient->expects(self::once())
            ->method('getAccounts')
            ->with($headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getAccounts($headers));
    }

    public function testGetAccountsAsyncDelegatesToClient(): void
    {
        $expected = 'async-promise';

        $this->bankAccountClient->expects(self::once())
            ->method('getAccountsAsync')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getAccountsAsync());
    }

    public function testGetAccountDelegatesToClient(): void
    {
        $hWire = 'hash-1';
        $expected = new BankAccountDetail(
            payto_uri: 'payto://iban/DE75512108001245126199?receiver-name=Sandbox',
            h_wire: $hWire,
            salt: 'salt-1',
            active: true,
        );

        $this->bankAccountClient->expects(self::once())
            ->method('getAccount')
            ->with($hWire, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getAccount($hWire));
    }

    public function testGetAccountPassesHeaders(): void
    {
        $hWire = 'hash-2';
        $headers = ['X-Trace-Id' => 'bank-account-get'];
        $expected = new BankAccountDetail(
            payto_uri: 'payto://iban/DE75512108001245126199?receiver-name=Sandbox',
            h_wire: $hWire,
            salt: 'salt-2',
            active: false,
        );

        $this->bankAccountClient->expects(self::once())
            ->method('getAccount')
            ->with($hWire, $headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getAccount($hWire, $headers));
    }

    public function testGetAccountAsyncDelegatesToClient(): void
    {
        $hWire = 'hash-async';
        $expected = 'async-promise';

        $this->bankAccountClient->expects(self::once())
            ->method('getAccountAsync')
            ->with($hWire, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getAccountAsync($hWire));
    }

    public function testUpdateAccountDelegatesToClient(): void
    {
        $hWire = 'hash-update';
        $details = new AccountPatchDetails(credit_facade_url: 'https://bank.example.test/facade');

        $this->bankAccountClient->expects(self::once())
            ->method('updateAccount')
            ->with($hWire, $details, []);

        $this->service->updateAccount($hWire, $details);
    }

    public function testUpdateAccountPassesHeaders(): void
    {
        $hWire = 'hash-update-headers';
        $details = new AccountPatchDetails(credit_facade_url: 'https://bank.example.test/facade');
        $headers = ['X-Custom' => 'value'];

        $this->bankAccountClient->expects(self::once())
            ->method('updateAccount')
            ->with($hWire, $details, $headers);

        $this->service->updateAccount($hWire, $details, $headers);
    }

    public function testUpdateAccountAsyncDelegatesToClient(): void
    {
        $hWire = 'hash-update-async';
        $details = new AccountPatchDetails(credit_facade_url: 'https://bank.example.test/facade');
        $expected = 'async-promise';

        $this->bankAccountClient->expects(self::once())
            ->method('updateAccountAsync')
            ->with($hWire, $details, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->updateAccountAsync($hWire, $details));
    }

    public function testDeleteAccountDelegatesToClient(): void
    {
        $hWire = 'hash-delete';

        $this->bankAccountClient->expects(self::once())
            ->method('deleteAccount')
            ->with($hWire, []);

        $this->service->deleteAccount($hWire);
    }

    public function testDeleteAccountPassesHeaders(): void
    {
        $hWire = 'hash-delete-headers';
        $headers = ['Authorization' => 'Bearer token'];

        $this->bankAccountClient->expects(self::once())
            ->method('deleteAccount')
            ->with($hWire, $headers);

        $this->service->deleteAccount($hWire, $headers);
    }

    public function testDeleteAccountAsyncDelegatesToClient(): void
    {
        $hWire = 'hash-delete-async';
        $expected = 'async-promise';

        $this->bankAccountClient->expects(self::once())
            ->method('deleteAccountAsync')
            ->with($hWire, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->deleteAccountAsync($hWire));
    }
}
