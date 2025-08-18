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
use Illuminate\Support\Facades\Log;

class FioBank implements ConnectorInterface
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

//                $transactions[] = new Transaction(
//                    externalKey: $transaction->entryReference,
//                    transactionIndicator: $this->getTransactionIndicators($transaction->creditDebitIndicator),
//                    dateTime: Carbon::createFromFormat('Y-m-d', $transaction->valueDate->date)?->setTime(0, 0, 0) ?? Carbon::now(),
//                    amount: $transaction->amount->value,
//                    currency: $transaction->amount->currency,
//                    bankAccountIdentifier: $this->extractPayerAccount($transaction),
//                    variableSymbol: $this->extractVariableSymbol($transaction->entryDetails->transactionDetails->remittanceInformation->structured->creditorReferenceInformation->reference),
//                    specificSymbol: null,
//                    constantSymbol: null,
//                    note: $transaction->entryDetails->transactionDetails->references->transactionDescription,
//                    description: null,
//                    error: null,
//                    status: $transaction->status
//                );
            }
        }

        return $transactions;
    }

    private function callBank(BankAccount $bankAccount, ?Carbon $fromDate = null): ?TransactionResponse
    {
        $client = $this->getClient();
        $from = $bankAccount->last_synced?->toDateString();

        if ($fromDate === null) {
            $from = Carbon::now()->startOfMonth()->startOfDay()->toDateString();
        }

        $today = Carbon::now()->startOfDay()->toDateString();

        $headers = [
            'Accept' => 'application/json',
        ];

        try {
            $response = $client->request(
                'GET',
                $bankAccount->account_credentials['token'].'/'.$from.'/'.$today.'/transactions.json',
                ['headers' => $headers]
            );

            dd($response->getBody()->getContents());

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
        return 'https://fioapi.fio.cz/v1/rest/periods/';
    }
}
