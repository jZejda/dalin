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
                    ->title(__('sport-event.actions.send_mail.notification_title'))
                    ->body(__('sport-event.actions.send_mail.notification_body'))
                    ->success()
                    ->seconds(8)
                    ->send();
            })
            ->color('gray')
            ->label(__('sport-event.actions.send_mail.label'))
            ->icon('heroicon-s-paper-airplane')
            ->modalHeading(__('sport-event.actions.send_mail.modal_heading'))
            ->modalDescription(__('sport-event.actions.send_mail.modal_description'))
            ->modalSubmitActionLabel(__('sport-event.actions.send_mail.modal_submit'))
            ->visible(auth()->user()->hasRole([AppRoles::SuperAdmin->value, AppRoles::EventMaster->value]))
            ->schema([
                Grid::make(1)
                    ->schema([
                        TextInput::make('subject')
                            ->label(__('sport-event.actions.send_mail.subject'))
                            ->required(),
                        TextInput::make('replyTo')
                            ->label(__('sport-event.actions.send_mail.reply_to'))
                            ->default(Auth::user()?->email),
                        MarkdownEditor::make('content')
                            ->label(__('sport-event.actions.send_mail.content'))
                            ->required(),
                    ]),

            ]);
    }
}
