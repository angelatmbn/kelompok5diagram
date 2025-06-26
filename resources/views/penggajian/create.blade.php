<h2>Proses Penggajian</h2>
<form method="POST" action="{{ route('penggajian.store') }}">
    @csrf
    <label>Pegawai:</label>
    <select name="pegawai_id">
        @foreach($pegawai as $p)
        <option value="{{ $p->id }}">{{ $p->nama }}</option>
        @endforeach
    </select><br><br>

    <label>Tanggal Gajian:</label>
    <input type="date" name="tanggal"><br><br>

    <button type="submit">Proses Gaji</button>
</form>
