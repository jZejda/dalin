<?php

declare(strict_types=1);

namespace App\Filament\Resources\MemberFinances\Actions;

use Illuminate\Support\Facades\DB;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ExportMemberFinancesAction
{
    public static function make(): ExportBulkAction
    {
        return ExportBulkAction::make('exportMemberFinances')
            ->label(__('member-finance.actions.export.label'))
            ->icon('heroicon-o-document-arrow-down')
            ->exports([
                ExcelExport::make()
                    ->askForFilename(date('Y-m-d') . '_export_clenu')
                    ->askForWriterType()
                    ->modifyQueryUsing(fn ($query) => $query->addSelect([
                        'users.*',
                        'computed_balance' => DB::table('user_credits')
                            ->selectRaw('COALESCE(SUM(amount), 0)')
                            ->whereColumn('user_id', 'users.id'),
                    ]))
                    ->withColumns([
                        Column::make('name')->heading(__('member-finance.actions.export.col_name')),
                        Column::make('email')->heading(__('member-finance.actions.export.col_email')),
                        Column::make('payer_variable_symbol')->heading(__('member-finance.actions.export.col_variable_symbol')),
                        Column::make('computed_balance')->heading(__('member-finance.actions.export.col_balance')),
                        Column::make('active')
                            ->heading(__('member-finance.actions.export.col_active'))
                            ->formatStateUsing(fn ($state): string => $state ? __('member-finance.actions.export.active_yes') : __('member-finance.actions.export.active_no')),
                    ]),
            ]);
    }
}
