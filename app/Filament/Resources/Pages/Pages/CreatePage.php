<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

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
