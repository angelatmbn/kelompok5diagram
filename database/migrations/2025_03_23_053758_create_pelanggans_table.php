<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Penjualan;

class TotalPenjualanPerPelangganChart extends ChartWidget
{
    protected static ?string $heading = 'Total Penjualan per Pelanggan';

    protected function getData(): array
    {
<<<<<<< HEAD
        $data = Penjualan::query()
            ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id')
            ->where('penjualan.status', 'bayar')
            ->selectRaw('pelanggan.nama as nama_pelanggan, SUM(penjualan.total_tagihan) as total_penjualan')
            ->groupBy('pelanggan.nama')
            ->orderByDesc('total_penjualan')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan',
                    'data' => $data->pluck('total_penjualan')->toArray(),
                    'backgroundColor' => '#fa9baf',
                ],
            ],
            'labels' => $data->pluck('nama_pelanggan')->toArray(),
        ];
=======
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('id_pelanggan')->unique();
            $table->string('nama');
            $table->string('alamat');
            $table->string('telepon')->nullable();
            $table->string('email')->unique()->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable();
            $table->timestamps();
});
       

>>>>>>> 519817204e2598416ec72975b2a8e9cff4710d33
    }

    protected function getType(): string
    {
        return 'bar'; // Atau 'pie', 'line', dll
    }
}
