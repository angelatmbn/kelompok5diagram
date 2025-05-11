<?php

<<<<<<< HEAD
// Di PegawaiiResource.php
=======
>>>>>>> 3f58c50 (menyelesaikan desain database, struktur migrasi serta trigger transaksi penjualan)
namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiiResource\Pages;
use App\Filament\Resources\PegawaiiResource\RelationManagers;
use App\Models\Pegawaii;
<<<<<<< HEAD
use App\Models\User;  // Import model User jika perlu
=======
>>>>>>> 3f58c50 (menyelesaikan desain database, struktur migrasi serta trigger transaksi penjualan)
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
<<<<<<< HEAD
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
=======
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
// use Filament\Forms\Components\InputMask;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload; //untuk tipe file


use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;

>>>>>>> 3f58c50 (menyelesaikan desain database, struktur migrasi serta trigger transaksi penjualan)

class PegawaiiResource extends Resource
{
    protected static ?string $model = Pegawaii::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id_pegawai')
<<<<<<< HEAD
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
                    ->afterStateUpdated(fn ($state, callable $set) => 
                        $set('harga', number_format((float) preg_replace('/[^0-9]/', '', $state), 0, ',', '.'))
                    ),
=======
                ->default(fn () => Pegawaii::getIdPegawai()) 
                ->label('Id Pegawai')
                ->required()
                ->readonly() 
            ,
            TextInput::make('nama')
                ->required()
                ->placeholder('Masukkan nama pegawai') 
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
   

>>>>>>> 3f58c50 (menyelesaikan desain database, struktur migrasi serta trigger transaksi penjualan)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_pegawai')
<<<<<<< HEAD
                    ->searchable(),
                TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->sortable(),
                TextColumn::make('alamat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('no_telp')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('shift')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gaji_pokok')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string|int|null $state): string => rupiah($state)),
=======
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
            TextColumn::make('shift')


>>>>>>> 3f58c50 (menyelesaikan desain database, struktur migrasi serta trigger transaksi penjualan)
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
<<<<<<< HEAD
=======

>>>>>>> 3f58c50 (menyelesaikan desain database, struktur migrasi serta trigger transaksi penjualan)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
<<<<<<< HEAD
            // Jika ada Relation Manager, bisa ditambahkan di sini
=======
            //
>>>>>>> 3f58c50 (menyelesaikan desain database, struktur migrasi serta trigger transaksi penjualan)
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
