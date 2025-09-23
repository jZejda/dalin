<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Actions;

use Filament\Actions\Action;
use Filament\Schemas\Components\Grid;
use App\Enums\AppRoles;
use App\Filament\Resources\SportEvents\Jobs\SendMail;
use App\Models\SportEvent;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EntrySendMail
{
    private SportEvent $sportEvent;

    public function __construct(SportEvent $sportEvent)
    {
        $this->sportEvent = $sportEvent;
    }

    public function sendNotification(): Action
    {

        return Action::make('sendNotification')
            ->action(function (array $data): void {
                /** @var SportEvent $sportEvent */

                (new SendMail(
                    $this->sportEvent,
                    $data['subject'],
                    $data['content'],
                    $data['replyTo'],
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
            ->modalHeading('Pošle e-mailovou zprávu k závodu/akci')
            ->modalDescription('E-mail je odesílán z fronty každý 5 minut.')
            ->modalSubmitActionLabel('Odeslat')
            ->visible(auth()->user()->hasRole([AppRoles::SuperAdmin->value, AppRoles::EventMaster->value]))
            ->schema([
                Grid::make(1)
                    ->schema([
                        TextInput::make('subject')
                            ->label('Předmět zprávy')
                            ->required(),
                        TextInput::make('replyTo')
                            ->label('Adresa pro odpovědi')
                            ->default(Auth::user()?->email),
                        MarkdownEditor::make('content')
                            ->label('Zpráva')
                            ->required(),
                    ]),

            ]);
    }
}
