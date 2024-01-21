<?php

declare(strict_types=1);

namespace App\Filament\Pages\Actions;

use App\Models\User;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;

final class UserChangePassword
{
    public static function getAction(): Action
    {
        return Action::make('setNewPassword')
            ->label(__('filament/common.change_password'))
            ->modalDescription(__('filament/common.user_setting.change_password_description', ['user' => auth()->user()?->name ?? '']))
            ->modalSubmitActionLabel(__('filament/common.user_setting.change_password_submit_label'))
            ->modalIcon('heroicon-o-finger-print')
            ->modalIconColor('danger')
            ->form([
                TextInput::make('password')
                    ->label(__('filament/common.new_password'))
                    ->required()
                    ->password()
                    ->minLength(6)
                    ->maxLength(128),
            ])
            ->action(function (array $data): void {
                /** @var User $user */
                $user = User::query()->where('id', '=', auth()->user()?->id)->first();

                $user->password = Hash::make($data['password']);
                $user->saveOrFail();

            });
    }
}
