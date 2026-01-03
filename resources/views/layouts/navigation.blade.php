<nav class="bg-blue-600 text-white shadow-lg relative z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                {{-- LOGO --}}
                <div class="flex-shrink-0 flex items-center">
                    <i class="fas fa-warehouse text-2xl mr-2"></i>
                    <span class="text-xl font-bold">Warehouse Inventory</span>
                </div>

                {{-- DESKTOP MENU (Tengah - Hanya Tampil di Laptop) --}}
                @auth
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}"
                            class="border-transparent hover:border-blue-300 hover:text-blue-100 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'border-blue-500 text-white' : 'text-blue-100' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('items.index') }}"
                            class="border-transparent hover:border-blue-300 hover:text-blue-100 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('items.*') ? 'border-blue-500 text-white' : 'text-blue-100' }}">
                            Items
                        </a>
                        <a href="{{ route('item-requests.index') }}"
                            class="border-transparent hover:border-blue-300 hover:text-blue-100 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('item-requests.*') ? 'border-blue-500 text-white' : 'text-blue-100' }}">
                            Requests
                        </a>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('transactions.index') }}"
                                class="border-transparent hover:border-blue-300 hover:text-blue-100 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('transactions.*') ? 'border-blue-500 text-white' : 'text-blue-100' }}">
                                Transactions
                            </a>
                            <a href="{{ route('users.index') }}"
                                class="border-transparent hover:border-blue-300 hover:text-blue-100 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('users.*') ? 'border-blue-500 text-white' : 'text-blue-100' }}">
                                Users
                            </a>
                            <a href="{{ route('categories.index') }}"
                                class="border-transparent hover:border-blue-300 hover:text-blue-100 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('categories.*') ? 'border-blue-500 text-white' : 'text-blue-100' }}">
                                Categories
                            </a>
                            <a href="{{ route('units.index') }}"
                                class="border-transparent hover:border-blue-300 hover:text-blue-100 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('units.*') ? 'border-blue-500 text-white' : 'text-blue-100' }}">
                                Units
                            </a>
                            <a href="{{ route('reports.stock') }}"
                                class="border-transparent hover:border-blue-300 hover:text-blue-100 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('reports.*') ? 'border-blue-500 text-white' : 'text-blue-100' }}">
                                Reports
                            </a>
                        @endif
                    </div>
                @endauth
            </div>

            {{-- DESKTOP USER MENU (Kanan Atas - Hanya Tampil di Laptop) --}}
            @auth
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <div class="ml-3 relative">
                        <div>
                            <button type="button"
                                class="bg-blue-700 flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-600 focus:ring-white items-center"
                                id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                {{-- FOTO PROFIL --}}
                                @if(auth()->user()->profile_photo)
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="{{ auth()->user()->name }}">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-blue-300 flex items-center justify-center text-blue-800 font-bold">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                            </button>
                        </div>
                        
                        {{-- DROPDOWN MENU --}}
                        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                            role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            
                            {{-- TAMBAHAN: LINK EDIT PROFIL --}}
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">
                                <i class="fas fa-user-edit mr-2"></i> Edit Profil
                            </a>
                            {{-- BATAS TAMBAHAN --}}
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    role="menuitem" tabindex="-1">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Sign out
                                </button>
                            </form>
                        </div>

            {{-- MOBILE MENU BUTTON (Tombol Garis Tiga) --}}
            <div class="-mr-2 flex items-center sm:hidden">
                <button type="button"
                    class="inline-flex items-center justify-center p-2 rounded-md text-blue-200 hover:text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                    aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    {{-- Icon Hamburger --}}
                    <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    {{-- Icon Silang (X) --}}
                    <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MOBILE MENU LIST (Tampil saat tombol diklik di HP) --}}
    <div class="sm:hidden hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:text-white hover:bg-blue-700' }}">Dashboard</a>
            <a href="{{ route('items.index') }}"
                class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('items.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:text-white hover:bg-blue-700' }}">Items</a>
            <a href="{{ route('item-requests.index') }}"
                class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('item-requests.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:text-white hover:bg-blue-700' }}">Requests</a>
            
            @if (auth()->check() && auth()->user()->isAdmin())
                <a href="{{ route('transactions.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('transactions.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:text-white hover:bg-blue-700' }}">Transactions</a>
                <a href="{{ route('users.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('users.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:text-white hover:bg-blue-700' }}">Users</a>
                <a href="{{ route('categories.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('categories.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:text-white hover:bg-blue-700' }}">Categories</a>
                <a href="{{ route('units.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('units.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:text-white hover:bg-blue-700' }}">Units</a>
                <a href="{{ route('reports.stock') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('reports.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:text-white hover:bg-blue-700' }}">Reports</a>
            @endif
        </div>

        {{-- MOBILE USER PROFILE (Bagian Bawah Menu HP) --}}
        @auth
            <div class="pt-4 pb-3 border-t border-blue-700">
                <div class="flex items-center px-5">
                    <div class="flex-shrink-0">
                        {{-- LINK PROFILE MOBILE (Bisa diklik) --}}
                        <a href="{{ route('profile') }}">
                            @if(auth()->user()->profile_photo)
                                <img class="h-10 w-10 rounded-full object-cover border-2 border-white" src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="{{ auth()->user()->name }}">
                            @else
                                <div class="h-10 w-10 rounded-full bg-blue-300 flex items-center justify-center text-blue-800 font-bold border-2 border-white">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </a>
                    </div>
                    <div class="ml-3">
                    <a href="{{ route('profile') }}" class="group">
                        <div class="text-base font-medium text-white group-hover:text-blue-200">{{ auth()->user()->name }}</div>
                        <div class="text-sm font-medium text-blue-300">{{ auth()->user()->email }}</div>
                    </a>
                </div>
                </div>
                <div class="mt-3 space-y-1 px-2">
                    <a href="{{ route('profile') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-blue-200 hover:text-white hover:bg-blue-700">
                        Your Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-blue-200 hover:text-white hover:bg-blue-700">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle untuk Menu Mobile
        const mobileMenuButton = document.querySelector('[aria-controls="mobile-menu"]');
        const mobileMenu = document.getElementById('mobile-menu');

        if(mobileMenuButton) {
            mobileMenuButton.addEventListener('click', function() {
                const expanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !expanded);
                mobileMenu.classList.toggle('hidden');
                
                // Ubah icon hamburger jadi X (dan sebaliknya)
                const svgs = this.querySelectorAll('svg');
                svgs.forEach(svg => svg.classList.toggle('hidden'));
                svgs.forEach(svg => svg.classList.toggle('block'));
            });
        }

        // Toggle untuk Dropdown User Desktop
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.querySelector('[role="menu"]');

        if(userMenuButton) {
            userMenuButton.addEventListener('click', function() {
                userMenu.classList.toggle('hidden');
            });

            // Tutup menu jika klik di luar
            document.addEventListener('click', function(event) {
                if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                    userMenu.classList.add('hidden');
                }
            });
        }
    });
</script>