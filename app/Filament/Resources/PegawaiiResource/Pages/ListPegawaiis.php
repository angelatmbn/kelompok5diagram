<?php

namespace App\Filament\Resources\PegawaiiResource\Pages;

use App\Filament\Resources\PegawaiiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPegawaiis extends ListRecords
{
    protected static string $resource = PegawaiiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
