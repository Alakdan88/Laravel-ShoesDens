<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ShoesDens - Toko Penjualan Sepatu Online')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    
    <!-- Google Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        .icon-align {
            display: inline-flex;
            vertical-align: middle;
            align-items: center;
            gap: 8px;
        }
        .btn-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <!-- Logo -->
            <div class="navbar-logo">
                <a href="{{ url('/') }}">
                    <span class="logo-text">ShoesDens</span>
                </a>
            </div>

            <!-- Toggle Mobile Menu -->
            <div class="navbar-toggle" id="navbarToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <!-- Menu -->
            <ul class="navbar-menu" id="navbarMenu">
                <li><a href="{{ url('/') }}" class="nav-link">Beranda</a></li>
                <li><a href="{{ url('/produk') }}" class="nav-link">Katalog</a></li>
                
                @auth
                    @if(Auth::user()->isPenjual())
                        <li><a href="{{ url('/penjual/dashboard') }}" class="nav-link">Dashboard</a></li>
                        <li><a href="{{ url('/penjual/produk') }}" class="nav-link">Produk Saya</a></li>
                    @endif
                @endauth

                <li class="nav-divider"></li>

                <!-- Keranjang (hanya untuk pembeli) -->
                @auth
                    @if(Auth::user()->isPembeli())
                        <li class="keranjang-icon">
                            <a href="{{ url('/pembeli/keranjang') }}" title="Keranjang">
                                <span class="icon">🛒</span>
                                <span class="badge" id="cartCount">0</span>
                            </a>
                        </li>
                    @endif
                @endauth

                <!-- Auth -->
                @auth
                    <li class="user-menu">
                        <button class="user-btn" id="userBtn">
                            <span class="user-name">{{ Auth::user()->nama }}</span>
                        </button>
                        <div class="dropdown-menu" id="userDropdown">
                            @if(Auth::user()->isPembeli())
                                <a href="{{ url('/pembeli/pesanan') }}">Pesanan Saya</a>
                                <a href="{{ url('/pembeli/profil') }}">Profil</a>
                            @endif
                            
                            <form action="{{ url('/logout') }}" method="POST" id="logout-form" style="display: none;">
                                @csrf
                            </form>
                            <a href="#" class="logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        </div>
                    </li>
                @else
                    <li><a href="{{ url('/login') }}" class="btn-login">Masuk</a></li>
                    <li><a href="{{ url('/register') }}" class="btn-register">Daftar</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container-wrapper">
        <!-- Flash Message -->
        @if(session('message'))
            <div class="container" style="padding: 1.5rem 1rem 0;">
                <div class="alert alert-{{ session('message.type') }}">
                    <span class="alert-close" onclick="this.parentElement.remove();">&times;</span>
                    {{ session('message.message') }}
                </div>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>Tentang ShoesDens</h4>
                <p>ShoesDens adalah platform pembelian sepatu online terpercaya.</p>
            </div>

            <div class="footer-section">
                <h4>Menu</h4>
                <ul>
                    <li><a href="{{ url('/') }}">Beranda</a></li>
                    <li><a href="{{ url('/produk') }}">Katalog Produk</a></li>
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Hubungi Kami</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Kontak</h4>
                <p>Email: cs@shoesdens.com</p>
                <p>Telepon: +62 812-3456-7890</p>
                <p>Alamat: Jl. Siliwangi No.4, Cirendang, Kuningan, Jawa Barat</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 ShoesDens. Semua hak cipta dilindungi.</p>
        </div>
    </footer>

    <script>
        // Toggle mobile menu
        const navbarToggle = document.getElementById('navbarToggle');
        const navbarMenu = document.getElementById('navbarMenu');

        if (navbarToggle) {
            navbarToggle.addEventListener('click', function() {
                navbarMenu.classList.toggle('active');
            });
        }

        // Toggle user dropdown
        const userBtn = document.getElementById('userBtn');
        const userDropdown = document.getElementById('userDropdown');

        if (userBtn && userDropdown) {
            userBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('show');
            });

            document.addEventListener('click', function() {
                userDropdown.classList.remove('show');
            });
        }

        // Update cart count (untuk pembeli)
        function updateCartCount() {
            @auth
                @if(Auth::user()->isPembeli())
                fetch('{{ url("/pembeli/keranjang/count") }}')
                    .then(response => response.json())
                    .then(data => {
                        const cartCountEl = document.getElementById('cartCount');
                        if (cartCountEl) {
                            cartCountEl.textContent = data.count;
                        }
                    })
                    .catch(err => console.error('Error:', err));
                @endif
            @endauth
        }

        // Update cart count saat halaman dimuat
        updateCartCount();
    </script>
    @yield('scripts')
</body>
</html>
