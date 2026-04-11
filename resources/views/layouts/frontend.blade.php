<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Pharmacare</title>

    <!-- Offline Assets -->
    <script src="{{ asset('assets/vendor/tailwind/tailwind-cdn.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/inter/inter.css') }}">

    <style>
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
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            min-height: 100vh;
        }

        /* Shared Styles */
        .top-bar {
            padding: 20px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            margin-bottom: 30px;
            border-radius: 0 0 15px 15px;
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

        .cart-btn {
            background: #E6F3FF;
            color: var(--primary-blue);
            padding: 10px 18px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            height: 50px;
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

        .btn-login-nav {
            padding: 10px 16px;
            border-radius: 30px;
            font-weight: 700;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-login-nav.outline {
            color: var(--text-main);
            background: transparent;
        }

        .btn-login-nav.outline:hover {
            color: var(--primary-blue);
        }

        .btn-login-nav.filled {
            background: #E6F3FF;
            color: var(--primary-blue);
        }

        .btn-login-nav.filled:hover {
            background: #D0E9FF;
        }

        /* Dropdown */
        .user-dropdown {
            position: relative;
        }

        .user-trigger {
            background: #E6F3FF;
            border: none;
            padding: 10px 18px;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--primary-blue);
            height: 50px;
        }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
            min-width: 220px;
            padding: 10px 0;
            z-index: 1000;
            border: 1px solid #E0E0E0;
        }

        .dropdown-menu a,
        .dropdown-menu button {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            text-decoration: none;
            color: #333;
            font-size: 0.95rem;
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
        }

        .dropdown-menu a:hover {
            background: #F5F5F5;
        }

        /* Floating AI Chatbot */
        #chatbot-widget {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
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
            font-size: 30px;
            color: white;
            border: 3px solid white;
            transition: 0.3s;
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
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
            display: none;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .chat-window.open {
            display: flex;
        }

        .chat-header {
            padding: 16px 20px;
            background: linear-gradient(135deg, #0076D6 0%, #00a2ff 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
        }

        .chat-header h4 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-header small {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            opacity: 0.9;
            font-weight: 400;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #4ade80;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 2px rgba(74, 222, 128, 0.2);
            animation: pulse-status 2s infinite;
        }

        @keyframes pulse-status {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 4px rgba(74, 222, 128, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74, 222, 128, 0); }
        }

        .chat-close-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .chat-close-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }

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
        }

        .chat-bubble.bot {
            background: white;
            border-bottom-left-radius: 2px;
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
        }

        .chat-input-area button {
            background: var(--primary-blue);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
        }

        .typing-indicator {
            font-style: italic;
            font-size: 0.8rem;
            color: #888;
        }
    </style>
    @stack('styles')
</head>

<body>

    <div class="container">
        <!-- Top Bar Navigation -->
        <div class="top-bar">
            <a href="{{ route('store.index') }}" style="text-decoration: none;">
                <span
                    style="font-size: 1.5rem; font-weight: 800; color: var(--primary-blue); letter-spacing: -0.025em;">Pharma<span
                        style="color: #333;">care</span></span>
            </a>

            <div class="search-container" style="flex: 1; max-width: 500px; margin: 0 30px;">
                <input type="text" class="search-input" placeholder="Cari obat atau keluhan...">
            </div>

            <div class="top-bar-right" style="display: flex; align-items: center; gap: 20px;">
                @php $cartCount = array_sum(session()->get('cart', [])); @endphp

                @guest
                    <a href="{{ route('store.login') }}" class="btn-login-nav outline">Masuk</a>
                    <a href="{{ route('store.register') }}" class="btn-login-nav filled">Daftar</a>
                @else
                    <div class="user-dropdown" x-data="{ open: false }">
                        <button type="button" class="user-trigger" @click.stop="open = !open">
                            <div
                                style="width: 30px; height: 30px; background: var(--primary-blue); border-radius: 50%; color: white; display:flex; align-items:center; justify-content:center;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                            {{ Auth::user()->name }}
                        </button>
                        <div class="dropdown-menu" x-show="open" x-cloak @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                            <a href="{{ route('account.orders') }}">Pesanan</a>
                            <a href="{{ route('account.profile') }}">Profil</a>
                            <form action="{{ route('store.logout') }}" method="POST">@csrf<button
                                    type="submit">Keluar</button></form>
                        </div>
                    </div>
                @endguest

                <a href="{{ route('cart.index') }}" class="cart-btn" title="Keranjang">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    @if($cartCount > 0) <span
                    style="font-size: 0.9rem; font-weight: 800; margin-left: 4px;">{{ $cartCount }}</span> @endif
                </a>
            </div>
        </div>

        @yield('content')
    </div>

    <!-- Floating AI Chatbot Widget -->
    <div id="chatbot-widget">
        <div class="chat-window" id="chatWindow">
            <div class="chat-header">
                <div>
                    <h4><i class="fas fa-robot"></i> Apoteker Digital</h4>
                    <small><span class="status-dot"></span> Aktif & Siap Membantu 24/7</small>
                </div>
                <button onclick="toggleChat()" class="chat-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="chat-body" id="chatBody">
                <div class="chat-bubble bot">Halo! Saya AI Pharmacare. Apa yang bisa saya bantu hari ini?</div>
            </div>
            <form class="chat-input-area" id="chatbotForm" data-url="{{ route('telemedicine.ai-reply') }}">
                <input type="text" id="chatbotInput" placeholder="Tanya tentang obat..." autocomplete="off">
                <button type="submit">➤</button>
            </form>
        </div>
        <div class="chat-fab" onclick="toggleChat()">⚕️</div>
    </div>

    <script src="{{ asset('assets/vendor/alpinejs/alpine.min.js') }}" defer></script>
    <script src="{{ asset('assets/vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    @vite(['resources/js/frontend/chatbot_widget.js'])
    @stack('scripts')

</body>

</html>