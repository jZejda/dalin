<?php

declare(strict_types=1);

namespace App\Filament\Pages\Actions;

use Filament\Schemas\Components\Grid;
use App\Enums\AppRoles;
use App\Filament\Actions\HelpLinkAction;
use App\Filament\Pages\Jobs\SendUserMail;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class UserSendMail
{
    /** Roles allowed to send the e-mail; the page's header "Actions" group follows the same list. */
    public const array SENDER_ROLES = [
        AppRoles::SuperAdmin,
        AppRoles::EventMaster,
        AppRoles::BillingSpecialist,
        AppRoles::EventOrganizer,
    ];

    /** Roles offered as target groups, labelled by the role's own name. */
    private const array TARGET_ROLES = [
        AppRoles::Member,
        AppRoles::EventMaster,
        AppRoles::Redactor,
        AppRoles::EventOrganizer,
        AppRoles::BillingSpecialist,
    ];

    public static function canSend(?User $user): bool
    {
        return $user?->hasRole(self::SENDER_ROLES) ?? false;
    }

    public static function getAction(): Action
    {
        return Action::make('sendUserNotification')
            ->action(function (array $data): void {
                $recipients = (new SendUserMail(
                    $data['subject'],
                    $data['content'],
                    $data['replyTo'],
                    $data['targetUsers'],
                    Auth::user(),
                ))->send();

                if ($recipients === 0) {
                    Notification::make()
                        ->title(__('filament/user-setting.actions.send_mail.notification_empty_title'))
                        ->body(__('filament/user-setting.actions.send_mail.notification_empty_body'))
                        ->warning()
                        ->persistent()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title(__('filament/user-setting.actions.send_mail.notification_title'))
                    ->body(trans_choice('filament/user-setting.actions.send_mail.notification_body', $recipients, ['count' => $recipients]))
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
            ->visible(fn (): bool => self::canSend(Auth::user()))
            ->schema([
                Grid::make(1)
                    ->schema([
                        TextInput::make('subject')
                            ->label(__('filament/user-setting.actions.send_mail.subject'))
                            ->required(),
                        TextInput::make('replyTo')
                            ->label(__('filament/user-setting.actions.send_mail.reply_to'))
                            ->email()
                            ->default(Auth::user()?->email),
                        Select::make('targetUsers')
                            ->label(__('filament/user-setting.actions.send_mail.target_users'))
                            ->helperText(__('filament/user-setting.actions.send_mail.target_users_helper'))
                            ->hintAction(HelpLinkAction::to('stranka-uzivatelska-nastaveni', 'komu-se-e-mail-pošle'))
                            ->multiple()
                            ->options(self::targetOptions())
                            ->required()
                            ->minItems(1),
                        MarkdownEditor::make('content')
                            ->label(__('filament/user-setting.actions.send_mail.content'))
                            ->required(),
                    ]),

            ]);
    }

    /**
     * @return array<string, string>
     */
    private static function targetOptions(): array
    {
        $options = [SendUserMail::TARGET_ALL => __('filament/user-setting.actions.send_mail.target_users_all')];

        foreach (self::TARGET_ROLES as $role) {
            $options[$role->value] = (string) $role->getLabel();
        }

        return $options;
    }
}
