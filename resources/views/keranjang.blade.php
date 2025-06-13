@extends('layout')

@section('konten')
<body style="background-color: #FAF7F0; color: #222; font-family: 'Inter', sans-serif;">

<meta name="csrf-token" content="{{ csrf_token() }}">

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
  h3 {
    font-weight: 700;
    font-size: 2.5rem;
    color: #b2afa5;
    margin-bottom: 1.5rem;
  }

  .btn-primary {
    background-color:rgb(41, 37, 30);
    border: none;
    border-radius: 9999px;
    padding: 0.375rem 1.25rem;
    font-weight: 600;
    transition: background-color 0.3s ease;
    color: #fff;
  }

  .card {
    background: linear-gradient(to top, #b2afa5, #332424);
    border-radius: 0.75rem;
    color:rgb(156, 151, 136);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    padding: 1rem;
  }

  .card img {
    border-radius: 0.5rem;
    object-fit: cover;
    height: 180px;
    width: 100%;
  }

  .card-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin-top: 0.75rem;
    color: #e6e3da;
  }

  .qty, .price {
    font-size: 0.95rem;
    color: #dad2c4;
    margin: 0.25rem 0;
  }

  .btn-outline-dark {
    border-color: #dad2c4;
    color:  #dad2c4;
  }

  .btn-outline-dark:hover {
    background-color:rgb(217, 213, 199);
    color:rgb(155, 153, 143);
  }

  .list-group-item {
    background-color: #fdfbf7;
    border: 1px solid #ccc;
    font-weight: 600;
    color: #222;
  }

  .btn-lg {
    border-radius: 9999px;
    font-size: 1.1rem;
    font-weight: 600;
  }

.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active {
  background-color: rgb(66, 60, 50) !important; /* warna gelap sedikit berbeda */
  color: #fff !important;
  border: none;
  box-shadow: none;
}

.btn-lg:hover,
.btn-lg:focus,
.btn-lg:active {
  background-color: rgb(66, 60, 50) !important; /* konsisten dengan btn-primary */
  color: #fff !important;
  border: none;
  box-shadow: none;
}

</style>

<header class="py-3 border-bottom" style="background-color:rgb(41, 37, 30);">
  <div class="container d-flex justify-content-between align-items-center">
    <a href="/depan">
  <img src="{{ asset('images/logos/diagram.PNG') }}" alt="Cafe Diagram Logo" class="img-fluid" style="max-height: 48px;">
</a>
    <div class="d-flex gap-2">
              <a href="/depan" class="btn btn-primary px-4 py-2"><span class="nowrap">Lihat Galeri</span></a>
              <a href="/logout" class="btn btn-primary px-4 py-2"><span class="nowrap">Keluar</span></a>
    </div>
  </div>
</header>

<section class="py-5">
  <div class="container" style="max-width: 1200px;">
    <h3 class="text-center">Keranjang Anda</h3>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
      @forelse($menu as $p)
      <div class="col">
        <div class="card h-100">
          <img src="{{ $p['foto'] }}" alt="{{ $p['nama'] }}">
          <h5 class="card-title">{{ $p['nama'] }}</h5>
          <p class="qty">Jumlah: {{ $p['quantity'] }} Unit</p>
          <p class="price"><strong>Total: {{ rupiah($p['harga'] * $p['quantity']) }}</strong></p>
          <button class="btn btn-outline-dark btn-sm w-100 mt-2" onclick="hapus({{ $p['id'] }})">Hapus</button>
        </div>
      </div>
      @empty
      <div class="col-12">
        <div class="alert alert-warning text-center">Keranjang kosong.</div>
      </div>
      @endforelse
    </div>

    <ul class="list-group my-4">
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>Total Tagihan</span>
        <strong>{{ rupiah($total_tagihan) }}</strong>
      </li>
    </ul>

    @if($total_tagihan > 0)
    <button id="pay-button" class="btn btn-primary btn-lg w-100 mt-2">Bayar Sekarang</button>
    @endif
  </div>
</section>

<!-- MIDTRANS SNAP SCRIPT -->
<script type="text/javascript"
  src="https://app.sandbox.midtrans.com/snap/snap.js"
  data-client-key="SB-Mid-client-9Y2AxMjo2exYcxMn"></script>

<script>
  const payBtn = document.getElementById('pay-button');
  if (payBtn) {
    payBtn.addEventListener('click', function () {
      window.snap.pay('{{ $snap_token }}', {
        onSuccess: function(result){
          // Hapus session server
          fetch('/keranjang/clear', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          .then(() => {
          // Redirect ke endpoint Laravel yang akan hapus session dan beri sinyal ke galeri
          window.location.href = "/bayar/sukses";
        });
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
  }

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