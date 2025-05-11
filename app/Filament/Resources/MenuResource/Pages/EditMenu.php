<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\kategoriMenu;

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $kategori = KategoriMenu::find($data['id_kategori']);
        $data['nama_kategori'] = $kategori ? $kategori->nama_kategori : null;

        // Pastikan harga tetap angka saat diupdate
        $data['harga'] = (int) str_replace('.', '', $data['harga']);

        return $data;
    }
}
