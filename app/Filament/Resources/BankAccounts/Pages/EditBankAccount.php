<?php

declare(strict_types=1);

namespace App\Filament\Resources\BankAccounts\Pages;

use App\Filament\Resources\BankAccounts\BankAccountResource;
use App\Models\BankAccount;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBankAccount extends EditRecord
{
    protected static string $resource = BankAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Credentials are write-only: an empty input keeps the stored value.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var BankAccount $record */
        $record = $this->getRecord();

        /** @var array<string, string> $incoming */
        $incoming = array_filter($data['credentials'] ?? [], static fn (?string $value): bool => filled($value));

        $data['account_credentials'] = array_merge($record->account_credentials ?? [], $incoming);
        unset($data['credentials']);

        return $data;
    }
}
