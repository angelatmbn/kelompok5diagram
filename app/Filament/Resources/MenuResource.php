<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use App\Models\KategoriMenu;
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

    protected static ?string $navigationGroup = 'Masterdata';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id_menu')
                    ->label('ID Menu')
                    ->default(fn () => Menu::getIdMenu())
                    ->required()
                    ->readonly(),

                TextInput::make('nama_menu')
                    ->label('Nama Menu')
                    ->required()
                    ->placeholder('Masukkan nama menu'),

                TextInput::make('harga')
                    ->label('Harga')
                    ->required()
                    ->numeric()
                    ->placeholder('Masukkan harga menu')
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) =>
                        $set('harga', number_format((float) preg_replace('/[^0-9]/', '', $state), 0, ',', '.'))
                    ),

                FileUpload::make('foto')
                    ->label('Foto Menu')
                    ->directory('foto')
                    ->required(),

                Select::make('id_kategori')
                    ->label('Kategori')
                    ->options(KategoriMenu::all()->pluck('nama_kategori', 'id_kategori'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) =>
                        $set('nama_kategori', KategoriMenu::find($state)?->nama_kategori)
                    ),

                TextInput::make('nama_kategori')
                    ->label('Nama Kategori')
                    ->required()
                    ->disabled()
                    ->placeholder('Akan terisi otomatis'),

                TextInput::make('stok')
                    ->label('Stok')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('Masukkan stok menu'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_menu')->searchable(),
                TextColumn::make('nama_menu')->label('Nama Menu')->searchable()->sortable(),
                TextColumn::make('harga')
                    ->label('Harga')
                    ->formatStateUsing(fn (string|int|null $state): string => rupiah($state))
                    ->extraAttributes(['class' => 'text-right'])
                    ->sortable(),
                ImageColumn::make('foto')->label('Foto'),
                TextColumn::make('id_kategori')->label('ID Kategori'),
                TextColumn::make('nama_kategori')->label('Nama Kategori'),
                TextColumn::make('stok')->label('Stok'),
            ])
            ->filters([
                // Tambahkan filter jika diperlukan
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
            // Tambahkan relasi jika ada
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
