<?php

namespace App\Filament\Resources\PegawaiiResource\Pages;

use App\Filament\Resources\PegawaiiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPegawaii extends EditRecord
{
    protected static string $resource = PegawaiiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
