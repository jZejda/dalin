<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector;

use App\Models\BankAccount;
use App\Services\Bank\Connector\MonetaResponseEntity\TransactionResponse;
use App\Services\Bank\Connector\MonetaResponseEntity\Transactions;
use App\Services\Bank\Enums\TransactionIndicator;
use App\Shared\SymfonySerializer;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class MonetaBank implements ConnectorInterface
{
    public function __construct(private ?SymfonySerializer $serializer = null)
    {
        $this->serializer = $serializer ?? new SymfonySerializer();
    }

    /**
     * @return Transaction[]|null
     */
    public function getTransactions(BankAccount $bankAccount, ?Carbon $fromDate = null): ?array
    {
        $transactions = [];
        $response = $this->callBank($bankAccount, $fromDate);

        if ($response !== null) {
            foreach ($response->transactions as $transaction) {
                $transactions[] = new Transaction(
                    externalKey: $transaction->entryReference,
                    transactionIndicator: $this->getTransactionIndicators($transaction->creditDebitIndicator),
                    dateTime: Carbon::createFromFormat('Y-m-d', $transaction->valueDate->date)?->setTime(0, 0, 0) ?? Carbon::now(),
                    amount: $transaction->amount->value,
                    currency: $transaction->amount->currency,
                    variable_symbol: $this->extractVariableSymbol($transaction->entryDetails->transactionDetails->remittanceInformation->structured->creditorReferenceInformation->reference),
                    specific_symbol: null,
                    constant_symbol: null,
                    note: $transaction->entryDetails->transactionDetails->references->transactionDescription,
                    description: $this->getDescription($transaction),
                    error: null,
                    status: $transaction->status
                );
            }
        }

        return $transactions;
    }

    private function getTransactionIndicators(string $indicator): TransactionIndicator
    {
        if ($indicator === 'DBIT') {
            return TransactionIndicator::Debit;
        } else {
            return TransactionIndicator::Credit;
        }
    }

    private function getDescription(Transactions $transaction): ?string
    {
        $identificationRaw = $transaction->entryDetails->transactionDetails->relatedParties->debtorAccount?->identification->other?->identification;
        $identification = explode(' ', $identificationRaw ?? '');
        $account = $identification[1] ?? null;
        $debtorName = $transaction->entryDetails->transactionDetails->relatedParties->debtor->name ?? null;
        $unstructuredDesc = $transaction->entryDetails->transactionDetails->remittanceInformation->unstructured ?? null;

        if ($debtorName === null && $account === null && $unstructuredDesc === null) {
            return null;
        }

        $description = [];
        if ($debtorName !== null) {
            $description[] = $debtorName;
        }
        if ($account !== null) {
            $description[] = $account;
        }
        if ($unstructuredDesc !== null) {
            $description[] = $unstructuredDesc;
        }

        return implode(' - ', $description);
    }

    private function extractVariableSymbol(string $symbol): string
    {
        if (str_starts_with($symbol, 'VS:')) {
            return mb_substr($symbol, 3);
        }

        return $symbol;
    }

    private function callBank(BankAccount $bankAccount, ?Carbon $fromDate = null): ?TransactionResponse
    {
        $client = $this->getClient();
        $from = $bankAccount->last_synced?->toIso8601String();
        if ($fromDate === null) {
            $from = Carbon::now()->startOfMonth()->startOfDay()->toIso8601String();
        }

        $headers = [
            'Authorization' => 'Bearer '.$bankAccount->account_credentials['token'],
            'Accept' => 'application/json',
        ];

        try {
            $response = $client->request(
                'GET',
                'vip/aisp/my/accounts/'.$bankAccount->account_credentials['account_id'].'/transactions?fromDate='.$from,
                ['headers' => $headers]
            );

            return $this->serializer?->getSerializer()->deserialize($response->getBody()->getContents(), TransactionResponse::class, 'json');
        } catch (GuzzleException $e) {
            Log::channel('site')->error('Bank Account MonetaMoneyBank exception: '.$e->getMessage());
        }

        return null;
    }

    private function getClient(): Client
    {
        return new Client(['base_uri' => $this->getBankBaseUrl()]);
    }

    private function getBankBaseUrl(): string
    {
        return 'https://api.moneta.cz/api/v1/';
    }
}
