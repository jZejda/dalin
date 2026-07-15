<?php

declare(strict_types=1);

namespace App\Filament\Resources\BankAccounts\Pages;

use App\Filament\Resources\BankAccounts\BankAccountResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBankAccount extends CreateRecord
{
    protected static string $resource = BankAccountResource::class;

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['account_credentials'] = $data['credentials'] ?? [];
        unset($data['credentials']);

        return $data;
    }
}
