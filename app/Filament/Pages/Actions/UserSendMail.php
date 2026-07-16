<?php

declare(strict_types=1);

namespace App\Filament\Pages\Actions;

use Filament\Schemas\Components\Grid;
use App\Enums\AppRoles;
use App\Filament\Pages\Jobs\SendUserMail;
use App\Models\SportEvent;
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
                    ->title(__('filament/user-setting.actions.send_mail.notification_title'))
                    ->body(__('filament/user-setting.actions.send_mail.notification_body'))
                    ->success()
                    ->seconds(8)
                    ->send();
            })
            ->color('gray')
            ->label(__('filament/user-setting.actions.send_mail.label'))
            ->icon('heroicon-s-paper-airplane')
            ->modalHeading(__('filament/user-setting.actions.send_mail.modal_heading'))
            ->modalDescription(__('filament/user-setting.actions.send_mail.modal_description'))
            ->modalSubmitActionLabel(__('filament/user-setting.actions.send_mail.modal_submit'))
            ->visible(auth()->user()->hasRole([
                AppRoles::SuperAdmin->value,
                AppRoles::EventMaster->value,
                AppRoles::BillingSpecialist->value,
                AppRoles::EventOrganizer->value,
            ]))
            ->schema([
                Grid::make(1)
                    ->schema([
                        TextInput::make('subject')
                            ->label(__('filament/user-setting.actions.send_mail.subject'))
                            ->required(),
                        TextInput::make('replyTo')
                            ->label(__('filament/user-setting.actions.send_mail.reply_to'))
                            ->default(Auth::user()?->email),
                        Select::make('targetUsers')
                            ->label(__('filament/user-setting.actions.send_mail.target_users'))
                            ->multiple()
                            ->options([
                                'all' => __('filament/user-setting.actions.send_mail.target_users_options.all'),
                                AppRoles::Member->value => __('filament/user-setting.actions.send_mail.target_users_options.'.AppRoles::Member->value),
                                AppRoles::EventMaster->value => __('filament/user-setting.actions.send_mail.target_users_options.'.AppRoles::EventMaster->value),
                                AppRoles::Redactor->value => __('filament/user-setting.actions.send_mail.target_users_options.'.AppRoles::Redactor->value),
                                AppRoles::EventOrganizer->value => __('filament/user-setting.actions.send_mail.target_users_options.'.AppRoles::EventOrganizer->value),
                                AppRoles::BillingSpecialist->value => __('filament/user-setting.actions.send_mail.target_users_options.'.AppRoles::BillingSpecialist->value),
                            ])
                            ->minItems(1),
                        MarkdownEditor::make('content')
                            ->label(__('filament/user-setting.actions.send_mail.content'))
                            ->required(),
                    ]),

            ]);
    }
}
