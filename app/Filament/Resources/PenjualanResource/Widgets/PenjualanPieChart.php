<?php

namespace App\Filament\Resources\PenjualanResource\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PenjualanPieChart extends ChartWidget
{
    protected static ?string $heading = 'Penjualan Per Item (Pie Chart) 2025';

    protected function getData(): array
    {
        // Get total quantity sold per menu item
        $data = DB::table('detail_penjualan')
            ->join('menu', 'detail_penjualan.menu_id', '=', 'menu.id_menu')
            ->select('menu.nama_menu', DB::raw('SUM(detail_penjualan.jumlah) as total'))
            ->groupBy('menu.nama_menu')
            ->pluck('total', 'menu.nama_menu');

        return [
            'datasets' => [
                [
                    'data' => $data->values(),
                    'backgroundColor' => [
                        '#e57373', // color 1
                        '#64b5f6', // color 2
                        '#81c784', // color 3
                        '#ffd54f', // add more if you have more items
                    ],
                ],
            ],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
