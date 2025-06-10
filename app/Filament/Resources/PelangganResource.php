<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelangganResource\Pages;
use App\Models\Pelanggan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';
    // merubah nama label menjadi Pelanggan
    protected static ?string $navigationLabel = 'Pelanggan';

    // tambahan buat grup masterdata
    protected static ?string $navigationGroup = 'Masterdata';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('user_id')
                ->label('User')
                ->relationship('user', 'email')
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $user = User::find($state);
                        if ($user) {
                            $set('nama', $user->name);
                        }
                    }
                }),
                
            TextInput::make('id')
                ->label('ID')
                ->disabled(),

            TextInput::make('id_pelanggan')
                ->default(fn () => Pelanggan::getCustomerID())
                ->label('Id Pelanggan')
                ->readonly()
                ->required(),

            TextInput::make('nama')
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
                ->unique(ignoreRecord: true) // agar tidak error saat edit
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
        return $table
            ->columns([
                TextColumn::make('id_pelanggan')->searchable(),
                TextColumn::make('nama')->searchable(),
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
        return [];
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
