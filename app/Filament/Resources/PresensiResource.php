<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PresensiResource\Pages;
use App\Models\Presensi;
use App\Models\Pegawaii;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

// tambahan untuk tombol unduh pdf
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf; // Kalau kamu pakai DomPDF
use Illuminate\Support\Facades\Storage;

class PresensiResource extends Resource
{
    protected static ?string $model = Presensi::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Presensi';
    protected static ?string $navigationGroup = 'Kepegawaian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Presensi')
                    ->tabs([
                        Tabs\Tab::make('Data Utama')
                            ->schema([
                                Select::make('id_pegawai')
                                    ->label('Pegawai')
                                    ->options(Pegawaii::pluck('nama', 'id')->toArray())
                                    ->required(),
                                DatePicker::make('tanggal')
                                    ->label('Tanggal Presensi')
                                    ->default(now())
                                    ->required(),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'Hadir' => 'Hadir',
                                        'Izin'  => 'Izin',
                                        'Sakit' => 'Sakit',
                                        'Alpha' => 'Alpha',
                                    ])
                                    ->default('Hadir')
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Jam Masuk')
                            ->schema([
                                TimePicker::make('jam_masuk')
                                    ->label('Jam Masuk')
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Jam Keluar')
                            ->schema([
                                TimePicker::make('jam_keluar')
                                    ->label('Jam Keluar')
                                    ->nullable(),
                            ]),
                        Tabs\Tab::make('Keterangan')
                            ->schema([
                                Forms\Components\Textarea::make('keterangan')
                                    ->label('Keterangan')
                                    ->nullable(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pegawaii.nama')->label('Nama Pegawai')->sortable()->searchable(),
                TextColumn::make('tanggal')->label('Tanggal')->date()->sortable(),
                TextColumn::make('jam_masuk')->label('Masuk')->sortable(),
                TextColumn::make('jam_keluar')->label('Keluar')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Hadir' => 'success',
                        'Izin'  => 'warning',
                        'Sakit' => 'info',
                        'Alpha' => 'danger',
                    }),
                TextColumn::make('created_at')->label('Dibuat')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Izin'  => 'Izin',
                        'Sakit' => 'Sakit',
                        'Alpha' => 'Alpha',
                    ]),
            ])
                    // tombol tambahan
            ->headerActions([
                // tombol tambahan export pdf
                // ✅ Tombol Unduh PDF
                Action::make('downloadPdf')
                ->label('Unduh PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $presensi = Presensi::all();

                    $pdf = Pdf::loadView('pdf.presensi', ['presensi' => $presensi]);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'pegawai-list.pdf'
                    );
                })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPresensis::route('/'),
            'create' => Pages\CreatePresensi::route('/create'),
            'edit'   => Pages\EditPresensi::route('/{record}/edit'),
        ];
    }
}
