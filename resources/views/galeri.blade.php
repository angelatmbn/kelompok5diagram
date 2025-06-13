@extends('layout')

@section('konten')
<body style="background-color: #FAF7F0; color: #222222; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
  Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;">

<meta name="csrf-token" content="{{ csrf_token() }}">

@if(session('clear_client_cart'))
<script>
    // Bersihkan sessionStorage dari sisi client
    sessionStorage.clear();
    window._shouldUpdateCartUI = true;
</script>
@endif

@if(session('should_autorefresh'))
<script>
    // Auto refresh sekali saja untuk menyegarkan cart
    if (!sessionStorage.getItem('cart_refreshed')) {
        console.log('🔄 Auto-refresh karena pembayaran sukses...');
        sessionStorage.setItem('cart_refreshed', '1');
        location.reload();
    } else {
        sessionStorage.removeItem('cart_refreshed'); // supaya tidak loop terus
    }
</script>
@endif

<!-- Optional overlay saat reload -->
<div id="refresh-overlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:#faf7f0;z-index:9999;justify-content:center;align-items:center;">
  <h3 style="color:#222">Menyegarkan keranjang...</h3>
</div>

<script>
if (!sessionStorage.getItem('cart_refreshed') && {{ session('should_autorefresh') ? 'true' : 'false' }}) {
    document.getElementById('refresh-overlay').style.display = 'flex';
}
</script>

<!-- Tambahan Sweet Alert -->
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
<!-- Akhir Tambahan Sweet Alert -->

