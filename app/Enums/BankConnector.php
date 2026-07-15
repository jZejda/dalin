<?php

declare(strict_types=1);

namespace App\Enums;

use App\Services\Bank\Connector\ConnectorInterface;
use App\Services\Bank\Connector\FioBank;
use App\Services\Bank\Connector\MonetaBank;

enum BankConnector: string
{
    case MonetaMoneyBank = 'monetaMoneyBank';
    case FioBank = 'fioBank';

    /** @return array<string, string> */
    public static function enumArray(): array
    {
        return [
            self::MonetaMoneyBank->value => self::MonetaMoneyBank->label(),
            self::FioBank->value => self::FioBank->label(),
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::MonetaMoneyBank => 'Moneta Money Bank',
            self::FioBank => 'Fio banka',
        };
    }

    /** @return class-string<ConnectorInterface> */
    public function connectorClass(): string
    {
        return match ($this) {
            self::MonetaMoneyBank => MonetaBank::class,
            self::FioBank => FioBank::class,
        };
    }

    public function connector(): ConnectorInterface
    {
        return app($this->connectorClass());
    }

    /**
     * Credentials required for a working connection, key => human label.
     *
     * @return array<string, string>
     */
    public function credentialFields(): array
    {
        return match ($this) {
            self::MonetaMoneyBank => [
                'token' => 'API token',
                'account_id' => 'ID účtu (account_id)',
            ],
            self::FioBank => [
                'token' => 'API token',
            ],
        };
    }
}
