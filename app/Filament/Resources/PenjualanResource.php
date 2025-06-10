<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Models\Penjualan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// model
use App\Models\Pembeli;
use App\Models\Barang;
use App\Models\Pembayaran;
use App\Models\PenjualanBarang;

// DB
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Filters\SelectFilter;

use App\Models\Pelanggan;
use App\Models\Menu;
use App\Models\DetailPenjualan;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    // merubah nama label menjadi Pembeli
    protected static ?string $navigationLabel = 'Penjualan';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Pesanan')
                        ->schema([
                            Forms\Components\Section::make('Faktur')
                                ->icon('heroicon-m-document-duplicate')
                                ->schema([
                                    TextInput::make('no_faktur')
                                        ->default(fn() => Penjualan::getKodeFaktur())
                                        ->label('Nomor Faktur')
                                        ->required()
                                        ->readOnly(),
                                        
                                    DateTimePicker::make('tgl')
                                        ->default(now())
                                        ->required(),
                                        
                                    Select::make('pelanggan_id')
                                        ->label('Pelanggan')
                                        ->options(Pelanggan::pluck('nama', 'id')->toArray())
                                        ->required()
                                        ->placeholder('Pilih Pelanggan'),
                                        
                                    Hidden::make('status')
                                        ->default('pesan'),
                                        
                                    Hidden::make('total_tagihan')
                                        ->default(0),
                                ])
                                ->collapsible()
                                ->columns(3),
                        ]),
                        
                    Wizard\Step::make('Pilih Menu')
                        ->schema([
                            Repeater::make('detailPenjualan')
                                ->relationship()
                                ->schema([
                                    Select::make('menu_id')
                                        ->label('Menu')
                                        ->options(Menu::pluck('nama_menu', 'id_menu')->toArray())
                                        ->required()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->reactive()
                                        ->placeholder('Pilih Menu')
                                        ->searchable()
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            $menu = Menu::find($state);
                                            $set('harga_satuan', $menu ? $menu->harga : 0);
                                            $set('subtotal', $menu ? $menu->harga : 0);
                                        }),
                                        
                                    TextInput::make('harga_satuan')
                                        ->label('Harga Satuan')
                                        ->numeric()
                                        ->readOnly()
                                        ->hidden()
                                        ->dehydrated(),
                                        
                                    TextInput::make('subtotal')
                                        ->label('Subtotal')
                                        ->numeric()
                                        ->readOnly()
                                        ->dehydrated(),
                                        
                                    TextInput::make('jumlah')
                                        ->label('Jumlah')
                                        ->default(1)
                                        ->reactive()
                                        ->live()
                                        ->required()
                                        ->numeric()
                                        ->minValue(1)
                                        ->helperText('Minimal jumlah lebih dari 0')
                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                            $harga = $get('harga_satuan') ?? 0;
                                            $set('subtotal', $harga * $state);
                                            
                                            // Calculate total tagihan
                                            $totalTagihan = collect($get('../../detailPenjualan'))
                                                ->sum(fn($item) => ($item['subtotal'] ?? 0) * ($item['jumlah'] ?? 0));
                                            $set('../../total_tagihan', $totalTagihan);
                                        }),
                                ])
                                ->columns(4)
                                ->addActionLabel('Tambah Item')
                                ->minItems(1)
                                ->required(),
                                
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('Simpan')
                                    ->action(function (Get $get, Set $set) {
                                        $penjualan = Penjualan::updateOrCreate(
                                            ['no_faktur' => $get('no_faktur')],
                                            [
                                                'tgl' => $get('tgl'),
                                                'pelanggan_id' => $get('pelanggan_id'),
                                                'status' => 'pesan',
                                                'total_tagihan' => $get('total_tagihan') ?? 0
                                            ]
                                        );
                                        
                                        // Save detail items
                                        foreach ($get('detailPenjualan') as $item) {
                                            DetailPenjualan::updateOrCreate(
                                                [
                                                    'penjualan_id' => $penjualan->id,
                                                    'menu_id' => $item['menu_id'],
                                                ],
                                                [
                                                    'harga_satuan' => $item['harga_satuan'],
                                                    'subtotal' => $item['subtotal'],
                                                    'jumlah' => $item['jumlah'],
                                                ]
                                            );
                                            
                                            // Update menu stock
                                            $menu = Menu::find($item['menu_id']);
                                            if ($menu) {
                                                $menu->decrement('stok', $item['jumlah']);
                                            }
                                        }
                                        
                                        Notification::make()
                                            ->title('Penjualan berhasil disimpan')
                                            ->success()
                                            ->send();
                                    })
                                    ->label('Simpan Penjualan')
                                    ->color('primary'),
                            ]),
                        ]),
                        
                    Wizard\Step::make('Pembayaran')
                        ->schema([
                            Placeholder::make('Tabel Pembayaran')
                                ->content(fn(Get $get) => view('filament.components.penjualan-table', [
                                    'penjualan' => Penjualan::where('no_faktur', $get('no_faktur'))->first()
                                ])),
                        ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_faktur')
                    ->label('No Faktur')
                    ->searchable(),
                    
                TextColumn::make('pelanggan.nama')
                    ->label('Nama Pelanggan')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'bayar' => 'success',
                        'pesan' => 'warning',
                        default => 'gray',
                    }),
                    
                TextColumn::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->numeric()
                    ->sortable()
                    ->money('IDR'),
                    
                TextColumn::make('tgl')
                    ->label('Tanggal')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pesan' => 'Pemesanan',
                        'bayar' => 'Pembayaran',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }
}