<?php 
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Penjualan;

class TotalPenjualanPerPelangganChart extends ChartWidget
{
    protected static ?string $heading = 'Total Penjualan per Pelanggan';

    protected function getData(): array
    {
        // Ambil total penjualan per Pelanggan
        $data = Penjualan::query()
            ->join('detail_penjualan', 'detail_penjualan.id', '=', 'detail_penjualan.penjualan_id')
            ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id') // Sesuaikan relasi
            // ->where('penjualan.status', 'bayar')
            ->selectRaw('pelanggan.nama, SUM(penjualan.total_tagihan) as total_penjualan')
            ->groupBy('pelanggan.nama')
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
                    'label' => 'Total Penjualan',
                    'data' => $data->pluck('total_penjualan')->toArray(),
                    'backgroundColor' => '#fa9baf',
                ],
            ],
            'labels' => $data->pluck('nama')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bisa diganti jadi 'pie' kalau ingin
    }
}