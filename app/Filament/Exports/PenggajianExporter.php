<?php

namespace App\Filament\Exports;

use App\Models\Penggajian;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PenggajianExporter extends Exporter
{
    protected static ?string $model = Penggajian::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('pegawaii.nama')->label('Pegawai'),
            ExportColumn::make('tanggal')->label('Tanggal'),
            ExportColumn::make('gaji_pokok')->label('Gaji Pokok'),
            ExportColumn::make('potongan')->label('Potongan'),
            ExportColumn::make('total_gaji')->label('Total Gaji'),
            ExportColumn::make('status_pembayaran')->label('Status Pembayaran'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your penggajian export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
