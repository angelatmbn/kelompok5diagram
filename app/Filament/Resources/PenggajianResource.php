<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenggajianResource\Pages;
use App\Filament\Resources\PenggajianResource\RelationManagers;
use App\Models\Penggajian;
use App\Models\Pegawaii;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use App\Filament\Exports\PenggajianExporter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ExportBulkAction;

class PenggajianResource extends Resource
{
    protected static ?string $model = Penggajian::class;

     protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function hitungGaji($pegawaiId, callable $set, $tanggal = null): void
    {
        if (!$pegawaiId || !$tanggal) {
        return;
    }

    $pegawai = \App\Models\Pegawaii::find($pegawaiId);
    if (!$pegawai) return;

    $gajiPokok = $pegawai->gaji_pokok;

    $bulan = date('m', strtotime($tanggal));
    $tahun = date('Y', strtotime($tanggal));

    // Hitung jumlah hari kerja (Senin - Jumat) dalam bulan itu
    $hariKerja = 0;
    for ($day = 1; $day <= date('t', strtotime($tanggal)); $day++) {
        $tanggalIterasi = date("Y-m-d", strtotime("$tahun-$bulan-$day"));
        $hari = date('N', strtotime($tanggalIterasi));
        if ($hari >= 1 && $hari <= 5) {
            $hariKerja++;
        }
    }

    // Hitung jumlah kehadiran pegawai dari tabel presensi
    $hadir = \App\Models\Presensi::where('id_pegawai', $pegawaiId)
        ->whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->where('status', 'hadir')
        ->count();

    // Hitung potongan dan total
    $persentase = $hariKerja > 0 ? ($hadir / $hariKerja) : 0;
    $potongan = round($gajiPokok * (1 - $persentase));
    $totalGaji = $gajiPokok - $potongan;

    $set('gaji_pokok', $gajiPokok);
    $set('potongan', $potongan);
    $set('total_gaji', $totalGaji);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pegawaii.nama')->label('Pegawai'),
                Tables\Columns\TextColumn::make('tanggal')->date(),
                Tables\Columns\TextColumn::make('gaji_pokok')->money('IDR', true),
                Tables\Columns\TextColumn::make('potongan')->money('IDR', true),
                Tables\Columns\TextColumn::make('total_gaji')->money('IDR', true),
                Tables\Columns\BadgeColumn::make('status_pembayaran'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                // tombol tambahan export csv dan excel
                ExportAction::make()->exporter(PenggajianExporter::class)->color('success'),
                // Tombol Unduh PDF yang sudah diperbaiki
                // Action::make('downloadPdf')
                // ->label('Unduh PDF')
                // ->icon('heroicon-o-document-arrow-down')
                // ->color('success')
                // ->action(function () {
                //     $users = User::all();

                //     $pdf = Pdf::loadView('pdf.users', ['users' => $users]);

                //     return response()->streamDownload(
                //         fn () => print($pdf->output()),
                //         'user-list.pdf'
                //     );
                // })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()->exporter(UserExporter::class)
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
            'index' => Pages\ListPenggajians::route('/'),
            'create' => Pages\CreatePenggajian::route('/create'),
            'edit' => Pages\EditPenggajian::route('/{record}/edit'),
        ];
    }
}
