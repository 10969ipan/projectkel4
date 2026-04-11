<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacare - Beli Obat Mudah</title>
    <style>
        /* Desktop-First Design - Pharmacare */
        :root {
            --primary-blue: #0076D6; 
            --bg-color: #F8F9FB; 
            --text-main: #333333;
            --text-muted: #888888;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
        }

        .container {
            max-width: 1200px; /* Desktop width */
            margin: 0 auto;
            background-color: transparent;
            min-height: 100vh;
        }

        /* Top Bar */
        .top-bar {
            padding: 20px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            margin-bottom: 30px;
            border-radius: 0 0 15px 15px;
        }

        .top-bar h1 {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Search Bar Desktop */
        .search-container {
            flex: 1;
            max-width: 500px;
            margin: 0 30px;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 20px;
            border-radius: 30px;
            border: 1px solid #E0E0E0;
            background-color: #F5F5F5;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary-blue);
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(0, 118, 214, 0.1);
        }

        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .cart-btn {
            background: #E6F3FF;
            color: var(--primary-blue);
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
        }

        .cart-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #EF5350;
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Hero Banner */
        .banner-section {
            padding: 0 40px 30px;
        }

        .banner {
            background: linear-gradient(135deg, #0076D6 0%, #00C6FF 100%);
            color: white;
            border-radius: 20px;
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 118, 214, 0.2);
        }

        .banner-text h2 {
            font-size: 2.2rem;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .banner-text p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 25px;
        }

        .btn-banner {
            background-color: white;
            color: var(--primary-blue);
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
        }
        
        .btn-banner:hover { transform: translateY(-2px); }

        /* Telemedicine Desktop Card */
        .telemedicine-shortcut {
            background-color: #fff;
            border: 2px solid #E6F3FF;
            border-radius: 16px;
            margin: 0 40px 40px;
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.3s;
        }

        .telemedicine-shortcut:hover {
            border-color: var(--primary-blue);
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .tele-content { display: flex; align-items: center; gap: 20px; }

        .telemedicine-icon {
            width: 60px;
            height: 60px;
            background-color: #E6F3FF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-blue);
            font-size: 1.5rem;
        }

        .btn-tele {
            background: var(--primary-blue);
            color: white;
            padding: 10px 25px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
        }

        /* Product Grid */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px 20px;
        }

        .section-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .section-header a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
            padding: 0 40px 60px;
        }

        /* Product Card Desktop */
        .product-card {
            background-color: #fff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            border: 1px solid #F0F0F0;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .product-img {
            width: 100%;
            height: 150px;
            margin-bottom: 20px;
            background-color: #FAFAFA;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            transition: background 0.3s;
        }

        .product-card:hover .product-img {
            background-color: #E6F3FF;
        }

        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .product-variant {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 15px;
        }

        .product-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-blue);
            margin-top: auto;
        }

        .btn-add {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            background-color: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 118, 214, 0.3);
            transition: background 0.2s;
        }
        
        .btn-add:hover { background-color: #005FA3; }

        /* Dynamic User Dropdown */
        .user-dropdown { position: relative; }
        .user-trigger {
            background: #E6F3FF;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--primary-blue);
            transition: background 0.2s;
        }
        .user-trigger:hover { background: #CCE6FF; }
        .user-avatar {
            width: 34px; height: 34px;
            background: var(--primary-blue);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 0.9rem;
        }
        .dropdown-menu {
            position: absolute; top: calc(100% + 10px); right: 0;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            min-width: 220px;
            padding: 10px 0;
            z-index: 999;
            display: none;
            border: 1px solid #E0E0E0;
        }
        .dropdown-menu.open { display: block; }
        .dropdown-menu .user-info { padding: 15px 20px; border-bottom: 1px solid #E0E0E0; }
        .dropdown-menu .user-info strong { display: block; font-size: 1rem; color: #333; }
        .dropdown-menu .user-info span { font-size: 0.85rem; color: #888; }
        .dropdown-menu a, .dropdown-menu button {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 20px; text-decoration: none; color: #333;
            font-size: 0.95rem; font-weight: 500; width: 100%;
            background: none; border: none; cursor: pointer; text-align: left;
        }
        .dropdown-menu a:hover, .dropdown-menu button:hover { background: #F5F5F5; color: var(--primary-blue); }
        .dropdown-menu button.logout-btn:hover { color: #EF5350; }
        .dropdown-menu .divider { height: 1px; background: #F0F0F0; margin: 5px 0; }

        /* Guest buttons */
        .btn-login-nav {
            padding: 10px 22px; border-radius: 30px;
            font-weight: 600; text-decoration: none; font-size: 0.95rem;
            transition: all 0.2s;
        }
        .btn-login-nav.outline {
            border: 2px solid var(--primary-blue); color: var(--primary-blue); background: white;
        }
        .btn-login-nav.outline:hover { background: #E6F3FF; }
        .btn-login-nav.filled {
            background: var(--primary-blue); color: white; border: 2px solid var(--primary-blue);
        }
        .btn-login-nav.filled:hover { background: #005FA3; }

        @media (max-width: 768px) {
            .banner, .telemedicine-shortcut { flex-direction: column; text-align: center; gap: 20px; }
            .top-bar { flex-direction: column; gap: 15px; }
        }

        /* 🤖 Floating AI Chatbot Styles */
        #chatbot-widget {
            position: fixed;
            bottom: 30px;
            right: 30px; /* Di pindah ke kanan */
            z-index: 9999;
            font-family: inherit;
        }

        .chat-fab {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0076D6 0%, #0056A3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(0, 118, 214, 0.4);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-size: 30px;
            color: white;
            border: 3px solid white;
        }

        .chat-fab:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .chat-window {
            position: absolute;
            bottom: 85px;
            right: 0;
            width: 380px;
            height: 550px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
            display: none;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.3);
            transform-origin: bottom right;
            transition: all 0.4s ease;
        }

        .chat-window.open {
            display: flex;
            animation: slideIn 0.4s ease forwards;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px) scale(0.8); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .chat-header {
            padding: 20px;
            background: var(--primary-blue);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-header h4 { margin: 0; font-size: 1.1rem; }
        .close-chat { cursor: pointer; font-size: 1.5rem; opacity: 0.8; transition: opacity 0.2s; }
        .close-chat:hover { opacity: 1; }

        .chat-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background: #f8fbff;
        }

        .chat-bubble {
            max-width: 85%;
            padding: 12px 16px;
            border-radius: 15px;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .chat-bubble.bot {
            background: white;
            color: #333;
            align-self: flex-start;
            border-bottom-left-radius: 2px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .chat-bubble.user {
            background: var(--primary-blue);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 2px;
        }

        .chat-input-area {
            padding: 15px 20px;
            background: white;
            display: flex;
            gap: 10px;
            border-top: 1px solid #eee;
        }

        .chat-input-area input {
            flex: 1;
            border: 1px solid #ddd;
            padding: 12px 15px;
            border-radius: 25px;
            outline: none;
            font-size: 0.9rem;
        }

        .chat-input-area button {
            background: var(--primary-blue);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.2s;
        }

        .chat-input-area button:hover { background: #0056A3; }

        .typing-indicator { font-style: italic; font-size: 0.8rem; color: #888; margin-top: 5px; }
    </style>
</head>
<body>

<div class="container">
    <!-- Top Nav Desktop -->
    <div class="top-bar">
        <h1 style="color: var(--primary-blue);">💊 Pharmacare</h1>
        
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Cari obat, suplemen, atau kategori...">
        </div>

        <div class="top-bar-right">
            @php $cartCount = array_sum(session()->get('cart', [])); @endphp

            @guest
                {{-- Tampil jika belum login --}}
                <a href="{{ route('store.login') }}" class="btn-login-nav outline">Masuk</a>
                <a href="{{ route('store.register') }}" class="btn-login-nav filled">Daftar Gratis</a>
            @else
                {{-- Tampil jika sudah login: Avatar + Dropdown --}}
                <div class="user-dropdown" id="userDropdown">
                    <button class="user-trigger" onclick="toggleDropdown()">
                        <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                        {{ Auth::user()->name }}
                        <span style="font-size: 0.75rem; opacity: 0.7;">▾</span>
                    </button>
                    <div class="dropdown-menu" id="dropdownMenu">
                        <div class="user-info">
                            <strong>{{ Auth::user()->name }}</strong>
                            <span>{{ Auth::user()->email }}</span>
                        </div>
                        <a href="{{ route('account.orders') }}">📦 Pesanan</a>
                        <a href="{{ route('account.profile') }}">👤 Akun Saya</a>
                        <a href="{{ route('cart.index') }}">🛒 Keranjang ({{ $cartCount }})</a>
                        {{-- Telemedicine moved to Chatbot Widget --}}
                        <div class="divider"></div>
                        <form action="{{ route('store.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="logout-btn">🚪 Keluar</button>
                        </form>
                    </div>
                </div>
            @endguest

            <a href="{{ route('cart.index') }}" class="cart-btn">
                🛒 Keranjang
                @if($cartCount > 0)
                    <span class="cart-badge">{{ $cartCount }}</span>
                @endif
            </a>
        </div>
    </div>

    <!-- Hero Banner -->
    <div class="banner-section">
        <div class="banner">
            <div class="banner-text">
                <h2>Apotek Digital Terpercaya<br>Beli Obat Makin Mudah</h2>
                <p>Kini hadir fitur Chatbot AI untuk konsultasi obat 24/7 di pojok kiri bawah.</p>
                <a href="#produk-pilihan" class="btn-banner">Belanja Sekarang</a>
            </div>
            <div style="font-size: 6rem; line-height: 1;">💊🌿</div>
        </div>
    </div>

    {{-- Telemedicine Shortcut Removed - Using Floating Chatbot Instead --}}

    <!-- Product Grid -->
    <div class="section-header">
        <h3>Produk Farmasi Populer</h3>
        <a href="#">Lihat Semua Kategori</a>
    </div>

    <div class="product-grid">
        @forelse($items as $item)
        <div class="product-card">
            <a href="{{ route('store.show', $item->id) }}" style="text-decoration: none; color: inherit;">
                <div class="product-img" style="background: {{ $item->requires_prescription ? '#ffebeb' : '#FAFAFA' }}">
                    {{ $item->requires_prescription ? '⚕️' : '💊' }}
                </div>
                <div class="product-title">{{ $item->name }}</div>
                <div class="product-variant">{{ $item->unit->name ?? 'Kemasan Standar' }}</div>
                <div class="product-price">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
            </a>
            <form action="{{ route('cart.add', $item->id) }}" method="POST">
                @csrf
                <input type="hidden" name="qty" value="1">
                <button type="submit" class="btn-add" title="Tambah ke Keranjang">+</button>
            </form>
        </div>
        @empty
        <div style="grid-column: 1/-1; text-align:center; padding: 40px; color: var(--text-muted); background: white; border-radius: 12px;">
            Belum ada data obat, silakan tambahkan item di Admin Simastok.
        </div>
        @endforelse
    </div>
</div>

    <!-- 🤖 Floating AI Chatbot Widget -->
    <div id="chatbot-widget">
        <div class="chat-window" id="chatWindow">
            <div class="chat-header">
                <div>
                    <h4>🤖 Apoteker Digital</h4>
                    <small style="opacity: 0.8;">Online | Siap Membantu</small>
                </div>
                <span class="close-chat" onclick="toggleChat()">&times;</span>
            </div>
            <div class="chat-body" id="chatBody">
                <div class="chat-bubble bot">
                    Halo! Saya Apoteker Digital Pharmacare. Ada yang bisa saya bantu mengenai informasi obat atau keluhan kesehatan Anda? 😊
                </div>
            </div>
            <form class="chat-input-area" id="chatbotForm">
                <input type="text" id="chatbotInput" placeholder="Tanya tentang obat..." autocomplete="off">
                <button type="submit">➤</button>
            </form>
        </div>
        <div class="chat-fab" onclick="toggleChat()" title="Tanya Apoteker AI">
            ⚕️
        </div>
    </div>

</div>

<!-- SweetAlert2 -->
<script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
<script>
    const chatWindow = document.getElementById('chatWindow');
    const chatBody = document.getElementById('chatBody');
    const chatbotForm = document.getElementById('chatbotForm');
    const chatbotInput = document.getElementById('chatbotInput');

    function toggleChat() {
        chatWindow.classList.toggle('open');
        if (chatWindow.classList.contains('open')) {
            chatbotInput.focus();
            scrollToBottom();
        }
    }

    function scrollToBottom() {
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    chatbotForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = chatbotInput.value.trim();
        if (!msg) return;

        // Add user bubble
        appendBubble(msg, 'user');
        chatbotInput.value = '';

        // Add typing indicator
        const typingId = 'typing-' + Date.now();
        const typingElem = document.createElement('div');
        typingElem.id = typingId;
        typingElem.className = 'typing-indicator';
        typingElem.style.padding = '0 10px 10px';
        typingElem.innerText = 'Apoteker sedang mengetik...';
        chatBody.appendChild(typingElem);
        scrollToBottom();

        try {
            const response = await fetch("{{ route('telemedicine.ai-reply') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: msg })
            });

            const data = await response.json();
            
            // Remove typing indicator
            document.getElementById(typingId).remove();

            // Add bot bubble
            appendBubble(data.reply, 'bot');

        } catch (error) {
            document.getElementById(typingId).remove();
            appendBubble('Maaf, terjadi gangguan koneksi. Silakan coba lagi.', 'bot');
        }
    });

    function appendBubble(text, side) {
        const bubble = document.createElement('div');
        bubble.className = `chat-bubble ${side}`;
        bubble.innerHTML = text.replace(/\n/g, '<br>');
        chatBody.appendChild(bubble);
        scrollToBottom();
    }

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

    // Toggle dropdown user
    function toggleDropdown() {
        const menu = document.getElementById('dropdownMenu');
        if (menu) menu.classList.toggle('open');
    }

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            const menu = document.getElementById('dropdownMenu');
            if (menu) menu.classList.remove('open');
        }
    });
</script>
</body>
</html>
