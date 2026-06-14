@extends('layouts.backend')

{{-- Judul Dinamis: Cek apakah ini halaman profil atau edit user biasa --}}
@section('title', request()->routeIs('profile') ? 'Edit Profil Saya' : 'Edit Pengguna')

@section('header')
    <div>
        <nav class="flex mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1">
                @if(request()->routeIs('profile'))
                    <li><a href="{{ route('dashboard') }}" class="text-xs text-blue-500 hover:text-blue-700 font-medium">Dashboard</a></li>
                    <li><span class="text-gray-300 mx-1.5 text-xs">/</span></li>
                    <li><span class="text-xs text-gray-400">Profil Saya</span></li>
                @else
                    <li><a href="{{ route('users.index') }}" class="text-xs text-blue-500 hover:text-blue-700 font-medium">User Staff</a></li>
                    <li><span class="text-gray-300 mx-1.5 text-xs">/</span></li>
                    <li><span class="text-xs text-gray-400">Edit Pengguna</span></li>
                @endif
            </ol>
        </nav>
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">
            {{ request()->routeIs('profile') ? 'Edit Profil Saya' : 'Edit Pengguna' }}
        </h1>
        <p class="text-xs text-gray-400 mt-0.5">
            {{ request()->routeIs('profile') ? 'Perbarui informasi profil pribadi Anda' : 'Perbarui data dan hak akses pengguna' }}
        </p>
    </div>
    <a href="{{ request()->routeIs('profile') ? route('dashboard') : route('users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 py-1.5 px-3 rounded-lg transition">
        <i class="fas fa-arrow-left text-[10px]"></i> Kembali
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6">
            {{-- Form Action Dinamis --}}
            {{-- Jika rute saat ini adalah 'profile', kirim ke 'profile.update' --}}
            {{-- Jika bukan, kirim ke 'users.update' dengan ID user --}}
            <form action="{{ request()->routeIs('profile') ? route('profile.update') : route('users.update', $user->id) }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  x-data="{ 
                      role: '{{ old('role', $user->role) }}',
                      customRoleInput: '',
                      roles: [
                          { key: 'admin', label: 'Administrator', desc: 'Akses penuh ke semua fitur dan konfigurasi sistem', icon: 'fa-user-shield', color: 'purple' },
                          { key: 'staff', label: 'Staf Farmasi', desc: 'Akses terbatas untuk operasional harian apotek', icon: 'fa-user-nurse', color: 'blue' }
                      ],
                      permissions: {{ json_encode(old('menu_permissions', $user->menu_permissions ?? ($user->isAdmin() ? ['dashboard', 'items', 'transactions', 'item_requests', 'store_transactions', 'store_logs', 'store_customers', 'users', 'categories', 'units', 'reports'] : ['dashboard', 'items', 'transactions', 'item_requests', 'store_transactions', 'store_logs']))) }},
                      init() {
                          // If current role is custom, push it to roles list
                          if (this.role !== 'admin' && this.role !== 'staff') {
                              this.roles.push({
                                  key: this.role,
                                  label: this.role.charAt(0).toUpperCase() + this.role.slice(1),
                                  desc: 'Peran khusus yang telah dibuat',
                                  icon: 'fa-user-cog',
                                  color: 'indigo'
                              });
                          }
                          this.$watch('role', value => {
                              if (value === 'admin') {
                                  this.permissions = ['dashboard', 'items', 'transactions', 'item_requests', 'store_transactions', 'store_logs', 'store_customers', 'users', 'categories', 'units', 'reports'];
                              } else if (value === 'staff') {
                                  this.permissions = ['dashboard', 'items', 'transactions', 'item_requests', 'store_transactions', 'store_logs'];
                              }
                          });
                      },
                      addNewRole() {
                          let cleaned = this.customRoleInput.trim();
                          if (!cleaned) return;
                          
                          let key = cleaned.toLowerCase().replace(/[^a-z0-9_-]/g, '_');
                          let exists = this.roles.find(r => r.key === key);
                          if (!exists) {
                              this.roles.push({
                                  key: key,
                                  label: cleaned,
                                  desc: 'Peran kustom baru',
                                  icon: 'fa-user-cog',
                                  color: 'indigo'
                              });
                              this.role = key;
                          } else {
                              this.role = exists.key;
                          }
                          this.customRoleInput = '';
                      }
                  }">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" id="name" required value="{{ old('name', $user->name) }}"
                                class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg px-3 py-2.5 border transition-all">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Alamat Email</label>
                            <input type="email" name="email" id="email" required
                                value="{{ old('email', $user->email) }}"
                                class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg px-3 py-2.5 border transition-all">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-bold text-gray-700 mb-1">Password (Kosongkan jika tidak diubah)</label>
                            <input type="password" name="password" id="password"
                                class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg px-3 py-2.5 border transition-all">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Foto Profil Saat Ini</label>
                            <div class="flex items-center space-x-4">
                                <div class="relative h-12 w-12 flex-shrink-0">
                                    <div class="absolute inset-0 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border border-primary-200">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    @if($user->profile_photo && ($user->isAdmin() || $user->isStaff() || $user->role !== 'customer'))
                                        <img class="absolute inset-0 h-12 w-12 rounded-full object-cover border border-white" 
                                            src="{{ asset('storage/' . $user->profile_photo) }}" 
                                            alt="{{ $user->name }}"
                                            onerror="this.style.display='none'"
                                            loading="lazy">
                                    @endif
                                </div>
                                <div class="flex-grow">
                                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none py-1.5 px-2">
                                    <p class="mt-1 text-xs text-gray-400">Format: PNG, JPG, JPEG (Maks. 2MB)</p>
                                </div>
                            </div>
                            @error('profile_photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    @if(!request()->routeIs('profile') && auth()->id() !== $user->id)
                        <!-- Pilihan Peran (Role) -->
                        <div class="border-t border-gray-100 pt-6">
                            <label class="block text-sm font-bold text-gray-800 mb-3">Peran Pengguna (Role)</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template x-for="r in roles" :key="r.key">
                                    <label :class="role === r.key ? 'border-primary-500 bg-primary-50/20 ring-2 ring-primary-500' : 'border-gray-200 bg-white'" 
                                           class="relative flex p-4 rounded-xl cursor-pointer hover:border-primary-300 transition-all duration-300 border shadow-sm">
                                        <input type="radio" name="role" :value="r.key" x-model="role" class="sr-only" required>
                                        <div class="flex items-center w-full">
                                            <div :class="role === r.key ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-500'" 
                                                 class="p-3 rounded-lg mr-4 transition-colors duration-200">
                                                <i :class="'fas ' + r.icon" class="text-xl"></i>
                                            </div>
                                            <div class="flex-grow">
                                                <span class="block text-sm font-bold text-gray-900" x-text="r.label"></span>
                                                <span class="block text-xs text-gray-500 mt-0.5" x-text="r.desc"></span>
                                            </div>
                                            <div class="flex-shrink-0 ml-2">
                                                <div :class="role === r.key ? 'border-primary-500 bg-primary-600' : 'border-gray-300 bg-white'" 
                                                     class="h-5 w-5 rounded-full border flex items-center justify-center transition-all duration-200">
                                                    <div class="h-2 w-2 rounded-full bg-white" x-show="role === r.key"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </template>
                            </div>
                            
                            <!-- Tambah Role Baru Field -->
                            <div class="mt-4 p-4 bg-gray-50 rounded-2xl border border-gray-200/60 max-w-md">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1.5">Tambah Peran Kustom Baru</label>
                                <div class="flex space-x-2">
                                    <input type="text" x-model="customRoleInput" @keydown.enter.prevent="addNewRole()" placeholder="Masukkan nama peran (contoh: Manager)" 
                                           class="flex-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm text-sm border-gray-300 rounded-lg px-3 py-2 border">
                                    <button type="button" @click="addNewRole()" 
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 transition-all">
                                        Tambah
                                    </button>
                                </div>
                            </div>

                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hak Akses Navigasi Menu -->
                        <div class="border-t border-gray-100 pt-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800">Hak Akses Navigasi Menu</label>
                                    <p class="text-xs text-gray-500 mt-0.5">Tentukan menu apa saja yang dapat diakses oleh user ini.</p>
                                </div>
                                <div class="flex items-center space-x-2 self-start sm:self-center">
                                    <button type="button" @click="permissions = ['dashboard', 'items', 'transactions', 'item_requests', 'store_transactions', 'store_logs', 'store_customers', 'users', 'categories', 'units', 'reports']" 
                                            class="inline-flex items-center px-2.5 py-1 border border-gray-300 rounded-md text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                                        Pilih Semua
                                    </button>
                                    <button type="button" @click="permissions = []" 
                                            class="inline-flex items-center px-2.5 py-1 border border-red-200 rounded-md text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 transition-all">
                                        Hapus Semua
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                <!-- Dashboard -->
                                <label class="relative flex items-center justify-between p-3.5 rounded-xl cursor-pointer transition-all duration-200 border shadow-sm"
                                       :class="permissions.includes('dashboard') ? 'border-primary-500 bg-primary-50/10' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="checkbox" name="menu_permissions[]" value="dashboard" x-model="permissions" class="sr-only">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg transition-colors duration-200"
                                             :class="permissions.includes('dashboard') ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-500'">
                                            <i class="fas fa-chart-line text-sm w-4 text-center"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900">Dashboard</span>
                                    </div>
                                    <div>
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-all duration-200"
                                             :class="permissions.includes('dashboard') ? 'border-primary-500 bg-primary-600 text-white' : 'border-gray-300 bg-white'">
                                            <i class="fas fa-check text-[10px]" x-show="permissions.includes('dashboard')"></i>
                                        </div>
                                    </div>
                                </label>

                                <!-- Obat -->
                                <label class="relative flex items-center justify-between p-3.5 rounded-xl cursor-pointer transition-all duration-200 border shadow-sm"
                                       :class="permissions.includes('items') ? 'border-primary-500 bg-primary-50/10' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="checkbox" name="menu_permissions[]" value="items" x-model="permissions" class="sr-only">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg transition-colors duration-200"
                                             :class="permissions.includes('items') ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-500'">
                                            <i class="fas fa-pills text-sm w-4 text-center"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900">Obat</span>
                                    </div>
                                    <div>
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-all duration-200"
                                             :class="permissions.includes('items') ? 'border-primary-500 bg-primary-600 text-white' : 'border-gray-300 bg-white'">
                                            <i class="fas fa-check text-[10px]" x-show="permissions.includes('items')"></i>
                                        </div>
                                    </div>
                                </label>

                                <!-- Mutasi Stok -->
                                <label class="relative flex items-center justify-between p-3.5 rounded-xl cursor-pointer transition-all duration-200 border shadow-sm"
                                       :class="permissions.includes('transactions') ? 'border-primary-500 bg-primary-50/10' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="checkbox" name="menu_permissions[]" value="transactions" x-model="permissions" class="sr-only">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg transition-colors duration-200"
                                             :class="permissions.includes('transactions') ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-500'">
                                            <i class="fas fa-exchange-alt text-sm w-4 text-center"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900">Mutasi Stok</span>
                                    </div>
                                    <div>
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-all duration-200"
                                             :class="permissions.includes('transactions') ? 'border-primary-500 bg-primary-600 text-white' : 'border-gray-300 bg-white'">
                                            <i class="fas fa-check text-[10px]" x-show="permissions.includes('transactions')"></i>
                                        </div>
                                    </div>
                                </label>

                                <!-- Permintaan Obat -->
                                <label class="relative flex items-center justify-between p-3.5 rounded-xl cursor-pointer transition-all duration-200 border shadow-sm"
                                       :class="permissions.includes('item_requests') ? 'border-primary-500 bg-primary-50/10' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="checkbox" name="menu_permissions[]" value="item_requests" x-model="permissions" class="sr-only">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg transition-colors duration-200"
                                             :class="permissions.includes('item_requests') ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-500'">
                                            <i class="fas fa-file-medical text-sm w-4 text-center"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900">Permintaan Obat</span>
                                    </div>
                                    <div>
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-all duration-200"
                                             :class="permissions.includes('item_requests') ? 'border-primary-500 bg-primary-600 text-white' : 'border-gray-300 bg-white'">
                                            <i class="fas fa-check text-[10px]" x-show="permissions.includes('item_requests')"></i>
                                        </div>
                                    </div>
                                </label>

                                <!-- Transaksi Toko -->
                                <label class="relative flex items-center justify-between p-3.5 rounded-xl cursor-pointer transition-all duration-200 border shadow-sm"
                                       :class="permissions.includes('store_transactions') ? 'border-primary-500 bg-primary-50/10' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="checkbox" name="menu_permissions[]" value="store_transactions" x-model="permissions" class="sr-only">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg transition-colors duration-200"
                                             :class="permissions.includes('store_transactions') ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-500'">
                                            <i class="fas fa-shopping-cart text-sm w-4 text-center"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900">Transaksi Toko</span>
                                    </div>
                                    <div>
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-all duration-200"
                                             :class="permissions.includes('store_transactions') ? 'border-primary-500 bg-primary-600 text-white' : 'border-gray-300 bg-white'">
                                            <i class="fas fa-check text-[10px]" x-show="permissions.includes('store_transactions')"></i>
                                        </div>
                                    </div>
                                </label>

                                <!-- Log Transaksi -->
                                <label class="relative flex items-center justify-between p-3.5 rounded-xl cursor-pointer transition-all duration-200 border shadow-sm"
                                       :class="permissions.includes('store_logs') ? 'border-primary-500 bg-primary-50/10' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="checkbox" name="menu_permissions[]" value="store_logs" x-model="permissions" class="sr-only">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg transition-colors duration-200"
                                             :class="permissions.includes('store_logs') ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-500'">
                                            <i class="fas fa-history text-sm w-4 text-center"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900">Log Transaksi</span>
                                    </div>
                                    <div>
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-all duration-200"
                                             :class="permissions.includes('store_logs') ? 'border-primary-500 bg-primary-600 text-white' : 'border-gray-300 bg-white'">
                                            <i class="fas fa-check text-[10px]" x-show="permissions.includes('store_logs')"></i>
                                        </div>
                                    </div>
                                </label>

                                <!-- Manajemen Pelanggan -->
                                <label class="relative flex items-center justify-between p-3.5 rounded-xl cursor-pointer transition-all duration-200 border shadow-sm"
                                       :class="permissions.includes('store_customers') ? 'border-primary-500 bg-primary-50/10' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="checkbox" name="menu_permissions[]" value="store_customers" x-model="permissions" class="sr-only">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg transition-colors duration-200"
                                             :class="permissions.includes('store_customers') ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-500'">
                                            <i class="fas fa-users text-sm w-4 text-center"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900">Pelanggan</span>
                                    </div>
                                    <div>
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-all duration-200"
                                             :class="permissions.includes('store_customers') ? 'border-primary-500 bg-primary-600 text-white' : 'border-gray-300 bg-white'">
                                            <i class="fas fa-check text-[10px]" x-show="permissions.includes('store_customers')"></i>
                                        </div>
                                    </div>
                                </label>

                                <!-- User Staff -->
                                <label class="relative flex items-center justify-between p-3.5 rounded-xl cursor-pointer transition-all duration-200 border shadow-sm"
                                       :class="permissions.includes('users') ? 'border-primary-500 bg-primary-50/10' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="checkbox" name="menu_permissions[]" value="users" x-model="permissions" class="sr-only">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg transition-colors duration-200"
                                             :class="permissions.includes('users') ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-500'">
                                            <i class="fas fa-users-cog text-sm w-4 text-center"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900">User Staff</span>
                                    </div>
                                    <div>
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-all duration-200"
                                             :class="permissions.includes('users') ? 'border-primary-500 bg-primary-600 text-white' : 'border-gray-300 bg-white'">
                                            <i class="fas fa-check text-[10px]" x-show="permissions.includes('users')"></i>
                                        </div>
                                    </div>
                                </label>

                                <!-- Kategori -->
                                <label class="relative flex items-center justify-between p-3.5 rounded-xl cursor-pointer transition-all duration-200 border shadow-sm"
                                       :class="permissions.includes('categories') ? 'border-primary-500 bg-primary-50/10' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="checkbox" name="menu_permissions[]" value="categories" x-model="permissions" class="sr-only">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg transition-colors duration-200"
                                             :class="permissions.includes('categories') ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-500'">
                                            <i class="fas fa-tags text-sm w-4 text-center"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900">Kategori</span>
                                    </div>
                                    <div>
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-all duration-200"
                                             :class="permissions.includes('categories') ? 'border-primary-500 bg-primary-600 text-white' : 'border-gray-300 bg-white'">
                                            <i class="fas fa-check text-[10px]" x-show="permissions.includes('categories')"></i>
                                        </div>
                                    </div>
                                </label>

                                <!-- Satuan Ukur -->
                                <label class="relative flex items-center justify-between p-3.5 rounded-xl cursor-pointer transition-all duration-200 border shadow-sm"
                                       :class="permissions.includes('units') ? 'border-primary-500 bg-primary-50/10' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="checkbox" name="menu_permissions[]" value="units" x-model="permissions" class="sr-only">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg transition-colors duration-200"
                                             :class="permissions.includes('units') ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-500'">
                                            <i class="fas fa-balance-scale text-sm w-4 text-center"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900">Satuan Ukur</span>
                                    </div>
                                    <div>
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-all duration-200"
                                             :class="permissions.includes('units') ? 'border-primary-500 bg-primary-600 text-white' : 'border-gray-300 bg-white'">
                                            <i class="fas fa-check text-[10px]" x-show="permissions.includes('units')"></i>
                                        </div>
                                    </div>
                                </label>

                                <!-- Laporan Medik -->
                                <label class="relative flex items-center justify-between p-3.5 rounded-xl cursor-pointer transition-all duration-200 border shadow-sm"
                                       :class="permissions.includes('reports') ? 'border-primary-500 bg-primary-50/10' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="checkbox" name="menu_permissions[]" value="reports" x-model="permissions" class="sr-only">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg transition-colors duration-200"
                                             :class="permissions.includes('reports') ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-500'">
                                            <i class="fas fa-file-medical-alt text-sm w-4 text-center"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900">Laporan Medik</span>
                                    </div>
                                    <div>
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-all duration-200"
                                             :class="permissions.includes('reports') ? 'border-primary-500 bg-primary-600 text-white' : 'border-gray-300 bg-white'">
                                            <i class="fas fa-check text-[10px]" x-show="permissions.includes('reports')"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button type="submit"
                            class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 transform hover:scale-[1.02]">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
