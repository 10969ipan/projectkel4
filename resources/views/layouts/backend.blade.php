<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - {{ config('app.name', 'Sistem Manajemen Stok') }}</title>
    <link rel="icon" href="{{ asset('image/sima1.png') }}" type="image/png">
    
    <!-- Offline Assets -->
    <script src="{{ asset('assets/vendor/tailwind/tailwind-cdn.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/all.min.css') }}">
    <script defer src="{{ asset('assets/vendor/alpinejs/alpine.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/inter/inter.css') }}">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#93cdfdff',
                            800: '#41b3f0ff',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .sidebar-item.active {
            background-color: #0284c7;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(2, 132, 199, 0.2);
        }

        .sidebar-item.active i {
            color: white;
        }

        .sidebar-item.active:hover {
            background-color: #0369a1;
        }

        .card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pagination li {
            margin: 0 2px;
        }

        .pagination li a,
        .pagination li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            transition: all 0.2s ease;
        }

        .pagination li a:hover {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .pagination li.active span {
            background-color: #0ea5e9;
            color: white;
        }

        .pagination li.disabled span {
            color: #9ca3af;
            cursor: not-allowed;
        }

        select,
        input[type="date"],
        input[type="text"] {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        select:focus,
        input[type="date"]:focus,
        input[type="text"]:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
    </style>
    @stack('styles')
</head>

<body class="h-full" x-data="{ mobileMenuOpen: false, profileMenuOpen: false }">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-white border-r border-gray-200">
                <div class="flex items-center justify-center h-16 px-4 bg-white border-b border-gray-100">
                    <div class="flex items-center">
                        <img src="{{ asset('image/sima1.png') }}" alt="SIMA-APOTEK" class="h-8 w-8 mr-2" loading="lazy" />
                        <span class="text-base font-black text-gray-800 tracking-tighter uppercase">SIMA-<span class="text-primary-600">APOTEK</span></span>
                    </div>
                </div>
                <div class="flex flex-col flex-grow overflow-y-auto">
                    <div class="flex flex-col py-4">
                        <!-- User Profile Card in Sidebar -->
                        <div class="flex items-center p-3 bg-primary-50 rounded-xl border border-primary-100 mb-4 mx-4">
                            <div class="relative h-10 w-10 flex-shrink-0">
                                <div class="absolute inset-0 rounded-full bg-primary-600 flex items-center justify-center text-white font-black shadow-sm">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                @if(auth()->user()->profile_photo)
                                    <img class="absolute inset-0 h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm"
                                        src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                                        alt="{{ auth()->user()->name }}"
                                        onerror="this.style.display='none'"
                                        loading="lazy">
                                @endif
                            </div>
                            <div class="ml-3 overflow-hidden">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] font-bold text-primary-500 uppercase tracking-tighter">{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Staf Farmasi' }}</p>
                            </div>
                        </div>

                        <nav class="flex-1 px-3 space-y-1">
                            <a href="{{ route('dashboard') }}"
                                class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                <i class="fas fa-chart-line mr-3"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('items.index') }}"
                                class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('items.*') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                <i class="fas fa-pills mr-3"></i>
                                Obat
                            </a>
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('transactions.index') }}"
                                    class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('transactions.*') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                    <i class="fas fa-exchange-alt mr-3"></i>
                                    Mutasi Stok
                                </a>
                            @endif
                            <a href="{{ route('item-requests.index') }}"
                                class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('item-requests.*') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                <i class="fas fa-file-medical mr-3"></i>
                                Permintaan Obat
                            </a>

                            @if (auth()->user()->isAdmin())
                                <div class="px-4 pt-6 pb-2">
                                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Administrator</h3>
                                </div>
                                <a href="{{ route('admin.pharmacare.transactions') }}"
                                    class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('admin.pharmacare.transactions') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                    <i class="fas fa-shopping-cart mr-3"></i>
                                    Transaksi Toko
                                </a>
                                <a href="{{ route('admin.pharmacare.transaction-logs') }}"
                                    class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('admin.pharmacare.transaction-logs') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                    <i class="fas fa-history mr-3"></i>
                                    Log Transaksi
                                </a>
                                <a href="{{ route('admin.pharmacare.customers') }}"
                                    class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('admin.pharmacare.customers') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                    <i class="fas fa-users mr-3"></i>
                                    Manajemen Pelanggan
                                </a>

                                <div class="px-4 pt-6 pb-2">
                                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Master Data</h3>
                                </div>
                                <a href="{{ route('users.index') }}"
                                    class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('users.*') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                    <i class="fas fa-users-cog mr-3"></i>
                                    User Staff
                                </a>
                                <a href="{{ route('categories.index') }}"
                                    class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('categories.*') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                    <i class="fas fa-tags mr-3"></i>
                                    Kategori
                                </a>
                                <a href="{{ route('units.index') }}"
                                    class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('units.*') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                    <i class="fas fa-balance-scale mr-3"></i>
                                    Satuan Ukur
                                </a>

                                <div x-data="{ open: false }" class="space-y-1">
                                    <button @click="open = !open"
                                        class="group w-full flex items-center px-4 py-3 text-sm font-bold text-gray-500 hover:text-primary-600 hover:bg-gray-50 rounded-xl transition-all">
                                        <i class="fas fa-file-medical-alt mr-3 text-gray-400 group-hover:text-primary-600"></i>
                                        <span class="flex-1 text-left uppercase text-[10px] tracking-widest font-black">Laporan Medik</span>
                                        <svg :class="{ 'transform rotate-180': open }"
                                            class="ml-3 h-4 w-4 text-gray-400 group-hover:text-primary-600 transition-transform duration-200"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition class="pl-4 space-y-1">
                                        <a href="{{ route('reports.stock') }}"
                                            class="group flex items-center px-4 py-2 text-xs font-bold text-gray-500 rounded-lg hover:text-primary-600 hover:bg-primary-50">
                                            <i class="fas fa-boxes mr-3 text-[10px]"></i>
                                            Laporan Stok
                                        </a>
                                        <a href="{{ route('reports.transactions') }}"
                                            class="group flex items-center px-4 py-2 text-xs font-bold text-gray-500 rounded-lg hover:text-primary-600 hover:bg-primary-50">
                                            <i class="fas fa-history mr-3 text-[10px]"></i>
                                            Riwayat Mutasi
                                        </a>
                                        <a href="{{ route('reports.requests') }}"
                                            class="group flex items-center px-4 py-2 text-xs font-bold text-gray-500 rounded-lg hover:text-primary-600 hover:bg-primary-50">
                                            <i class="fas fa-clipboard-check mr-3 text-[10px]"></i>
                                            Rekap Permintaan
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </nav>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="group flex items-center w-full px-4 py-3 text-sm font-bold text-red-500 rounded-xl hover:bg-red-50 transition-all">
                            <i class="fas fa-power-off mr-3"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center">
                    <button @click="mobileMenuOpen = true" class="md:hidden text-gray-500 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="ml-4 text-gray-800 font-bold tracking-tight">@yield('title')</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center focus:outline-none">
                            <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border border-primary-200">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="origin-top-right absolute right-0 mt-2 w-48 rounded-xl shadow-xl py-1 bg-white ring-1 ring-black ring-opacity-5 z-50">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 font-bold">Profil Saya</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold">Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    @yield('header')
                </div>
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            iconColor: '#0076D6'
        });
        @if (session('success')) Toast.fire({ icon: 'success', title: '{{ session('success') }}' }); @endif
        @if (session('error')) Toast.fire({ icon: 'error', title: '{{ session('error') }}' }); @endif
    </script>
    @stack('scripts')
</body>

</html>
