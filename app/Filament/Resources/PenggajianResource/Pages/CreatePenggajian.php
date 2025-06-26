<?php

namespace App\Filament\Resources\PenggajianResource\Pages;

use App\Filament\Resources\PenggajianResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use App\Models\Pegawaii;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Actions\ButtonAction;

class CreatePenggajian extends CreateRecord
{
    protected static string $resource = PenggajianResource::class;

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make() // ✅ tambahkan Section untuk kontrol layout
                ->schema([
                    Forms\Components\Wizard::make([
                        Forms\Components\Wizard\Step::make('Data Pegawai')
                            ->schema([
                                Forms\Components\Select::make('id_pegawai')
                                    ->label('Pegawai')
                                    ->options(Pegawaii::all()->pluck('nama', 'id'))
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => $this->hitungGaji($state, $set)),
                                Forms\Components\DatePicker::make('tanggal')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => $this->hitungGaji($get('id_pegawai'), $set, $state)),
                            ]),

                        Forms\Components\Wizard\Step::make('Perhitungan')
                            ->schema([
                                Forms\Components\TextInput::make('gaji_pokok')
                                    ->label('Gaji Pokok')
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->required(),

                                Forms\Components\TextInput::make('potongan')
                                    ->label('Potongan')
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->required(),

                                Forms\Components\TextInput::make('total_gaji')
                                    ->label('Total Gaji')
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->required(),
                            ]),

                        Forms\Components\Wizard\Step::make('Pembayaran')
                            ->schema([
                                Forms\Components\Select::make('status_pembayaran')
                                    ->label('Status Pembayaran')
                                    ->options([
                                        'belum' => 'Belum Dibayar',
                                        'dibayar' => 'Dibayar',
                                    ])
                                    ->default('belum'),
                            ]),
                    ])
                    ->columns(1) // ✅ agar isinya satu kolom dan lebar
                ])
                ->columns(1)
                ->columnSpanFull() // ✅ ini yang bikin tampil full width
        ]);
}

    protected function hitungGaji($pegawaiId, callable $set, $tanggal = null): void
    {
        if (!$pegawaiId || !$tanggal) return;

        $pegawai = Pegawaii::find($pegawaiId);
        if (!$pegawai) return;

        $gajiPokok = $pegawai->gaji_pokok;

        $bulan = date('m', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));

        $hariKerja = 0;
        for ($day = 1; $day <= date('t', strtotime($tanggal)); $day++) {
            $tanggalIterasi = date("Y-m-d", strtotime("$tahun-$bulan-$day"));
            $hari = date('N', strtotime($tanggalIterasi));
            if ($hari >= 1 && $hari <= 5) {
                $hariKerja++;
            }
        }

        $hadir = \App\Models\Presensi::where('id_pegawai', $pegawaiId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'hadir')
            ->count();

        $persentase = $hariKerja > 0 ? ($hadir / $hariKerja) : 0;
        $potongan = round($gajiPokok * (1 - $persentase));
        $totalGaji = $gajiPokok - $potongan;

        $set('gaji_pokok', $gajiPokok);
        $set('potongan', $potongan);
        $set('total_gaji', $totalGaji);
    }
}
