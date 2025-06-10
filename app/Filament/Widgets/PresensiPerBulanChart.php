<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Presensi;
use Carbon\Carbon;

class PresensiPerBulanChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return 'Grafik Presensi Pegawai ' . date('Y');
    }

    protected function getData(): array
    {
        $year = now()->year;

        // Ambil data presensi grup per bulan dan status
        $presensi = Presensi::query()
            ->whereYear('tanggal', $year)
            ->selectRaw('MONTH(tanggal) as month, status, COUNT(*) as total')
            ->groupBy('month', 'status')
            ->get();

        // Siapkan struktur awal
        $allMonths = collect(range(1, 12));
        $statuses = ['Hadir', 'Izin', 'Sakit', 'Alpha'];

        // Map setiap status untuk tiap bulan
        $dataPerStatus = [];
        foreach ($statuses as $status) {
            $dataPerStatus[$status] = $allMonths->map(function ($month) use ($presensi, $status) {
                return $presensi->firstWhere('month', $month)?->status === $status
                    ? $presensi->firstWhere('month', $month)?->total ?? 0
                    : $presensi->where('month', $month)->where('status', $status)->pluck('total')->sum();
            });
        }

        // Label bulan
        $labels = $allMonths->map(fn($month) =>
            Carbon::create()->month($month)->locale('id')->translatedFormat('F')
        );

        return [
            'datasets' => [
                [
                    'label' => 'Hadir',
                    'data' => $dataPerStatus['Hadir'],
                    'backgroundColor' => '#22c55e',
                    'borderColor' => '#22c55e',
                ],
                [
                    'label' => 'Izin',
                    'data' => $dataPerStatus['Izin'],
                    'backgroundColor' => '#facc15',
                    'borderColor' => '#facc15',
                ],
                [
                    'label' => 'Sakit',
                    'data' => $dataPerStatus['Sakit'],
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
                ],
                [
                    'label' => 'Alpha',
                    'data' => $dataPerStatus['Alpha'],
                    'backgroundColor' => '#ef4444',
                    'borderColor' => '#ef4444',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bisa diganti ke 'line' kalau mau garis
    }
}
