<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Filament\Resources\PenjualanResource\RelationManagers;
use App\Models\Penjualan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Wizard; //untuk menggunakan wizard
use Filament\Forms\Components\TextInput; //untuk penggunaan text input
use Filament\Forms\Components\DateTimePicker; //untuk penggunaan date time picker
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select; //untuk penggunaan select
use Filament\Forms\Components\Repeater; //untuk penggunaan repeater
use Filament\Tables\Columns\TextColumn; //untuk tampilan tabel

use Filament\Forms\Components\Placeholder; //untuk menggunakan text holder
use Filament\Forms\Get; //menggunakan get
use Filament\Forms\Set; //menggunakan set
use Filament\Forms\Components\Hidden; //menggunakan hidden field
use Filament\Tables\Filters\SelectFilter; //untuk menambahkan filter

// model
use App\Models\Pembeli;
use App\Models\Barang;
use App\Models\Pembayaran;
use App\Models\PenjualanBarang;

// DB
use Illuminate\Support\Facades\DB;
// untuk dapat menggunakan action
// use Filament\Forms\Components\Actions\Action;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    // merubah nama label menjadi Pembeli
    protected static ?string $navigationLabel = 'Penjualan';

    // tambahan buat grup masterdata
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('pelanggan_id')
                    ->label('Pelanggan')
                    ->relationship('pelanggan', 'nama') // pastikan relasi sudah dibuat di model
                    ->required(),

                Hidden::make('no_faktur')
                    ->required()
                    ->default(function () {
                        $tanggal = now()->format('Ymd');
                        $prefix = 'INV-' . $tanggal . '-';

                        // Hitung jumlah transaksi hari ini
                        $count = \App\Models\Penjualan::whereDate('created_at', now())->count() + 1;

                        // Format jadi 4 digit, misal: 0001, 0010, dst
                        $nomor = str_pad($count, 4, '0', STR_PAD_LEFT);

                        return $prefix . $nomor;
                    }),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pesan' => 'Pesan',
                        'bayar' => 'Bayar',
                    ])
                    ->default('pesan')
                    ->required(),

                DateTimePicker::make('tgl')
                    ->label('Tanggal')
                    ->required(),

                TextInput::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_faktur')->label('No Faktur')->searchable(),
                TextColumn::make('pelanggan.nama')->label('Pelanggan')->searchable(),
                TextColumn::make('status')->label('Status'),
                TextColumn::make('tgl')->label('Tanggal')->dateTime(),
                TextColumn::make('total_tagihan')->label('Total')->money('IDR'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }
}
