<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Other\Pages;

use App\Enums\BadgeColor;
use App\Filament\Clusters\Other\OtherCluster;
use App\Filament\Pages\Actions\UserChangePassword;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserProfileSettings extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static ?string $cluster = OtherCluster::class;

    protected static ?int $navigationSort = 1;

    protected static string | \BackedEnum | null $navigationIcon = LucideIcon::User;

    protected string $view = 'filament.clusters.other.pages.user-profile-settings';

    protected static ?string $slug = 'profile';

    public string $name = '';

    public string $email = '';

    public string $badge_color = '';

    public mixed $avatar = null;

    public static function getNavigationLabel(): string
    {
        return __('user-profile-settings.navigation_label');
    }

    public function getTitle(): string
    {
        return __('user-profile-settings.title');
    }

    public function mount(): void
    {
        $user = $this->authUser();

        // Fill through the schema (not direct property assignment) so fields with their
        // own hydration lifecycle run it — notably FileUpload, which wraps its raw value
        // into an array via afterStateHydrated(). Skipping that leaves $this->avatar a
        // bare string/null, which breaks once the browser tries to set a nested
        // "avatar.<key>" path for a newly uploaded file.
        $this->getForm('form')?->fill([
            'name' => $user->name,
            'email' => $user->email,
            'badge_color' => $user->badge_color->value,
            'avatar' => $user->avatar_path,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            View::make('filament.clusters.other.pages.components.badge-preview')
                ->viewData(fn (Get $get): array => [
                    'initials' => $this->initialsFromName((string) $get('name')),
                    'color' => BadgeColor::tryFrom((string) $get('badge_color')),
                    'name' => $get('name'),
                    'email' => $get('email'),
                ]),
            TextInput::make('name')
                ->label(__('user-profile-settings.form.name'))
                ->required()
                ->maxLength(255)
                ->live(onBlur: true),
            TextInput::make('email')
                ->label(__('user-profile-settings.form.email'))
                ->disabled()
                ->dehydrated(false),
            Select::make('badge_color')
                ->label(__('user-profile-settings.form.badge_color'))
                ->helperText(__('user-profile-settings.form.badge_color_helper'))
                ->options(BadgeColor::enumArray())
                ->native(false)
                ->live()
                ->required(),
            FileUpload::make('avatar')
                ->label(__('user-profile-settings.form.avatar'))
                ->helperText(__('user-profile-settings.form.avatar_helper'))
                ->avatar()
                ->imageEditor()
                ->circleCropper()
                ->disk('public')
                ->directory(fn (): string => self::avatarDirectory($this->authUser()))
                ->visibility('public')
                ->maxSize(2048),
        ];
    }

    public function submit(): void
    {
        // FileUpload keeps its internal state as an array (keyed by an upload id) regardless
        // of "multiple" mode; getState() applies its state cast to collapse that back to a
        // single value in the array IT RETURNS. $this->avatar itself is never rewritten in
        // place, so the resolved path must be read from getState()'s return value, not from
        // the raw property.
        $data = $this->getForm('form')?->getState() ?? [];

        $user = $this->authUser();

        $newAvatarPath = $this->resolveAvatarPath($user, $data['avatar'] ?? null);
        $previousAvatarPath = $user->avatar_path;

        if (
            $previousAvatarPath !== null
            && $previousAvatarPath !== $newAvatarPath
            && Str::startsWith($previousAvatarPath, self::avatarDirectory($user).'/')
        ) {
            Storage::disk('public')->delete($previousAvatarPath);
        }

        $user->name = $this->name;
        $user->badge_color = BadgeColor::from($this->badge_color);
        $user->avatar_path = $newAvatarPath;
        $user->saveOrFail();

        Notification::make()
            ->title(__('user-profile-settings.notification.saved_title'))
            ->success()
            ->send();
    }

    /**
     * The FileUpload field is a plain Livewire property with no statePath binding, so its
     * value is client-controlled: a crafted request could set it to an arbitrary string
     * regardless of what the upload widget itself would ever produce. Only accept it as the
     * new avatar path when it is a real, existing file inside this user's own upload
     * directory, closing off both path traversal and pointing at another user's file.
     */
    private function resolveAvatarPath(User $user, mixed $avatar): ?string
    {
        if (!is_string($avatar)) {
            return null;
        }

        if (!Str::startsWith($avatar, self::avatarDirectory($user).'/')) {
            return null;
        }

        if (!Storage::disk('public')->exists($avatar)) {
            return null;
        }

        return $avatar;
    }

    private static function avatarDirectory(User $user): string
    {
        return 'avatars/'.$user->id;
    }

    private function authUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    protected function getHeaderActions(): array
    {
        return [
            UserChangePassword::getAction(),
        ];
    }

    private function initialsFromName(string $name): string
    {
        return Str::of($name)
            ->squish()
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
