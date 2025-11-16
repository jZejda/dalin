<?php

namespace App\Filament\Resources\Pages\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Resources\Pages\PageResource;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Při načítání dat převedeme KeyValue formát (meta) na Repeater formát (meta_items)
        if (isset($data['meta']) && is_array($data['meta'])) {
            $metaItems = [];
            foreach ($data['meta'] as $key => $value) {
                $metaItems[] = [
                    'key' => $key,
                    'value' => $value,
                ];
            }
            $data['meta_items'] = $metaItems;
        } else {
            $data['meta_items'] = [];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Při ukládání převedeme Repeater formát (meta_items) na KeyValue formát (meta)
        if (isset($data['meta_items']) && is_array($data['meta_items'])) {
            $meta = [];
            foreach ($data['meta_items'] as $item) {
                if (isset($item['key']) && isset($item['value']) && !empty($item['key'])) {
                    $meta[$item['key']] = $item['value'];
                }
            }
            $data['meta'] = $meta;
            unset($data['meta_items']);
        }

        return $data;
    }
}
