<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use Taler\Api\BankAccounts\Dto\AccountAddDetails;
use Taler\Api\BankAccounts\Dto\AccountAddResponse;
use Taler\Api\BankAccounts\Dto\AccountPatchDetails;
use Taler\Api\BankAccounts\Dto\AccountsSummaryResponse;
use Taler\Api\BankAccounts\Dto\BankAccountDetail;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;

interface BankAccountServiceInterface
{
    /**
     * @param AccountAddDetails $details
     * @param array<string, string> $headers
     * @return AccountAddResponse|ChallengeResponse|array<string, mixed>
     */
    public function createAccount(AccountAddDetails $details, array $headers = []): AccountAddResponse|ChallengeResponse|array;

    /**
     * @param AccountAddDetails $details
     * @param array<string, string> $headers
     */
    public function createAccountAsync(AccountAddDetails $details, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     * @return AccountsSummaryResponse|array<string, mixed>
     */
    public function getAccounts(array $headers = []): AccountsSummaryResponse|array;

    /**
     * @param array<string, string> $headers
     */
    public function getAccountsAsync(array $headers = []): mixed;

    /**
     * @param string $hWire
     * @param array<string, string> $headers
     * @return BankAccountDetail|array<string, mixed>
     */
    public function getAccount(string $hWire, array $headers = []): BankAccountDetail|array;

    /**
     * @param string $hWire
     * @param array<string, string> $headers
     */
    public function getAccountAsync(string $hWire, array $headers = []): mixed;

    /**
     * @param string $hWire
     * @param AccountPatchDetails $details
     * @param array<string, string> $headers
     */
    public function updateAccount(string $hWire, AccountPatchDetails $details, array $headers = []): void;

    /**
     * @param string $hWire
     * @param AccountPatchDetails $details
     * @param array<string, string> $headers
     */
    public function updateAccountAsync(string $hWire, AccountPatchDetails $details, array $headers = []): mixed;

    /**
     * @param string $hWire
     * @param array<string, string> $headers
     */
    public function deleteAccount(string $hWire, array $headers = []): void;

    /**
     * @param string $hWire
     * @param array<string, string> $headers
     */
    public function deleteAccountAsync(string $hWire, array $headers = []): mixed;
}
