<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\TextInput;
// use Filament\Forms\Components\InputMask;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload; //untuk tipe file

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

use App\Filament\Resources\KategoriMenuResource\Pages;
use App\Filament\Resources\KategoriMenuResource\RelationManagers;
use App\Models\kategoriMenu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoriMenuResource extends Resource
{
    protected static ?string $model = KategoriMenu::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Masterdata';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('id_kategori')
            ->default(fn () => kategorimenu::getKategoriMenu()) // Ambil default dari method getKategoriMenu
                ->label('ID Kategori')
                ->required()
                ->placeholder('Masukkan id kategori')
                ->readonly()
                ->unique(ignoreRecord: true) //mencegah duplikasi
            ,
            TextInput::make('nama_kategori')
                ->autocapitalize('words')
                ->label('Nama kategori')
                ->required()
                ->placeholder('Masukkan nama kategori')
            ,
            FileUpload::make('gambar')
            ->directory('gambar')
            ->required()
            ,
            TextInput::make('deskripsi')
                ->autocapitalize('words')
                ->label('deskripsi kategori')
                ->required()
                ->placeholder('Masukkan deskripsi dari kategori tersebut')
            ,
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
                TextColumn::make('id_kategori')
                    ->searchable(),
                TextColumn::make('nama_kategori')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('gambar'),
                TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->searchable(),
            ])
            ->filters([

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
            'index' => Pages\ListKategoriMenu::route('/'),
            'create' => Pages\CreateKategoriMenu::route('/create'),
            'edit' => Pages\EditKategoriMenu::route('/{record:id_kategori}/edit'),
        ];
    }
}
