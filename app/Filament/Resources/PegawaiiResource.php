<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiiResource\Pages;
use App\Filament\Resources\PegawaiiResource\RelationManagers;
use App\Models\Pegawaii;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
// use Filament\Forms\Components\InputMask;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload; //untuk tipe file


use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;


class PegawaiiResource extends Resource
{
    protected static ?string $model = Pegawaii::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id_pegawai')
                ->default(fn () => Pegawaii::getIdPegawai()) // Ambil default dari method getKodeBarang
                ->label('Id Pegawai')
                ->required()
                ->readonly() // Membuat field menjadi read-only
            ,
            TextInput::make('nama')
                ->required()
                ->placeholder('Masukkan nama pegawai') // Placeholder untuk membantu pengguna
            ,
            DatePicker::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->required()
                ,
                TextInput::make('alamat')
                ->label('Alamat')
                ->required()
                ,
                TextInput::make('no_telp')
                ->label('Nomor Telp')
                ->required()
                ,
         Select::make('shift')
      ->label('Shift')
      ->options([
        'Siang' => 'Siang',
        'Malam' => 'Malam',])
    ->required()
    ->native(false) // Opsional: Gunakan dropdown yang lebih cantik di UI
    ->searchable() // Opsional: Jika ingin bisa diketik untuk mencari opsi
            ,
   

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
            TextColumn::make('Shift')


            ])
            ->filters([
                //
            ])
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
        return [
            //
        ];
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
