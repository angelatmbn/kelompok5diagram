<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Presensi;

class TotalPresensiPegawaiChart extends ChartWidget
{
    protected static ?string $heading = 'Presensi Hadir Pegawai';

    protected function getData(): array
    {
        $data = Presensi::query()
            ->where('status', 'hadir') // 🔍 Hanya status "hadir"
            ->join('pegawaii', 'presensi.id_pegawai', '=', 'pegawaii.id')
            ->selectRaw('pegawaii.nama, COUNT(*) as total_hadir')
            ->groupBy('pegawaii.nama')
            ->orderByDesc('total_hadir')
            ->limit(10)
            ->get();

        if ($data->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Hadir',
                    'data' => $data->pluck('total_hadir')->toArray(),
                    'backgroundColor' => '#60a5fa', // biru lembut
                ],
            ],
            'labels' => $data->pluck('nama')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
