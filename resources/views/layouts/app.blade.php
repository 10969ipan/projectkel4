<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - {{ config('app.name', 'Sistem Manajemen Stok') }}</title>
    <link rel="icon" type="image/webp" href="{{ asset('assets/images/branding/pharmacare-logo.webp') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js"></script>
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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .sidebar-item.active {
            background-color: #e0f2fe;
            color: #0369a1;
            border-left: 4px solid #0ea5e9;
        }

        .sidebar-item.active:hover {
            background-color: #e0f2fe;
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

        /* Untuk konsistensi tampilan form controls */
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

        /* Global fix for SweetAlert z-index */
        .swal2-container {
            z-index: 1000000 !important;
        }

        /* Skeleton Animation */
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        
        .skeleton {
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite linear;
        }

        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
    <!-- Vercel Speed Insights -->
    <script>
        window.si = window.si || function () { (window.siq = window.siq || []).push(arguments); };
    </script>
    <script defer src="/_vercel/speed-insights/script.js"></script>
</head>

<body class="h-full" x-data="{ mobileMenuOpen: false, profileMenuOpen: false, pageLoading: true }" x-init="window.onload = () => { setTimeout(() => { pageLoading = false }, 400) }">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[10000] focus:px-4 focus:py-2 focus:bg-primary-600 focus:text-white focus:rounded-lg">Lompat ke Konten</a>
    <div class="min-h-screen flex flex-col md:flex-row">
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-white border-r border-gray-200">
                <div class="flex items-center justify-center h-16 px-4 bg-primary-600 border-b border-primary-700">
                    <div class="flex items-center">
                        <img src="{{ asset('image/sima1.png') }}" alt="SIMA-APOTEK" class="h-12 w-12 mr-2" />
                        <span class="text-xl font-black text-primary-600 tracking-widest">SIMA-APOTEK</span>
                    </div>
                </div>
                <div class="flex flex-col flex-grow overflow-y-auto">
                    <div class="flex flex-col py-4">
                            <div x-data="{ openProfile: true }" class="flex flex-col">
                                <div class="flex items-center p-3 bg-primary-50 rounded-xl border border-primary-100 mb-4 mx-4">
                                    <div class="relative h-10 w-10 flex-shrink-0">
                                        <div class="absolute inset-0 rounded-full bg-primary-600 flex items-center justify-center text-white font-black shadow-sm">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                        @if(auth()->user()->profile_photo && (auth()->user()->isAdmin() || auth()->user()->isStaff()))
                                            <img class="absolute inset-0 h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm"
                                                src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                                                alt="{{ auth()->user()->name }}"
                                                onerror="this.style.display='none'">
                                        @endif
                                    </div>
                                    <div class="ml-3 overflow-hidden">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                        <p class="text-[10px] font-bold text-primary-500 uppercase tracking-tighter">{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Staf Farmasi' }}</p>
                                    </div>
                                </div>
                            </div>
                        <nav class="flex-1 px-3 space-y-1">
                            <a href="{{ route('dashboard') }}"
                                class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                <i class="fas fa-chart-line mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}" aria-hidden="true"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('items.index') }}"
                                class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('items.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                <i class="fas fa-pills mr-3 {{ request()->routeIs('items.*') ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}"></i>
                                Obat
                            </a>
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('transactions.index') }}"
                                    class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('transactions.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                    <i class="fas fa-exchange-alt mr-3 {{ request()->routeIs('transactions.*') ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}"></i>
                                    Mutasi Stok
                                </a>
                            @endif
                            <a href="{{ route('item-requests.index') }}"
                                class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('item-requests.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                <i class="fas fa-file-medical mr-3 {{ request()->routeIs('item-requests.*') ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}"></i>
                                Permintaan Obat
                            </a>

                            @if (auth()->user()->isAdmin())
                                <div class="px-4 pt-6 pb-2">
                                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Administrator</h3>
                                </div>

                                <a href="{{ route('users.index') }}"
                                    class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('users.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                    <i class="fas fa-users-cog mr-3 {{ request()->routeIs('users.*') ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}"></i>
                                    Manajemen Staff
                                </a>
                                <a href="{{ route('admin.pharmacare.customers') }}"
                                    class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('admin.pharmacare.customers') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                    <i class="fas fa-users mr-3 {{ request()->routeIs('admin.pharmacare.customers') ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}"></i>
                                    Manajemen Pelanggan
                                </a>
                                <a href="{{ route('admin.pharmacare.transactions') }}"
                                    class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('admin.pharmacare.transactions') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                    <i class="fas fa-shopping-cart mr-3 {{ request()->routeIs('admin.pharmacare.transactions') ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}"></i>
                                    Transaksi Toko
                                </a>
                                <a href="{{ route('admin.pharmacare.transaction-logs') }}"
                                    class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('admin.pharmacare.transaction-logs') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                    <i class="fas fa-history mr-3 {{ request()->routeIs('admin.pharmacare.transaction-logs') ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}"></i>
                                    Log Transaksi
                                </a>
                                <a href="{{ route('categories.index') }}"
                                    class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('categories.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                    <i class="fas fa-tags mr-3 {{ request()->routeIs('categories.*') ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}"></i>
                                    Kategori Produk
                                </a>
                                <a href="{{ route('units.index') }}"
                                    class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('units.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                    <i class="fas fa-balance-scale mr-3 {{ request()->routeIs('units.*') ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}"></i>
                                    Satuan Ukur
                                </a>

                                <div x-data="{ open: false }" class="space-y-1">
                                    <button @click="open = !open"
                                        class="group w-full flex items-center px-4 py-3 text-sm font-bold text-gray-500 hover:text-indigo-600 hover:bg-gray-50 rounded-xl transition-all">
                                        <i class="fas fa-file-medical-alt mr-3 text-gray-400 group-hover:text-indigo-600"></i>
                                        <span class="flex-1 text-left">Laporan Medik</span>
                                        <svg :class="{ 'transform rotate-180': open }"
                                            class="ml-3 h-4 w-4 text-gray-400 group-hover:text-indigo-600 transition-transform duration-200"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition class="pl-4 space-y-1">
                                        @if (auth()->user()->isAdmin())
                                            <a href="{{ route('reports.stock') }}"
                                                class="group flex items-center px-4 py-2 text-xs font-bold text-gray-500 rounded-lg hover:text-primary-600 hover:bg-primary-50 {{ request()->routeIs('reports.stock*') ? 'text-primary-600 bg-primary-50' : '' }}">
                                                <i class="fas fa-boxes mr-3 text-[10px]"></i>
                                                Laporan Stok
                                            </a>
                                        @endif

                                        <a href="{{ route('reports.transactions') }}"
                                            class="group flex items-center px-4 py-2 text-xs font-bold text-gray-500 rounded-lg hover:text-primary-600 hover:bg-primary-50 {{ request()->routeIs('reports.transactions*') ? 'text-primary-600 bg-primary-50' : '' }}">
                                            <i class="fas fa-history mr-3 text-[10px]"></i>
                                            Riwayat Mutasi
                                        </a>
                                        <a href="{{ route('reports.requests') }}"
                                            class="group flex items-center px-4 py-2 text-xs font-bold text-gray-500 rounded-lg hover:text-primary-600 hover:bg-primary-50 {{ request()->routeIs('reports.requests*') ? 'text-primary-600 bg-primary-50' : '' }}">
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
                            <button type="submit"
                                class="group flex items-center w-full px-4 py-3 text-sm font-bold text-red-500 rounded-xl hover:bg-red-50 transition-all">
                                <i class="fas fa-power-off mr-3 text-red-400 group-hover:text-red-500"></i>
                                Keluar
                            </button>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-gray-900 bg-opacity-75 md:hidden"
            @click="mobileMenuOpen = false">
        </div>

        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-40 flex flex-col w-full max-w-xs bg-white md:hidden">
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button @click="mobileMenuOpen = false"
                    aria-label="Tutup menu sidebar"
                    class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <span class="sr-only">Close sidebar</span>
                    <i class="fas fa-times text-white text-xl" aria-hidden="true"></i>
                </button>
            </div>
            <div class="flex items-center justify-center h-16 px-4 bg-primary-600 border-b border-primary-700">
                <div class="flex items-center">
                    <img src="{{ asset('image/sima1.png') }}" alt="SIMA-APOTEK" class="h-10 w-10 mr-2" />
                    <span class="text-xl font-black text-white tracking-widest">SIMA-APOTEK</span>
                </div>
            </div>
            <div class="flex-1 h-0 overflow-y-auto">
                <nav class="px-2 py-5 space-y-1">
                    <a href="{{ route('dashboard') }}" @click="mobileMenuOpen = false"
                        class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i
                            class="fas fa-tachometer-alt mr-4 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('dashboard') ? 'text-primary-600' : '' }}"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('items.index') }}" @click="mobileMenuOpen = false"
                        class="group flex items-center px-4 py-2 text-base font-bold rounded-lg {{ request()->routeIs('items.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                        <i
                            class="fas fa-pills mr-4 {{ request()->routeIs('items.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}"></i>
                        Obat
                    </a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('transactions.index') }}" @click="mobileMenuOpen = false"
                            class="group flex items-center px-4 py-2 text-base font-bold rounded-lg {{ request()->routeIs('transactions.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                            <i
                                class="fas fa-exchange-alt mr-4 {{ request()->routeIs('transactions.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}"></i>
                            Mutasi Stok
                        </a>
                    @endif
                    <a href="{{ route('item-requests.index') }}" @click="mobileMenuOpen = false"
                        class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('item-requests.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i
                            class="fas fa-clipboard-list mr-4 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('item-requests.*') ? 'text-primary-600' : '' }}"></i>
                        Permintaan Barang
                    </a>

                    @if (auth()->user()->isAdmin())
                        <div class="px-4 pt-4">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin</h3>
                        </div>

                        <a href="{{ route('users.index') }}" @click="mobileMenuOpen = false"
                            class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('users.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i
                                class="fas fa-users-cog mr-4 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('users.*') ? 'text-primary-600' : '' }}"></i>
                            Manajemen Staff
                        </a>
                        <a href="{{ route('admin.pharmacare.customers') }}" @click="mobileMenuOpen = false"
                            class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('admin.pharmacare.customers') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i
                                class="fas fa-users mr-4 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('admin.pharmacare.customers') ? 'text-primary-600' : '' }}"></i>
                            Manajemen Pelanggan
                        </a>
                        <a href="{{ route('admin.pharmacare.transactions') }}" @click="mobileMenuOpen = false"
                            class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('admin.pharmacare.transactions') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i
                                class="fas fa-shopping-cart mr-4 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('admin.pharmacare.transactions') ? 'text-primary-600' : '' }}"></i>
                            Transaksi Toko
                        </a>
                        <a href="{{ route('admin.pharmacare.transaction-logs') }}" @click="mobileMenuOpen = false"
                            class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('admin.pharmacare.transaction-logs') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i
                                class="fas fa-history mr-4 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('admin.pharmacare.transaction-logs') ? 'text-primary-600' : '' }}"></i>
                            Log Transaksi
                        </a>
                        <a href="{{ route('categories.index') }}" @click="mobileMenuOpen = false"
                            class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('categories.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i
                                class="fas fa-tags mr-4 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('categories.*') ? 'text-primary-600' : '' }}"></i>
                            Kategori
                        </a>
                        <a href="{{ route('units.index') }}" @click="mobileMenuOpen = false"
                            class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('units.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i
                                class="fas fa-balance-scale mr-4 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('units.*') ? 'text-primary-600' : '' }}"></i>
                            Satuan
                        </a>

                        <div x-data="{ open: false }" class="space-y-1">
                            <button @click="open = !open"
                                class="group w-full flex items-center px-2 py-2 text-base font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                <i class="fas fa-chart-bar mr-4 text-gray-400 group-hover:text-gray-500"></i>
                                <span class="flex-1 text-left">Laporan</span>
                                <svg :class="{ 'transform rotate-180': open }"
                                    class="ml-3 h-5 w-5 text-gray-400 group-hover:text-gray-500 transition-transform duration-200"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" class="pl-4 space-y-1">
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('reports.stock') }}" @click="mobileMenuOpen = false"
                                        class="group flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 {{ request()->routeIs('reports.stock*') ? 'text-primary-600 bg-primary-50' : '' }}">
                                        <i class="fas fa-box mr-3 text-gray-400 group-hover:text-gray-500"></i>
                                        Laporan Stok
                                    </a>
                                @endif

                                <a href="{{ route('reports.transactions') }}" @click="mobileMenuOpen = false"
                                    class="group flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 {{ request()->routeIs('reports.transactions*') ? 'text-primary-600 bg-primary-50' : '' }}">
                                    <i class="fas fa-exchange-alt mr-3 text-gray-400 group-hover:text-gray-500"></i>
                                    Laporan Transaksi
                                </a>
                                <a href="{{ route('reports.requests') }}" @click="mobileMenuOpen = false"
                                    class="group flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 {{ request()->routeIs('reports.requests*') ? 'text-primary-600 bg-primary-50' : '' }}">
                                    <i class="fas fa-clipboard-list mr-3 text-gray-400 group-hover:text-gray-500"></i>
                                    Laporan Permintaan
                                </a>
                            </div>
                        </div>
                    @endif
                </nav>
            </div>
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center">
                    <div
                        class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->role }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit"
                        class="group flex items-center w-full px-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                        <i class="fas fa-sign-out-alt mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="bg-white border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center">
                        <button @click="mobileMenuOpen = true"
                            aria-label="Buka menu mobile"
                            aria-expanded="false"
                            :aria-expanded="mobileMenuOpen"
                            class="md:hidden text-gray-500 hover:text-gray-900 focus:outline-none">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 class="ml-4 text-lg font-semibold text-gray-900">@yield('title')</h1>
                    </div>
                    <div class="flex items-center">
                        <div class="relative ml-3">
                            <div>
                                <button @click="profileMenuOpen = !profileMenuOpen" type="button"
                                    aria-label="Menu profil pengguna"
                                    :aria-expanded="profileMenuOpen"
                                    class="flex items-center max-w-xs text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                    id="user-menu-button" aria-haspopup="true">
                                    <span class="sr-only">Open user menu</span>

                                    {{-- LOGIKA BARU: Tampilkan Foto jika ada --}}
                                    <div class="relative h-8 w-8 flex-shrink-0">
                                        <div class="absolute inset-0 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border border-primary-200">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                        @if(auth()->user()->profile_photo && (auth()->user()->isAdmin() || auth()->user()->isStaff()))
                                            <img class="absolute inset-0 h-8 w-8 rounded-full object-cover border-2 border-white"
                                                src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                                                alt="{{ auth()->user()->name }}"
                                                onerror="this.style.display='none'">
                                        @endif
                                    </div>
                                </button>
                            </div>
                            <div x-show="profileMenuOpen" @click.away="profileMenuOpen = false" x-transition
                                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button"
                                tabindex="-1">

                                {{-- === TAMBAHKAN BAGIAN INI === --}}
                                <a href="{{ route('profile') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem"
                                    tabindex="-1">
                                    <i class="fas fa-user-edit mr-2" aria-hidden="true"></i> Edit Profil
                                </a>
                                {{-- ============================ --}}

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        role="menuitem" tabindex="-1" id="user-menu-item-2">
                                        <i class="fas fa-sign-out-alt mr-2" aria-hidden="true"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                <main id="main-content" class="p-4 sm:px-6 lg:px-8 relative">
                    <!-- Skeleton Loader -->
                    <div x-show="pageLoading" class="space-y-6">
                        <div class="flex justify-between items-center mb-8">
                            <div class="h-8 w-48 skeleton rounded-lg"></div>
                            <div class="h-10 w-32 skeleton rounded-lg"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <template x-for="i in 4">
                                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-3">
                                    <div class="h-4 w-20 skeleton rounded"></div>
                                    <div class="h-8 w-32 skeleton rounded"></div>
                                    <div class="h-3 w-40 skeleton rounded"></div>
                                </div>
                            </template>
                        </div>

                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="p-6 border-b border-gray-50 flex justify-between">
                                <div class="h-6 w-32 skeleton rounded"></div>
                                <div class="h-6 w-24 skeleton rounded"></div>
                            </div>
                            <div class="p-6 space-y-4">
                                <template x-for="i in 5">
                                    <div class="flex gap-4">
                                        <div class="h-12 w-12 skeleton rounded-xl"></div>
                                        <div class="flex-1 space-y-2 py-1">
                                            <div class="h-4 w-3/4 skeleton rounded"></div>
                                            <div class="h-3 w-1/2 skeleton rounded"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div x-show="!pageLoading" x-cloak>
                        <div class="mb-6">
                            @yield('header')
                        </div>
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </div>

    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        @endif

        @if (session('warning'))
            Toast.fire({
                icon: 'warning',
                title: '{{ session('warning') }}'
            });
        @endif
    </script>


    @stack('scripts')
</body>

</html>