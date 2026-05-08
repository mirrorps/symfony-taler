<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use MirrorPS\TalerBundle\Taler;
use Taler\Api\BankAccounts\Dto\AccountAddDetails;
use Taler\Api\BankAccounts\Dto\AccountAddResponse;
use Taler\Api\BankAccounts\Dto\AccountPatchDetails;
use Taler\Api\BankAccounts\Dto\AccountsSummaryResponse;
use Taler\Api\BankAccounts\Dto\BankAccountDetail;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;

final class BankAccountService implements BankAccountServiceInterface
{
    public function __construct(private readonly Taler $taler)
    {
    }

    public function createAccount(AccountAddDetails $details, array $headers = []): AccountAddResponse|ChallengeResponse|array
    {
        return $this->taler->bankAccounts()->createAccount($details, $headers);
    }

    public function createAccountAsync(AccountAddDetails $details, array $headers = []): mixed
    {
        return $this->taler->bankAccounts()->createAccountAsync($details, $headers);
    }

    public function getAccounts(array $headers = []): AccountsSummaryResponse|array
    {
        return $this->taler->bankAccounts()->getAccounts($headers);
    }

    public function getAccountsAsync(array $headers = []): mixed
    {
        return $this->taler->bankAccounts()->getAccountsAsync($headers);
    }

    public function getAccount(string $hWire, array $headers = []): BankAccountDetail|array
    {
        return $this->taler->bankAccounts()->getAccount($hWire, $headers);
    }

    public function getAccountAsync(string $hWire, array $headers = []): mixed
    {
        return $this->taler->bankAccounts()->getAccountAsync($hWire, $headers);
    }

    public function updateAccount(string $hWire, AccountPatchDetails $details, array $headers = []): void
    {
        $this->taler->bankAccounts()->updateAccount($hWire, $details, $headers);
    }

    public function updateAccountAsync(string $hWire, AccountPatchDetails $details, array $headers = []): mixed
    {
        return $this->taler->bankAccounts()->updateAccountAsync($hWire, $details, $headers);
    }

    public function deleteAccount(string $hWire, array $headers = []): void
    {
        $this->taler->bankAccounts()->deleteAccount($hWire, $headers);
    }

    public function deleteAccountAsync(string $hWire, array $headers = []): mixed
    {
        return $this->taler->bankAccounts()->deleteAccountAsync($hWire, $headers);
    }
}
