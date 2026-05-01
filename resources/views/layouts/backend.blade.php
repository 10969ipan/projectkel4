<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - {{ config('app.name', 'Sistem Manajemen Stok') }}</title>
    <link rel="icon" href="{{ asset('image/sima1.png') }}" type="image/png">
    
    <!-- Offline Assets -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/inter/inter.css') }}">
    <script src="{{ asset('assets/vendor/tailwind/tailwind-cdn.js') }}"></script>
    <script defer src="{{ asset('assets/vendor/alpinejs/alpine.min.js') }}"></script>
    
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
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

        /* Global fix for SweetAlert z-index */
        .swal2-container {
            z-index: 1000000 !important;
        }

        /* Page Transitions - Option 1: Premium Fade & Slide (Smoothed) */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-page-content {
            animation: fadeInUp 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            will-change: transform, opacity;
        }

        /* Sidebar Item Enhancements (Smoothed & Staggered) */
        @keyframes sidebarSlideIn {
            from {
                opacity: 0;
                transform: translateX(-12px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .sidebar-item {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: sidebarSlideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
        }

        /* Staggered delays for sidebar items */
        .sidebar-item:nth-child(1) { animation-delay: 0.05s; }
        .sidebar-item:nth-child(2) { animation-delay: 0.10s; }
        .sidebar-item:nth-child(3) { animation-delay: 0.15s; }
        .sidebar-item:nth-child(4) { animation-delay: 0.20s; }
        .sidebar-item:nth-child(5) { animation-delay: 0.25s; }
        .sidebar-item:nth-child(n+6) { animation-delay: 0.30s; }

        .sidebar-item:hover:not(.active) {
            transform: translateX(6px);
            color: #0284c7;
            background-color: rgba(14, 165, 233, 0.05);
        }

        .sidebar-item i {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: inline-block;
            backface-visibility: hidden;
            transform-style: preserve-3d;
            -webkit-font-smoothing: subpixel-antialiased;
        }

        .sidebar-item:hover i {
            transform: scale(1.2) rotate(5deg);
            color: #0284c7;
        }

        /* Icon-specific smoothness for the entire page */
        i.fas, i.fab, i.far, i.fal, i.fad {
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            display: inline-block;
            min-width: 1.25rem; /* Pre-allocate space for icons */
            text-align: center;
        }

        /* Ensure x-cloak works for SVG and nested elements */
        [x-cloak] { display: none !important; }

        .loading-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(to right, #0ea5e9, #6366f1, #0ea5e9);
            background-size: 200% 100%;
            z-index: 9999;
            width: 0;
            animation: progress 0.8s ease-out forwards, gradientMove 2s linear infinite;
        }

        @keyframes progress {
            0% { width: 0; opacity: 1; }
            80% { width: 100%; opacity: 1; }
            100% { width: 100%; opacity: 0; }
        }

        @keyframes gradientMove {
            0% { background-position: 100% 0; }
            100% { background-position: -100% 0; }
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

<body class="h-full" x-data="{ mobileMenuOpen: false, profileMenuOpen: false }">
    <div class="loading-bar"></div>
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="mobileMenuOpen" class="fixed inset-0 z-50 flex md:hidden" x-cloak>
            <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="mobileMenuOpen = false"></div>

            <div x-show="mobileMenuOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-white">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="mobileMenuOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <i class="fas fa-times text-white text-xl"></i>
                    </button>
                </div>
                
                <div class="flex-shrink-0 flex items-center px-4">
                    <img src="{{ asset('image/sima1.png') }}" alt="SIMA-APOTEK" class="h-8 w-8 mr-2" />
                    <span class="text-base font-black text-gray-800 tracking-tighter uppercase">SIMA-<span class="text-primary-600">APOTEK</span></span>
                </div>
                
                <div class="mt-5 flex-1 h-0 overflow-y-auto">
                    <!-- User Profile Card in Mobile Sidebar -->
                    <div class="flex items-center p-3 bg-primary-50 rounded-xl border border-primary-100 mb-4 mx-4">
                        <div class="relative h-10 w-10 flex-shrink-0">
                            <div class="absolute inset-0 rounded-full bg-primary-600 flex items-center justify-center text-white font-black shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="ml-3 overflow-hidden">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] font-bold text-primary-500 uppercase tracking-tighter">{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Staf Farmasi' }}</p>
                        </div>
                    </div>

                    <nav class="px-2 space-y-1">
                        <a href="{{ route('dashboard') }}" class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                            <i class="fas fa-chart-line mr-3"></i> Dashboard
                        </a>
                        <a href="{{ route('items.index') }}" class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('items.*') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                            <i class="fas fa-pills mr-3"></i> Obat
                        </a>
                        @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                            <a href="{{ route('transactions.index') }}" class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('transactions.*') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                <i class="fas fa-exchange-alt mr-3"></i> Mutasi Stok
                            </a>
                        @endif
                        <a href="{{ route('item-requests.index') }}" class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('item-requests.*') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                            <i class="fas fa-file-medical mr-3"></i> Permintaan Obat
                        </a>

                        @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                            <div class="px-4 pt-6 pb-2"><h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Transaksi Toko</h3></div>
                            <a href="{{ route('admin.pharmacare.transactions') }}" class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('admin.pharmacare.transactions') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                <i class="fas fa-shopping-cart mr-3"></i> Transaksi Toko
                            </a>
                            <a href="{{ route('admin.pharmacare.transaction-logs') }}" class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('admin.pharmacare.transaction-logs') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                <i class="fas fa-history mr-3"></i> Log Transaksi
                            </a>
                        @endif

                        @if (auth()->user()->isAdmin())
                            <div class="px-4 pt-6 pb-2"><h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Administrator</h3></div>
                            <a href="{{ route('admin.pharmacare.customers') }}" class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('admin.pharmacare.customers') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                <i class="fas fa-users mr-3"></i> Manajemen Pelanggan
                            </a>

                            <div class="px-4 pt-6 pb-2"><h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Master Data</h3></div>
                            <a href="{{ route('users.index') }}" class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('users.*') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                <i class="fas fa-users-cog mr-3"></i> User Staff
                            </a>
                            <a href="{{ route('categories.index') }}" class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('categories.*') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                <i class="fas fa-tags mr-3"></i> Kategori
                            </a>
                            <a href="{{ route('units.index') }}" class="sidebar-item group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all {{ request()->routeIs('units.*') ? 'sidebar-item active' : 'text-gray-500 hover:bg-gray-50 hover:text-primary-600' }}">
                                <i class="fas fa-balance-scale mr-3"></i> Satuan Ukur
                            </a>

                            <div x-data="{ reportOpen: false }" class="space-y-1 mt-2">
                                <button @click="reportOpen = !reportOpen" class="group w-full flex items-center px-4 py-3 text-sm font-bold text-gray-500 hover:text-primary-600 hover:bg-gray-50 rounded-xl transition-all">
                                    <i class="fas fa-file-medical-alt mr-3 text-gray-400 group-hover:text-primary-600"></i>
                                    <span class="flex-1 text-left uppercase text-[10px] tracking-widest font-black">Laporan Medik</span>
                                    <svg :class="{ 'transform rotate-180': reportOpen }" class="ml-3 h-4 w-4 text-gray-400 group-hover:text-primary-600 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div x-show="reportOpen" x-transition x-cloak class="pl-4 space-y-1">
                                    <a href="{{ route('reports.stock') }}" class="group flex items-center px-4 py-2 text-xs font-bold text-gray-500 rounded-lg hover:text-primary-600 hover:bg-primary-50">
                                        <i class="fas fa-boxes mr-3 text-[10px]"></i> Laporan Stok
                                    </a>
                                    <a href="{{ route('reports.transactions') }}" class="group flex items-center px-4 py-2 text-xs font-bold text-gray-500 rounded-lg hover:text-primary-600 hover:bg-primary-50">
                                        <i class="fas fa-history mr-3 text-[10px]"></i> Riwayat Mutasi
                                    </a>
                                    <a href="{{ route('reports.requests') }}" class="group flex items-center px-4 py-2 text-xs font-bold text-gray-500 rounded-lg hover:text-primary-600 hover:bg-primary-50">
                                        <i class="fas fa-clipboard-check mr-3 text-[10px]"></i> Rekap Permintaan
                                    </a>
                                </div>
                            </div>
                        @endif
                    </nav>
                </div>
                
                <div class="p-4 border-t border-gray-100 bg-gray-50/50 mt-auto">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="group flex items-center w-full px-4 py-3 text-sm font-bold text-red-500 rounded-xl hover:bg-red-50 transition-all">
                            <i class="fas fa-power-off mr-3"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
            <div class="flex-shrink-0 w-14"></div>
        </div>

        <!-- Desktop Sidebar -->
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
                                @if(auth()->user()->profile_photo && (auth()->user()->isAdmin() || auth()->user()->isStaff()))
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
                            @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
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

                            {{-- Transaksi Toko & Log: Admin + Staff --}}
                            @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                                <div class="px-4 pt-6 pb-2">
                                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Transaksi Toko</h3>
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
                            @endif

                            @if (auth()->user()->isAdmin())
                                <div class="px-4 pt-6 pb-2">
                                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Administrator</h3>
                                </div>
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
                                    <div x-show="open" x-transition x-cloak class="pl-4 space-y-1">
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
                    <div class="relative">
                        <button @click="profileMenuOpen = !profileMenuOpen" class="flex items-center focus:outline-none">
                            <div class="relative h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border border-primary-200 overflow-hidden">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @if(auth()->user()->profile_photo && (auth()->user()->isAdmin() || auth()->user()->isStaff()))
                                    <img class="absolute inset-0 h-full w-full object-cover"
                                        src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                                        alt="{{ auth()->user()->name }}"
                                        onerror="this.style.display='none'"
                                        loading="lazy">
                                @endif
                            </div>
                        </button>
                        <div x-show="profileMenuOpen" @click.away="profileMenuOpen = false" x-transition x-cloak class="origin-top-right absolute right-0 mt-2 w-48 rounded-xl shadow-xl py-1 bg-white ring-1 ring-black ring-opacity-5 z-50">
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
            <main class="flex-1 overflow-y-auto p-4 sm:px-6 lg:px-8 animate-page-content">
                @if(View::hasSection('header'))
                <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-gray-100">
                    @yield('header')
                </div>
                @endif
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
