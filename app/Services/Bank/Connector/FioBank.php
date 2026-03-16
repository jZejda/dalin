<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector;

use LogicException;
use App\Models\BankAccount;
use App\Services\Bank\Connector\FioResponseEntity\TransactionResponse;
use App\Services\Bank\Connector\FioResponseEntity\Transaction as FioTransaction;
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
     * Retrieve Transaction value objects for the given bank account starting from an optional date.
     *
     * @param BankAccount $bankAccount The bank account to fetch transactions for.
     * @param Carbon|null $fromDate Optional start date to limit returned transactions; when omitted the account's last synced date or the start of the current month is used.
     * @return Transaction[] An array of Transaction objects; empty if no transactions were found.
     * @throws LogicException If a transaction is missing an external key.
     */
    public function getTransactions(BankAccount $bankAccount, ?Carbon $fromDate = null): ?array
    {
        $transactions = [];
        $response = $this->callBank($bankAccount, $fromDate);

        if ($response !== null) {
            foreach ($response->accountStatement->transactionList->transaction as $transaction) {

                if ($transaction->column22?->value === null) {
                    throw new LogicException('Transaction has no external key');
                }

                $transactions[] = new Transaction(
                    externalKey: (string)$transaction->column22->value,
                    transactionIndicator: $this->getTransactionIndicator($transaction),
                    dateTime: Carbon::createFromFormat('Y-m-dO', $transaction->column0->value ?? '')?->setTime(0, 0, 0) ?? Carbon::now(),
                    amount: (float)$transaction->column1?->value,
                    currency: $transaction->column5->value ?? 'CZK',
                    bankAccountIdentifier: $this->getBankAccountIdentifier($transaction),
                    variableSymbol: $transaction->column5?->value,
                    specificSymbol: null,
                    constantSymbol: $transaction->column4?->value,
                    note: $transaction->column16?->value,
                    description: $transaction->column25?->value,
                    error: null,
                    status: null
                );
            }
        }

        return $transactions;
    }

    private function getBankAccountIdentifier(FioTransaction $transaction): string
    {
        return $transaction->column2?->value . '/' . $transaction->column3?->value;
    }

    private function getTransactionIndicator(FioTransaction $transaction): TransactionIndicator
    {
        $amount = $transaction->column1?->value;

        if ($amount === null) {
            return TransactionIndicator::Debit;
        }

        return $amount < 0 ? TransactionIndicator::Debit : TransactionIndicator::Credit;
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
