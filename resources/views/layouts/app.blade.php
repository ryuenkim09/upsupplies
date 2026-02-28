<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @php
        use Illuminate\Support\Facades\Auth;
    @endphp
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body {
            background-color: #f0ebe5;
            color: #000;
        }
        .navbar {
            background-color: #000;
            border-bottom: 1px solid #333;
        }
        .navbar-light .navbar-brand,
        .navbar-light .nav-link {
            color: #fff !important;
        }
        .navbar-light .nav-link:hover {
            color: #8b7355 !important;
        }
        .btn-primary {
            background-color: #000;
            border-color: #000;
        }
        .btn-primary:hover {
            background-color: #8b7355;
            border-color: #8b7355;
        }
        .btn-success {
            background-color: #8b7355;
            border-color: #8b7355;
        }
        .btn-success:hover {
            background-color: #6b5344;
            border-color: #6b5344;
        }
        .btn-info {
            background-color: #5a7a8c;
            border-color: #5a7a8c;
        }
        .btn-info:hover {
            background-color: #465a6e;
            border-color: #465a6e;
        }
        .btn-light {
            background-color: #faf6f1;
            color: #000;
            border: 1px solid #e8dfd8;
        }
        .btn-light:hover {
            background-color: #f2ece5;
            color: #000;
        }
        .card {
            border-color: #e8dfd8;
            background-color: #fffbf7;
        }
        .card-header {
            background-color: #faf6f1;
            border-color: #e8dfd8;
        }
        .alert {
            background-color: #faf6f1;
            border-color: #e8dfd8;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    UpSupplies
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                {{-- @var \Illuminate\Support\Facades\Auth Auth --}}
                <div class="collapse navbar-collapse" id="navbarNav">
                    <!-- search form slightly left of center -->
                    <div class="" style="flex:1; max-width:500px; margin-left:50px;">
                        <form class="d-flex" method="GET" action="{{ route('products.index') }}">
                            <input class="form-control me-2" type="search" name="search" placeholder="Search products" value="{{ request('search') }}">
                            <button class="btn btn-light" type="submit">Search</button>
                        </form>
                    </div>
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('products.index') }}">Products</a>
                        </li>
                        @auth
                            @if(\Auth::user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard.index') }}">Admin</a>
                                </li>
                            @endif
                            @if(!\Auth::user()->isAdmin())
                                <li class="nav-item">
                                    @php
                                        /** @var \App\Models\User $user */
                                        $user = Auth::user();
                                        $cartCount = $user->cartItems()->sum('quantity');
                                    @endphp
                                    <a class="nav-link" href="{{ route('getCart') }}">Cart @if($cartCount) ({{ $cartCount }})@endif</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('orderHistory') }}">Orders</a>
                                </li>
                            @endif
                        @endauth
                        @guest
                            @if (\Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                                </li>
                            @endif
                            @if (\Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ \Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('user.profile.edit') }}">
                                            Profile
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>
                                    </li>
                                </ul>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- show a back link on every admin page --}}
                @if(request()->is('admin/*'))
                    <div class="mb-3">
                        <a href="{{ route('admin.dashboard.index') }}" class="btn btn-outline-secondary">
                            &larr; Back to Dashboard
                        </a>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
    {{-- Toast container (custom styled) --}}
    <div id="custom-toast-root" aria-live="polite" aria-atomic="true" class="position-fixed" style="top:20px; right:20px; z-index: 1080;">
        @if(session('success'))
            <div class="custom-toast success" role="status" data-autohide="true" data-delay="4000">
                <div class="toast-content">{{ session('success') }}</div>
                <button class="toast-close" aria-label="Close">×</button>
            </div>
        @endif
        @if(session('error'))
            <div class="custom-toast error" role="status" data-autohide="true" data-delay="6000">
                <div class="toast-content">{{ session('error') }}</div>
                <button class="toast-close" aria-label="Close">×</button>
            </div>
        @endif
    </div>

    {{-- Checkout modal (custom styled) --}}
    @if(session('checkout_success'))
        @php $cs = session('checkout_success'); @endphp
        <div id="checkoutModal" class="custom-modal" aria-hidden="true" tabindex="-1">
            <div class="custom-modal-dialog">
                <div class="custom-modal-content">
                    <button class="custom-modal-close" aria-label="Close">×</button>
                    <div class="custom-modal-body">
                        <h4 class="mb-2">Order Placed Successfully</h4>
                        <p class="mb-1">{{ $cs['message'] ?? 'Your order was placed.' }}</p>
                        <p class="mb-1"><strong>Order ID:</strong> {{ $cs['order_id'] ?? '' }}</p>
                        @if(!empty($cs['total']))
                            <p class="mb-3"><strong>Total:</strong> ₱{{ number_format($cs['total'], 2) }}</p>
                        @endif
                        
                        @if(($cs['payment_method'] ?? null) === 'cod')
                            <div style="background-color:#fff3cd; border:1px solid #ffc107; border-radius:8px; padding:12px; margin-bottom:15px;">
                                <p style="margin:0; color:#856404; font-weight:500;">
                                    <strong>💳 Cash on Delivery (COD)</strong><br>
                                    <small>Please have cash ready when the delivery arrives. Payment must be made upon delivery.</small>
                                </p>
                            </div>
                        @elseif(($cs['payment_method'] ?? null) === 'online')
                            <div style="background-color:#d4edda; border:1px solid #28a745; border-radius:8px; padding:12px; margin-bottom:15px;">
                                <p style="margin:0; color:#155724; font-weight:500;">
                                    <strong>✓ Payment Confirmed</strong><br>
                                    <small>Your online payment has been processed successfully.</small>
                                </p>
                            </div>
                        @endif
                    </div>
                    <div class="custom-modal-footer">
                        <a href="{{ route('orderDetails', $cs['order_id']) }}" class="btn btn-primary">View Order</a>
                        <a href="{{ route('orderHistory') }}" class="btn btn-secondary">My Orders</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        /* Toast styles */
        .custom-toast {
            min-width: 280px;
            max-width: 380px;
            margin-bottom: 12px;
            padding: 12px 14px;
            border-radius: 12px;
            color: #fff;
            box-shadow: 0 6px 18px rgba(0,0,0,0.35);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            opacity: 0;
            transform: translateY(-8px) scale(0.98);
            transition: all 210ms ease;
        }
        .custom-toast.show { opacity: 1; transform: translateY(0) scale(1); }
        .custom-toast.success { background: linear-gradient(180deg,#2b2b2b,#1f1b1b); border: 1px solid rgba(255,255,255,0.04); }
        .custom-toast.error { background: linear-gradient(180deg,#4c2323,#3a1919); border: 1px solid rgba(255,255,255,0.04); }
        .custom-toast .toast-content { flex:1; font-size:14px; }
        .custom-toast .toast-close { background: transparent; border: 1px solid rgba(255,255,255,0.08); color:#fff; padding:4px 8px; border-radius:8px; cursor:pointer; }

        /* Modal styles */
        .custom-modal { position: fixed; inset:0; display:flex; align-items:center; justify-content:center; background: rgba(0,0,0,0.45); z-index:1100; opacity:0; pointer-events:none; transition: opacity 180ms ease; }
        .custom-modal.open { opacity:1; pointer-events:auto; }
        .custom-modal-dialog { max-width:600px; width:100%; padding: 20px; }
        .custom-modal-content { background: linear-gradient(180deg,#111,#0b0b0b); color:#fff; border-radius:14px; padding:20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .custom-modal-close { position:absolute; right:18px; top:14px; background:#fde6e9; color:#b0303f; border-radius:999px; border:none; width:36px; height:36px; font-size:18px; cursor:pointer; }
        .custom-modal-body h4 { color:#ffd1d8; }
        .custom-modal-footer { display:flex; gap:10px; margin-top:12px; }
        .custom-modal .btn-primary { background:#8b7355; border-color:#8b7355; }
        .custom-modal .btn-secondary { background:#faf6f1; color:#000; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Try using Bootstrap toast/modal if available, otherwise use custom handlers
            if (window.bootstrap && typeof window.bootstrap.Toast === 'function') {
                // Bootstrap present: init standard toasts
                var toastElList = [].slice.call(document.querySelectorAll('.toast'));
                toastElList.forEach(function (toastEl) {
                    var t = new bootstrap.Toast(toastEl);
                    t.show();
                });

                var checkoutModalEl = document.querySelector('.modal');
                if (checkoutModalEl) {
                    var modal = new bootstrap.Modal(checkoutModalEl);
                    modal.show();
                }
                return;
            }

            // Custom toasts fallback
            var toasts = document.querySelectorAll('#custom-toast-root .custom-toast');
            toasts.forEach(function (t) {
                // show
                t.classList.add('show');
                var delay = parseInt(t.getAttribute('data-delay') || 4000, 10);
                // close button
                var btn = t.querySelector('.toast-close');
                if (btn) btn.addEventListener('click', function () { t.classList.remove('show'); });
                // autohide
                if (t.getAttribute('data-autohide') !== 'false') {
                    setTimeout(function () { t.classList.remove('show'); }, delay);
                }
            });

            // Custom checkout modal fallback
            var checkoutModal = document.getElementById('checkoutModal');
            if (checkoutModal) {
                checkoutModal.classList.add('open');
                var close = checkoutModal.querySelector('.custom-modal-close');
                if (close) close.addEventListener('click', function () { checkoutModal.classList.remove('open'); });
                // close when clicking outside content
                checkoutModal.addEventListener('click', function (e) {
                    if (e.target === checkoutModal) checkoutModal.classList.remove('open');
                });
            }
        });
    </script>
</body>
</html>
