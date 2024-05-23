<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector;

use App\Models\BankAccount;
use App\Services\Bank\Connector\MonetaResponseEntity\TransactionResponse;
use App\Services\Bank\Enums\TransactionIndicator;
use App\Shared\SymfonySerializer;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use function PHPUnit\Framework\stringStartsWith;

class MonetaBank implements ConnectorInterface
{
    public function __construct(private ?SymfonySerializer $serializer = null)
    {
        $this->serializer = $serializer ?? new SymfonySerializer();
    }

    public function getTransactions(BankAccount $bankAccount, ?Carbon $fromDate = null): ?array
    {
        $transactions = [];
        $response = $this->callBank($bankAccount, $fromDate);

        if ($response !== null) {
            foreach ($response->transactions as $transaction) {
                $transactions[] = new Transaction(
                    externalKey: $transaction->entryReference,
                    transactionIndicator: $this->getTransactionIndicators($transaction->creditDebitIndicator),
                    dateTime: Carbon::now(),
                    amount: $transaction->amount->value,
                    variable_symbol: $this->extractVariableSymbol($transaction->entryDetails->transactionDetails->remittanceInformation->structured->creditorReferenceInformation->reference),
                    specific_symbol: null,
                    constant_symbol: null,
                    note: $transaction->entryDetails->transactionDetails->references->transactionDescription,
                    description: null,
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
            return TransactionIndicator::DEBIT;
        } else {
            return TransactionIndicator::CREDIT;
        }

    }

    private function extractVariableSymbol(string $symbol): string
    {
        if (stringStartsWith('VS:', $symbol)) {
            return mb_substr($symbol, 3);
        }

        return $symbol;
    }

    private function callBank(BankAccount $bankAccount, ?Carbon $fromDate = null): ?TransactionResponse
    {
        $client = $this->getClient();
        $from = Carbon::parse('2024-05-17 00:00:00')->toIso8601String();

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
            //($e->getMessage());
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
