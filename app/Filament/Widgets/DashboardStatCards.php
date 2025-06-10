<?php

namespace App\Filament\Widgets;

use App\Models\Presensi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatCards extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Hadir', Presensi::where('status', 'Hadir')->count())
                ->description('Total hadir')
                ->color('success')
                ->chart([7, 6, 9, 4, 10, 5, 8]),

            Stat::make('Izin', Presensi::where('status', 'Izin')->count())
                ->description('Total izin')
                ->color('warning')
                ->chart([1, 2, 1, 2, 1, 3, 2]),

            Stat::make('Sakit', Presensi::where('status', 'Sakit')->count())
                ->description('Total sakit')
                ->color('info')
                ->chart([2, 3, 2, 1, 2, 1, 2]),

            Stat::make('Alpha', Presensi::where('status', 'Alpha')->count())
                ->description('Tanpa keterangan')
                ->color('danger')
                ->chart([0, 1, 0, 1, 1, 0, 2]),

            Stat::make('Total Presensi', Presensi::count())
                ->description('Semua status')
                ->color('gray')
                ->chart([10, 12, 13, 15, 11, 10, 9]),
        ];
    }
}
