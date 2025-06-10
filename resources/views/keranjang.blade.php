@extends('layout')

@section('konten')
<body style="background-color: #FAF7F0; color: #222222; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;">

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- SweetAlert jika sukses -->
@if(session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Berhasil!",
                text: "{{ session('success') }}",
                icon: "success",
                timer: 3000,
                showConfirmButton: false,
                background: '#fff9f0',
                color: '#222',
                iconColor: '#2d372d'
            });
        });
    </script>
@endif

<style>
  h3 { font-weight: 700; font-size: 2.75rem; color: #1a1a1a; margin-bottom: 1.5rem; }
  .product-item { background-color: #fff; border-radius: 0.75rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 1rem; }
  .product-item img { border-radius: 0.5rem; }
  .product-item h3 { font-size: 1.125rem; margin-top: 0.75rem; color: #222; }
  .qty, .price { color: #555; font-size: 0.9rem; }
  .list-group-item h6 { margin: 0; font-weight: 600; }
</style>

<header class="py-3 border-bottom bg-light">
  <div class="container d-flex justify-content-between align-items-center">
    <a href="/depan"><img src="{{ asset('foto/diagram.jpg') }}" alt="logo" height="48"></a>
    <div class="d-flex gap-2">
      <a href="/lihatkeranjang" class="btn btn-primary btn-sm">Lihat Keranjang</a>
      <a href="/depan" class="btn btn-dark btn-sm">Lihat Galeri</a>
      <a href="/lihatriwayat" class="btn btn-info btn-sm">Riwayat</a>
      <a href="/logout" class="btn btn-danger btn-sm">Keluar</a>
    </div>
  </div>
</header>

<section class="py-5">
  <div class="container">
    <h3>Keranjang Anda</h3>
    <div class="row g-4">
      @forelse($menu as $p)
      <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="product-item">
          <img src="{{ Storage::url($p->foto) }}" alt="{{ $p->nama_menu }}" class="img-fluid">
          <h3>{{ $p->nama_menu }}</h3>
          <div class="qty">Jumlah: {{ $p->total_menu }} Unit</div>
          <div class="price"><b>Total: {{ rupiah($p->total_belanja) }}</b></div>
          <button class="btn btn-danger btn-sm w-100 mt-2" onclick="hapus({{ $p->menu_id }})">Hapus</button>
        </div>
      </div>
      @empty
      <div class="col-12">
        <div class="alert alert-warning text-center">Keranjang kosong.</div>
      </div>
      @endforelse
    </div>

    <ul class="list-group my-4">
      <li class="list-group-item d-flex justify-content-between">
        <h6>Total</h6>
        <strong>{{ rupiah($total_tagihan) }}</strong>
      </li>
    </ul>

    <button id="pay-button" class="btn btn-primary btn-lg w-100">Bayar</button>
  </div>
</section>

<script>
  const payBtn = document.getElementById('pay-button');
  payBtn.addEventListener('click', function () {
    window.snap.pay('{{ $snap_token }}', {
      onSuccess: function(result){
        Swal.fire({ icon: 'success', title: 'Pembayaran Berhasil', showConfirmButton: false, timer: 2000 });
        window.location.href = "/depan";
      },
      onPending: function(result){
        Swal.fire({ icon: 'info', title: 'Pembayaran Tertunda' });
        window.location.href = "/depan";
      },
      onError: function(result){
        Swal.fire({ icon: 'error', title: 'Pembayaran Gagal' });
        window.location.href = "/depan";
      },
      onClose: function(){
        alert("Transaksi dibatalkan.");
      }
    });
  });

  function hapus(id) {
    fetch('/hapus/' + id, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
    })
    .then(res => res.json())
    .then(data => {
      if(data.success){
        Swal.fire({ icon: 'success', title: 'Berhasil Dihapus', timer: 1500 });
        location.reload();
      }
    });
  }
</script>

@endsection
