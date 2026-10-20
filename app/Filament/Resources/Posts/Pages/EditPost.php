<?php

declare(strict_types=1);

namespace App\Filament\Resources\Posts\Pages;

use App\Enums\ContentFormat;
use App\Filament\Resources\Posts\PostResource;
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Override;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('convertToTipTap')
                ->label(__('content.post.actions.convert_to_tiptap.label'))
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading(__('content.post.actions.convert_to_tiptap.modal_heading'))
                ->modalDescription(__('content.post.actions.convert_to_tiptap.modal_description'))
                ->modalSubmitActionLabel(__('content.post.actions.convert_to_tiptap.modal_submit'))
                ->visible(fn (Post $record): bool => $record->content_mode !== ContentFormat::TipTapJson)
                ->action(function (Post $record): void {
                    $record->convertToTipTap();

                    Notification::make()
                        ->title(__('content.post.actions.convert_to_tiptap.notification_title'))
                        ->success()
                        ->send();

                    // The form swaps its editor component, so load the page again instead of refilling it
                    $this->redirect(PostResource::getUrl('edit', ['record' => $record]));
                }),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
