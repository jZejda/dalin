<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages\Actions;

use App\Enums\AppRoles;
use App\Filament\Resources\SportEventResource\Jobs\SendMail;
use App\Models\SportEvent;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action as ModalAction;
use Illuminate\Support\Facades\Auth;

class EntrySendMail
{
    private SportEvent $sportEvent;

    public function __construct(SportEvent $sportEvent)
    {
        $this->sportEvent = $sportEvent;
    }

    public function sendNotification(): ModalAction
    {

        return ModalAction::make('sendNotification')
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
            ->color('secondary')
            ->label('Pošli e-mail')
            ->icon('heroicon-s-paper-airplane')
            ->modalHeading('Pošle e-mailovou zprávu k závodu/akci')
            ->modalSubheading('E-mail je odesílán z fronty každý 5 minut.')
            ->modalButton('Odeslat')
            ->visible(auth()->user()->hasRole([AppRoles::SuperAdmin->value, AppRoles::EventMaster->value]))
            ->form([
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
