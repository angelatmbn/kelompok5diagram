<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelangganResource\Pages;
use App\Models\Pelanggan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Komponen Forms
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;

// Komponen Tables
use Filament\Tables\Columns\TextColumn;

// untuk model ke user
use App\Models\User;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';
    protected static ?string $navigationLabel = 'Pelanggan';
    protected static ?string $navigationGroup = 'Masterdata';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
                //direlasikan ke tabel user
                Select::make('user_id')
                    ->label('User Id')
                    ->relationship('user', 'email')
                    ->searchable() // Menambahkan fitur pencarian
                    ->preload() // Memuat opsi lebih awal untuk pengalaman yang lebih cepat
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $user = User::find($state);
                            $set('nama_pelanggan', $user->name);
                        }
                    }),

            TextInput::make('id_pelanggan')
                ->label('ID Pelanggan')
                ->default(fn () => Pelanggan::getCustomerID())
                ->readonly()
                ->required(),

            TextInput::make('nama_pelanggan')
                ->label('Nama')
                ->required(),

            TextInput::make('alamat')
                ->label('Alamat')
                ->required(),

            TextInput::make('telepon')
                ->label('Telepon')
                ->tel()
                ->required(),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->unique(ignoreRecord: true)
                ->required(),

            DatePicker::make('tanggal_lahir')
                ->label('Tanggal Lahir')
                ->required(),

            Select::make('jenis_kelamin')
                ->label('Jenis Kelamin')
                ->options([
                    'laki-laki' => 'Laki-laki',
                    'perempuan' => 'Perempuan',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id_pelanggan')->label('ID Pelanggan')->searchable(),
            TextColumn::make('nama_pelanggan')->label('Nama')->searchable(),
            TextColumn::make('alamat')->label('Alamat')->searchable(),
            TextColumn::make('telepon')->label('Telepon')->searchable(),
            TextColumn::make('email')->label('Email')->searchable(),
            TextColumn::make('tanggal_lahir')->label('Tanggal Lahir')->date(),
            TextColumn::make('jenis_kelamin')->label('Jenis Kelamin'),
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
            // Tambahkan relation manager jika ada
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPelanggans::route('/'),
            'create' => Pages\CreatePelanggan::route('/create'),
            'edit' => Pages\EditPelanggan::route('/{record}/edit'),
        ];
    }
}