<style>
  /* Typography */
  h3 {
    font-weight: 700;
    font-size: 2.75rem;
    letter-spacing: -0.02em;
    margin-bottom: 1.5rem;
    color:rgb(178, 175, 165);
  }
  .card-title {
    font-weight: 600;
    font-size: 1.125rem;
    color:rgb(178, 175, 165);
  }
  p.card-text {
    color:rgb(178, 175, 165);
  }
  h6 {
    color:rgb(226, 223, 215);
  }

  /* Cards */
  .card {
    background: linear-gradient(to top,rgb(78, 69, 69),rgb(51, 36, 36));
    border-radius: 0.75rem;
    color: #222222;
    box-shadow: 0 4px 12px rgb(0 0 0 / 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 24px rgb(0 0 0 / 0.10);
  }

  /* Buttons */
  .btn-primary {
    background-color: #222222;
    border: none;
    border-radius: 9999px;
    padding: 0.375rem 1.25rem;
    font-weight: 600;
    transition: background-color 0.3s ease;
    color: #fff;
  }
  .btn-primary:hover, .btn-primary:focus {
    background-color: #444444;
    color: #fff;
  }
  .btn-outline-dark {
    border-color: #444;
    color: #444;
  }
  .btn-outline-dark:hover {
    border-color: #222;
    color: #222;
    background-color: #e0dcdc;
  }
  .btn-number {
    border-radius: 0.5rem;
    border-width: 1.5px;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: bold;
  }

  /* Input qty */
  input.form-control {
    background-color: #fdfbf7;
    border: 1.5px solid #ccc;
    border-radius: 0.5rem;
    color: #222;
    font-weight: 600;
  }
  input.form-control:focus {
    border-color: #222222;
    box-shadow: none;
    outline: none;
  }

  /* Product grid spacing */
  .product-grid > .col {
    margin-bottom: 2rem;
  }

  /* Header adjustments */
  header {
    background-color:rgb(28, 26, 22);
    border-bottom: 1px solid #e0dcd6;
  }
  .search-bar {
    background-color: #f7f5f0 !important;
  }
  .form-select, .form-control {
    color: #222222 !important;
    background-color: #fff !important;
  }
  .form-select option {
    color: #222;
  }

  /* Cart and User icons background */
  .rounded-circle.bg-light {
    background-color: #eae6dc !important;
  }

  /* Badge */
  .badge.bg-primary {
    background-color: #c9bca8;
    color: #4a422b;
  }
  .cart-badge {
  font-size: 0.75rem;
  padding: 5px 8px;
  top: 0;
  right: -10px;
  background-color: #c9bca8;
  color: #4a422b;
}

.cart-total {
  font-size: 1.25rem;
  font-weight: bold;
  color:rgb(178, 175, 165);
  white-space: nowrap;
}

.btn-sm {
  padding: 6px 16px;
  font-size: 0.9rem;
  border-radius: 9999px;
  font-weight: 600;
}
.button-bar .btn {
  white-space: nowrap;
  text-align: center;
  min-width: 180px;
  border-radius: 9999px;
  font-weight: 600;
  font-size: 1rem;
}

.nowrap {
  white-space: nowrap;
}

</style>

<header>
  <div class="container-fluid">
    <div class="row py-3 align-items-center border-bottom">
      
      <!-- Logo -->
      <div class="col-lg-3 text-center text-lg-start">
        <a href="/" aria-label="Homepage">
          <img src="{{ asset('images/logos/diagram.PNG') }}" alt="Cafe Diagram Logo" class="img-fluid" style="max-height: 48px;">
        </a>
      </div>

      <!-- Search Bar -->
      <div class="col-lg-6">
        <div class="search-bar row p-2 rounded-4 align-items-center">
          <div class="col-md-4 d-none d-md-block">
            <select class="form-select border-0 bg-white">
              <option>All Categories</option>
              <option>Groceries</option>
              <option>Drinks</option>
              <option>Chocolates</option>
            </select>
          </div>
          <div class="col-11 col-md-7">
            <form id="search-form" class="text-center" action="index.html" method="post">
              <input type="text" class="form-control border-0 bg-white" placeholder="Search for more than 20,000 products" aria-label="Search products" />
            </form>
          </div>
          <div class="col-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#4a422b" viewBox="0 0 24 24"><path d="M21.71 20.29L18 16.61A9 9 0 1 0 16.61 18l3.68 3.68a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.39ZM11 18a7 7 0 1 1 7-7a7 7 0 0 1-7 7Z"/></svg>
          </div>
        </div>

        <!-- Tombol Aksi di Tengah -->
        <div class="mt-3">
          <div class="d-flex justify-content-center">
            <div class="button-bar d-flex gap-3 flex-wrap justify-content-center">
              <button class="btn btn-primary px-4 py-2" onclick="window.location.href='/lihatkeranjang'"><span class="nowrap">Lihat Keranjang</span></button>
              <a href="/depan" class="btn btn-primary px-4 py-2"><span class="nowrap">Lihat Galeri</span></a>
              <a href="/logout" class="btn btn-primary px-4 py-2"><span class="nowrap">Keluar</span></a>
            </div>
          </div>
        </div>
      </div>

      <!-- Cart di ujung kanan -->
      <div class="col-lg-3 text-end">
        <div class="position-relative d-inline-block">
          <button class="border-0 bg-transparent d-flex flex-column align-items-end" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCart" aria-controls="offcanvasCart">
            <span class="fs-5 fw-bold" style="color:rgb(178, 175, 165)">Your Cart</span>
            <span class="cart-total fs-5 fw-bold" style="color:rgb(178, 175, 165)">Rp 0</span>
          </button>
          <span class="cart-badge position-absolute badge rounded-pill" style="top: -10px; right: -10px; display: none;">0</span>
        </div>
      </div>
    </div>
  </div>
</header>

<section class="py-5">
  <div class="container" style="max-width: 1200px;">

    <div class="row">
      <div class="col-md-12">

        <div class="bootstrap-tabs product-tabs">
          <div class="tabs-header d-flex justify-content-between border-bottom my-5" style="border-color: #e0dcd6 !important;">
            <h3>Produk Terbaru</h3>
          </div>
          <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-all" role="tabpanel" aria-labelledby="nav-all-tab">
             <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
                @foreach($menu as $p)
                <div class="col">
                  <div class="card h-100 shadow border-0" data-product-id="{{ $p->id_menu }}">
                    <div class="position-relative">
                      <a href="{{ Storage::url($p->foto) }}" class="d-block" aria-label="View {{ $p->nama_menu }}">
                        <img src="{{ Storage::url($p->foto) }}" class="card-img-top rounded-top" alt="{{ $p->nama_menu }}" style="object-fit: cover; height: 200px;">
                      </a>
                      <a href="#" class="btn btn-light position-absolute top-0 end-0 m-2 rounded-circle shadow-sm" aria-label="Add to Favorites">
                        <svg width="20" height="20" fill="#c9bca8" viewBox="0 0 24 24">
                          <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                      </a>
                    </div>
                    <div class="card-body">
                      <h5 class="card-title fw-semibold">{{ $p->nama_menu }}</h5>
                      <p class="card-text small">Stok: {{ $p->stok }}</p>
                      <h6 class="fw-bold mb-3 product-price" data-price="{{ $p->harga }}">{{ rupiah($p->harga) }}</h6>
                      <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center justify-content-between">
  <div class="input-group input-group-sm" style="width: 110px;">
    <!-- Tombol Minus -->
    <button type="button" class="btn btn-outline-dark btn-number btn-minus" data-product-id="{{ $p->id_menu }}">
      <span>-</span>
    </button>

    <!-- Input Quantity -->
    <input type="text" id="quantity-{{ $p->id_menu }}" name="quantity" 
           class="form-control text-center quantity-input" 
           value="1" 
           style="color: #4a422b; background-color: #f5f5e8; border-color: #e0dcd6;">

    <!-- Tombol Plus -->
    <button type="button" 
            class="btn btn-outline-dark btn-number btn-plus" 
            data-product-id="{{ $p->id_menu }}" 
            data-stok="{{ $p->stok }}">
      <span>+</span>
    </button>
  </div>

  <!-- Tombol Tambah ke Keranjang -->
  <button type="button" class="btn btn-dark btn-sm rounded-pill add-to-cart" data-product-id="{{ $p->id_menu }}">
    <svg width="20" height="20" fill="#c9bca8" viewBox="0 0 24 24">
      <path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1zm16 16c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
    </svg> 
    Tambah
  </button>
</div>

                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
              <!-- / product-grid -->
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Cart system starting...');

    // Ambil cart dari sessionStorage
    function getCart() {
        return JSON.parse(sessionStorage.getItem('cart') || '[]');
    }

    // Format angka ke format Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Update tampilan cart UI
    function updateCartUI() {
        let cart = getCart();
        let total = 0;
        let itemCount = 0;

        cart.forEach(item => {
            total += item.harga * item.quantity;
            itemCount += item.quantity;
        });

        const cartTotal = document.querySelector('.cart-total');
        if (cartTotal) cartTotal.textContent = formatRupiah(total);

        const badge = document.querySelector('.cart-badge');
        if (badge) {
            badge.textContent = itemCount;
            badge.style.display = itemCount > 0 ? 'inline-block' : 'none';
        }

        console.log('📊 Cart updated:', { total, itemCount, items: cart.length });
    }

    // Simpan cart ke sessionStorage
    function saveCart(cart) {
        sessionStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();
    }

    // Notifikasi
    function notify(message, type = 'success') {
        if (window.Swal) {
            Swal.fire({
                title: type === 'success' ? 'Berhasil!' : 'Error!',
                text: message,
                icon: type,
                timer: 2000,
                showConfirmButton: false,
                background: '#fff9f0',
                color: '#222'
            });
        } else {
            alert(message);
        }
    }

    // Event handler tombol plus, minus, dan add to cart
    document.body.addEventListener('click', function(e) {
        const target = e.target;

        // PLUS
        if (target.classList.contains('btn-plus') || target.closest('.btn-plus')) {
            e.preventDefault();
            const btn = target.closest('.btn-plus');
            const productId = btn.dataset.productId;
            const stok = parseInt(btn.dataset.stok);
            const card = btn.closest('.card');
            const input = card.querySelector(`#quantity-${productId}`);
            if (input) {
                let currentValue = parseInt(input.value);
                if (currentValue < stok) {
                    input.value = currentValue + 1;
                }
            }
            return;
        }

        // MINUS
        if (target.classList.contains('btn-minus') || target.closest('.btn-minus')) {
            e.preventDefault();
            const btn = target.closest('.btn-minus');
            const productId = btn.dataset.productId;
            const card = btn.closest('.card');
            const input = card.querySelector(`#quantity-${productId}`);
            if (input) {
                let currentValue = parseInt(input.value);
                if (currentValue > 1) {
                    input.value = currentValue - 1;
                }
            }
            return;
        }

        // ADD TO CART
        if (target.classList.contains('add-to-cart') || target.closest('.add-to-cart')) {
            e.preventDefault();
            const btn = target.classList.contains('add-to-cart') ? target : target.closest('.add-to-cart');
            const productId = btn.dataset.productId;
            const card = btn.closest('.card');

            if (!card) return;

            try {
                const nameEl = card.querySelector('.card-title');
                const priceEl = card.querySelector('.product-price');
                const qtyInput = document.getElementById(`quantity-${productId}`);
                if (!nameEl || !priceEl || !qtyInput) return;

                const productName = nameEl.textContent.trim();
                const productPrice = parseInt(priceEl.dataset.price);
                const quantity = parseInt(qtyInput.value) || 1;

                // ✅ Ambil gambar dari dalam card
                const imageEl = card.querySelector('img');
                const productImage = imageEl ? imageEl.getAttribute('src') : '';

                let cart = getCart();
                const existingIndex = cart.findIndex(item => item.id === productId);

                if (existingIndex >= 0) {
                    cart[existingIndex].quantity += quantity;
                } else {
                    cart.push({
                        id: productId,
                        nama: productName,
                        harga: productPrice,
                        quantity: quantity,
                        type: 'produk',
                        foto: productImage // ← ini sekarang aman digunakan
                    });

                    fetch('/keranjang/tambah', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            id: productId,
                            nama: productName,
                            harga: productPrice,
                            quantity: quantity,
                            type: 'produk',
                            foto: productImage // ← aman dipakai
                        })
                    }).then(res => res.json())
                      .then(data => console.log('✅ Server:', data))
                      .catch(err => {
                          console.error(err);
                          notify('Gagal menambahkan ke keranjang', 'error');
                      });
                }
                saveCart(cart);
                qtyInput.value = 1;
                notify(`${productName} ditambahkan ke keranjang`);

            } catch (error) {
                console.error('❌ Error:', error);
                notify('Gagal menambahkan ke keranjang', 'error');
            }
        }
    });

    // Inisialisasi saat halaman dimuat
    updateCartUI();

    // Jika flag diset dari Laravel (clear_client_cart)
    if (window._shouldUpdateCartUI === true) {
        console.log('🧹 Reset cart UI karena sinyal dari server...');
        sessionStorage.clear();

        // Pastikan UI langsung bersih
        const badge = document.querySelector('.cart-badge');
        const total = document.querySelector('.cart-total');
        if (badge) {
            badge.textContent = '0';
            badge.style.display = 'none';
        }
        if (total) {
            total.textContent = 'Rp 0';
        }

        updateCartUI(); // refresh ulang
    }

    console.log('✅ Cart UI initialized');

    console.log(`🔍 Found ${document.querySelectorAll('.btn-plus').length} plus buttons`);
    console.log(`🔍 Found ${document.querySelectorAll('.btn-minus').length} minus buttons`);
    console.log(`🔍 Found ${document.querySelectorAll('.add-to-cart').length} cart buttons`);
    console.log('✅ Cart system ready!');
});
</script>
@endsection