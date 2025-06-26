<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Presensi;

class PDFController extends Controller
{
    // ... method contohpdf() di atas tetap ada

    /**
     * Generate PDF untuk daftar presensi
     */
    public function presensiPdf()
    {
        // Ambil semua data presensi beserta relasi pegawai
        $presensi = Presensi::with('pegawai')->get();

        // Load view blade 'pdf.presensi' dan kirimkan data $presensi
        $pdf = Pdf::loadView('pdf.presensi', ['presensi' => $presensi]);

        // Download dengan nama file presensi-{tanggal}.pdf
        $filename = 'presensi-' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }
}
