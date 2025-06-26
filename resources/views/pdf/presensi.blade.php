<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Presensi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>Daftar Presensi</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Pegawai</th>
                <th>Tanggal</th>
                <th class="text-center">Jam Masuk</th>
                <th class="text-center">Jam Keluar</th>
                <th class="text-center">Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($presensi as $p)
            <tr>
                <td>{{ optional($p->pegawaii)->nama }}</td>
                <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') }}</td>
                <td class="text-center">
                    {{ $p->jam_keluar 
                        ? \Carbon\Carbon::parse($p->jam_keluar)->format('H:i') 
                        : '-' 
                    }}
                </td>
                <td class="text-center">{{ $p->status }}</td>
                <td>{{ $p->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
