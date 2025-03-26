<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\kategoriMenu;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        // Pastikan harga disimpan sebagai angka tanpa pemisah ribuan
        $data['harga'] = (int) str_replace('.', '', $data['harga']);

        return $data;
    }
}
