<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiiResource\Pages;
use App\Models\Pegawaii;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;

class PegawaiiResource extends Resource
{
    protected static ?string $model = Pegawaii::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Masterdata';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('id_pegawai')
                ->default(fn () => Pegawaii::getIdPegawai())
                ->label('Id Pegawai')
                ->required()
                ->readonly(),

            TextInput::make('nama')
                ->required()
                ->placeholder('Masukkan nama pegawai'),

            DatePicker::make('tanggal_lahir')
                ->label('Tanggal Lahir')
                ->required(),

            TextInput::make('alamat')
                ->label('Alamat')
                ->required(),

            TextInput::make('no_telp')
                ->label('Nomor Telp')
                ->required(),

            Select::make('shift')
                ->label('Shift')
                ->options([
                    'Siang' => 'Siang',
                    'Malam' => 'Malam',
                ])
                ->required()
                ->native(false)
                ->searchable(),

            TextInput::make('gaji_pokok')
                ->label('Gaji Pokok')
                ->required()
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('gaji_pokok', number_format((float) preg_replace('/[^0-9]/', '', $state), 0, ',', '.'));
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_pegawai')
                ->searchable(),
            // agar bisa di search
            TextColumn::make('nama')
                ->searchable()
                ->sortable(),
            TextColumn::make('tanggal_lahir')
                ->label('Tanggal Lahir')
                ->sortable()
            ,
            TextColumn::make('alamat')
                ->searchable()
                ->sortable(),
                TextColumn::make('no_telp')
                ->searchable()
                ->sortable(),
            TextColumn::make('shift') // Perubahan disini
            ->label('Shift')


            ])
            ->filters([
                //
                TextColumn::make('id_pegawai')->searchable(),
                TextColumn::make('nama')->searchable()->sortable(),
                TextColumn::make('tanggal_lahir')->label('Tanggal Lahir')->sortable(),
                TextColumn::make('alamat')->searchable()->sortable(),
                TextColumn::make('no_telp')->searchable()->sortable(),
                TextColumn::make('shift')->searchable()->sortable(),
                TextColumn::make('gaji_pokok')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string|int|null $state): string => rupiah($state)),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPegawaiis::route('/'),
            'create' => Pages\CreatePegawaii::route('/create'),
            'edit' => Pages\EditPegawaii::route('/{record}/edit'),
        ];
    }
}
