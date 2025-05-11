<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use App\Models\KategoriMenu; // Ensure this is imported
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id_menu')
                    ->default(fn () => Menu::getIdMenu())
                    ->label('Id Menu')
                    ->required()
                    ->readonly(),

                TextInput::make('nama_menu')
                    ->required()
                    ->placeholder('Masukkan nama menu'),

                TextInput::make('harga')
                    ->required()
                    ->numeric() // Pastikan input hanya menerima angka
                    ->reactive()
                    ->extraAttributes(['id' => 'harga'])
                    ->placeholder('Masukkan harga menu')
                    ->live()
<<<<<<< HEAD
                    ->afterStateUpdated(fn ($state, callable $set) => 
=======
                    ->afterStateUpdated(fn ($state, callable $set) =>
>>>>>>> 3f58c50 (menyelesaikan desain database, struktur migrasi serta trigger transaksi penjualan)
                        $set('harga', number_format((float) preg_replace('/[^0-9]/', '', $state), 0, ',', '.'))
                    ),

                FileUpload::make('foto')
                    ->directory('foto')
                    ->required(),

                    Select::make('id_kategori')
                    ->label('Id Kategori')
                    ->options(KategoriMenu::all()->pluck('nama_kategori', 'id_kategori'))
                    ->required()
                    ->reactive()
<<<<<<< HEAD
                    ->afterStateUpdated(fn ($state, callable $set) => 
=======
                    ->afterStateUpdated(fn ($state, callable $set) =>
>>>>>>> 3f58c50 (menyelesaikan desain database, struktur migrasi serta trigger transaksi penjualan)
                        $set('nama_kategori', KategoriMenu::find($state)?->nama_kategori)
                    ),

                TextInput::make('nama_kategori')
                    ->label('Nama Kategori')
                    ->required()
                    ->disabled()
                    ->placeholder('Nama Kategori akan otomatis terisi'),
<<<<<<< HEAD
=======

                TextInput::make('stok')
                    ->required()
                    ->placeholder('Masukkan stok barang') // Placeholder untuk membantu pengguna
                    ->minValue(0)
>>>>>>> 3f58c50 (menyelesaikan desain database, struktur migrasi serta trigger transaksi penjualan)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_menu')->searchable(),
                TextColumn::make('nama_menu')->searchable()->sortable(),
                TextColumn::make('harga')
                    ->label('Harga menu')
                    ->formatStateUsing(fn (string|int|null $state): string => rupiah($state))
                    ->extraAttributes(['class' => 'text-right'])
                    ->sortable(),
<<<<<<< HEAD
                TextColumn::make('stok'),
                ImageColumn::make('foto'),
                TextColumn::make('id_kategori'),
                TextColumn::make('nama_kategori'),
=======
                ImageColumn::make('foto'),
                TextColumn::make('id_kategori'),
                TextColumn::make('nama_kategori'),
                TextColumn::make('stok'),
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}