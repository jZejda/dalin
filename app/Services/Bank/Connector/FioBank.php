<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector;

use App\Models\BankAccount;
use App\Services\Bank\Connector\FioResponseEntity\TransactionResponse;
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

        //dd($response);

        if ($response !== null) {
            foreach ($response->accountStatement->transactionList->transaction as $transaction) {

                $transactions[] = new Transaction(
                    externalKey: (string)$transaction->column22->value,
                    transactionIndicator: TransactionIndicator::Debit,
                    dateTime: Carbon::createFromFormat('Y-m-dO', $transaction->column0?->value)?->setTime(0, 0, 0) ?? Carbon::now(),
                    amount: (float)$transaction->column5?->value,
                    currency: $transaction->column5?->value ?? 'CZK',
                    bankAccountIdentifier: $transaction->column5?->value,
                    variableSymbol: $transaction->column5?->value,
                    specificSymbol: null,
                    constantSymbol: $transaction->column4?->value,
                    note: $transaction->column16?->value,
                    description: null,
                    error: null,
                    status: null
                );
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

            return $this->serializer?->getSerializer()->deserialize($response->getBody()->getContents(), TransactionResponse::class, 'json');
        } catch (GuzzleException $e) {
            Log::channel('site')->error('Bank Account FioMoneyBank exception: '.$e->getMessage());
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
