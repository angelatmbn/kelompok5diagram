<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penggajian;
use App\Models\Pegawaii;
use App\Models\Presensi;

class PenggajianController extends Controller
{
    public function generate(Request $request)
    {
        $pegawai = Pegawaii::findOrFail($request->id_pegawai);
        $tanggal = Carbon::parse($request->tanggal);

        $hariKerja = $tanggal->copy()->startOfMonth()->daysUntil($tanggal->copy()->endOfMonth())
            ->filter(fn ($date) => $date->isWeekday())->count();

        $hadir = Presensi::where('id_pegawai', $pegawai->id)
            ->whereBetween('tanggal', [$tanggal->startOfMonth(), $tanggal->endOfMonth()])
            ->where('status', 'hadir')->count();

        $persenHadir = $hadir / $hariKerja;
        $gajiPokok = $pegawai->gaji_pokok;
        $potongan = (int) ($gajiPokok * (1 - $persenHadir));
        $totalGaji = $gajiPokok - $potongan;

        $penggajian = Penggajian::create([
            'id_pegawai' => $pegawai->id,
            'tanggal' => $tanggal,
            'gaji_pokok' => $gajiPokok,
            'potongan' => $potongan,
            'total_gaji' => $totalGaji,
        ]);

        return response()->json($penggajian);
    }

}
