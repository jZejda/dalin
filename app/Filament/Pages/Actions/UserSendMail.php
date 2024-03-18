<?php

declare(strict_types=1);

namespace App\Filament\Pages\Actions;

use App\Enums\AppRoles;
use App\Filament\Pages\Jobs\SendUserMail;
use App\Models\SportEvent;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

final class UserSendMail
{
    public static function getAction(): Action
    {
        return Action::make('sendUserNotification')
            ->action(function (array $data): void {
                /** @var SportEvent $sportEvent */

                (new SendUserMail(
                    $data['subject'],
                    $data['content'],
                    $data['replyTo'],
                    $data['targetUsers'],
                ))->send();

                Notification::make()
                    ->title('E-mail rozeslán')
                    ->body('Přihlášeným uživatelům byl odeslán e-mail.')
                    ->success()
                    ->seconds(8)
                    ->send();
            })
            ->color('gray')
            ->label('Pošli e-mail')
            ->icon('heroicon-s-paper-airplane')
            ->modalHeading('Pošle e-mailovou vybraným skupinám uživatelů systému.')
            ->modalDescription('E-mail je odesílán z fronty každý 5 minut.')
            ->modalSubmitActionLabel('Odeslat')
            ->visible(auth()->user()->hasRole([
                AppRoles::SuperAdmin->value,
                AppRoles::EventMaster->value,
                AppRoles::BillingSpecialist->value,
                AppRoles::EventOrganizer->value,
            ]))
            ->form([
                Grid::make(1)
                    ->schema([
                        TextInput::make('subject')
                            ->label('Předmět zprávy')
                            ->required(),
                        TextInput::make('replyTo')
                            ->label('Adresa pro odpovědi')
                            ->default(Auth::user()?->email),
                        Select::make('targetUsers')
                            ->multiple()
                            ->options([
                                'all' => 'Všem aktivním uživatelům',
                                AppRoles::Member->value => 'Členové',
                                AppRoles::EventMaster->value => 'Správce závodů',
                                AppRoles::Redactor->value => 'Redaktor',
                                AppRoles::EventOrganizer->value => 'Organizátor závodů',
                                AppRoles::BillingSpecialist->value => 'Finančník',
                            ])
                            ->minItems(1),
                        MarkdownEditor::make('content')
                            ->label('Zpráva')
                            ->required(),
                    ]),

            ]);
    }
}
