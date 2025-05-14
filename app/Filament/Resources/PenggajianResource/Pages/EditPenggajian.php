<?php

namespace App\Filament\Resources\PenggajianResource\Pages;

use App\Filament\Resources\PenggajianResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Actions;

class EditPenggajian extends EditRecord
{
    protected static string $resource = PenggajianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_pegawai')
                    ->label('Pegawai')
                    ->relationship('pegawaii', 'nama')
                    ->required(),

                Forms\Components\DatePicker::make('tanggal')
                    ->required(),

                Forms\Components\TextInput::make('gaji_pokok')
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\TextInput::make('potongan')
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\TextInput::make('total_gaji')
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\Select::make('status_pembayaran')
                    ->options([
                        'belum' => 'Belum Dibayar',
                        'dibayar' => 'Dibayar',
                    ])
                    ->required(),
            ]);
    }
}
