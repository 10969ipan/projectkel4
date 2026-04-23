<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Pharmacare</title>

    <!-- Optimization: Preload Critical Assets -->
    <link rel="preload" href="{{ asset('assets/vendor/tailwind/tailwind-cdn.js') }}" as="script">
    <link rel="preload" href="{{ asset('assets/vendor/fontawesome/all.min.css') }}" as="style">
    <link rel="preload" href="{{ asset('assets/vendor/fonts/inter/inter.css') }}" as="style">

    <!-- Offline Assets -->
    <script src="{{ asset('assets/vendor/tailwind/tailwind-cdn.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/inter/inter.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/branding/pharmacare-logo.png') }}">

    <style>
        :root {
            --primary-blue: #0076D6;
            --secondary-blue: #005FA3;
            --accent-green: #059669;
            --bg-glass: rgba(255, 255, 255, 0.85);
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        html, body {
            height: 100%;
        }

        body {
            background-color: #F8FAFC;
            color: var(--text-main);
            overflow-x: hidden;
            animation: fadeInPage 0.6s ease-out;
            display: flex;
            flex-direction: column;
        }

        @keyframes fadeInPage {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- Chatbot Greeting Bubble Styling --- */
        #chat-greeting-bubble {
            position: fixed;
            bottom: 105px;
            right: 30px;
            background: linear-gradient(135deg, #0076D6 0%, #00a2ff 100%);
            color: white;
            padding: 15px 45px 15px 20px;
            border-radius: 20px 20px 5px 20px;
            box-shadow: 0 15px 35px rgba(0, 118, 214, 0.2);
            z-index: 999998;
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1.4;
            max-width: 250px;
            animation: bubbleFadeIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
            display: none;
        }

        #chat-greeting-bubble::after {
            content: '';
            position: absolute;
            bottom: -10px;
            right: 25px;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-top: 10px solid #00a2ff;
        }

        .bubble-close {
            position: absolute;
            top: 8px;
            right: 12px;
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.7rem;
            transition: 0.2s;
        }

        .bubble-close:hover { background: rgba(255,255,255,0.4); }

        @keyframes bubbleFadeIn {
            from { opacity: 0; transform: translateY(20px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .chat-fab-card {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: white;
            padding: 8px;
            border-radius: 22px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            cursor: pointer;
            z-index: 999998;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid #f1f5f9;
        }

        .chat-fab-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .chat-fab-logo {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #0076D6 0%, #00a2ff 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.4rem;
            box-shadow: 0 5px 15px rgba(0, 118, 214, 0.2);
        }

        .chat-fab-text {
            padding-right: 15px;
        }

        .chat-fab-text h5 { margin: 0; font-size: 0.95rem; font-weight: 800; color: #1e293b; }
        .chat-fab-text p { margin: 0; font-size: 0.75rem; color: #94a3b8; font-weight: 500; }

        /* Minimalist Transitions */
        a, button, .card, .btn, .nav-link, input, select {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        /* Shared Styles */
        .top-bar {
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            padding: 15px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
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
            color: var(--primary-blue);
            background: transparent;
            border: 1.5px solid var(--primary-blue);
            padding: 10px 22px;
        }

        .btn-login-nav.outline:hover {
            background: var(--primary-blue);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 118, 214, 0.2);
        }

        .btn-login-nav.filled {
            background: #E6F3FF;
            color: var(--primary-blue);
        }

        .btn-login-nav.filled:hover {
            background: #D0E9FF;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 118, 214, 0.1);
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

        .user-trigger:hover {
            background: #D0E9FF;
            transform: translateY(-1px);
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
            transform-origin: top right;
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

        /* Search Autocomplete */
        .search-container {
            position: relative;
        }
        
        .search-results-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: white;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.06);
            z-index: 9999;
            border: 1px solid #f0f0f0;
            max-height: 360px;
            overflow-y: auto;
            display: none;
            padding: 6px;
        }

        .search-result-item {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            gap: 12px;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            transition: background 0.15s;
            border-radius: 10px;
        }

        .search-result-item:hover {
            background: #f0f7ff;
        }

        .search-result-img {
            width: 44px;
            height: 44px;
            min-width: 44px;
            background: #F5F7FA;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid #eee;
        }

        .search-result-img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .search-result-info {
            flex: 1;
            min-width: 0;
        }

        .search-result-name {
            font-size: 0.88rem;
            font-weight: 700;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }

        .search-result-meta {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .search-result-price {
            font-size: 0.85rem;
            font-weight: 800;
            color: #0076D6;
            flex-shrink: 0;
            white-space: nowrap;
        }

        /* Global Hover Animation for Cards */
        .product-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 118, 214, 0.08) !important;
            border-color: rgba(0, 118, 214, 0.2) !important;
        }
        .product-card:hover .quick-view-btn {
            opacity: 1 !important;
            transform: translateY(-5px);
        }
            flex: 1;
        }

        .search-result-name {
            font-weight: 700;
            font-size: 0.95rem;
            color: #1a1a1a;
            margin-bottom: 2px;
        }

        .search-result-price {
            font-size: 0.85rem;
            color: var(--primary-blue);
            font-weight: 600;
        }

        /* --- Auth Modal Styling --- */
        .auth-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 24, 46, 0.4);
            backdrop-filter: blur(8px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-modal-content {
            background: white;
            width: 100%;
            max-width: 480px;
            border-radius: 28px;
            padding: 40px;
            position: relative;
            box-shadow: 0 25px 70px rgba(0,0,0,0.15);
            animation: modalScaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes modalScaleUp {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .auth-modal-close {
            position: absolute;
            top: 25px;
            right: 25px;
            background: #f1f5f9;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            transition: 0.2s;
        }
        .auth-modal-close:hover { background: #e2e8f0; color: #1e293b; transform: rotate(90deg); }

        .auth-tabs {
            display: flex;
            gap: 10px;
            background: #f1f5f9;
            padding: 5px;
            border-radius: 14px;
            margin-bottom: 30px;
        }

        .auth-tab-btn {
            flex: 1;
            padding: 10px;
            border: none;
            background: transparent;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
            color: #64748b;
        }

        .auth-tab-btn.active {
            background: white;
            color: var(--primary-blue);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        /* Form styling inside modal (matching existing) */
        .modal-form-header { text-align: center; margin-bottom: 25px; }
        .modal-form-header h2 { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 6px; }
        .modal-form-header p { color: #64748b; font-size: 0.9rem; }

        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 8px; }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            outline: none;
            font-size: 0.95rem;
            transition: 0.2s;
            background: #f8fafc;
        }
        .form-input:focus { border-color: var(--primary-blue); background: white; box-shadow: 0 0 0 4px rgba(0, 118, 214, 0.08); }

        .btn-auth-submit {
            width: 100%;
            background: var(--primary-blue);
            color: white;
            padding: 14px;
            border-radius: 12px;
            border: none;
            font-weight: 800;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.2s;
        }
        .btn-auth-submit:hover { background: #0066BA; transform: translateY(-1px); box-shadow: 0 10px 20px rgba(0, 118, 214, 0.2); }

        /* Google Button in Modal */
        .modal-google-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0;
            color: #94a3b8;
            font-size: 0.82rem;
        }
        .modal-google-divider::before,
        .modal-google-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .modal-btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 12px 20px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            color: #334155;
            font-size: 0.92rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s;
            cursor: pointer;
        }
        .modal-btn-google:hover {
            border-color: #4285F4;
            background: #F0F6FF;
            box-shadow: 0 4px 15px rgba(66, 133, 244, 0.15);
            transform: translateY(-1px);
        }
        .modal-btn-google:active { transform: translateY(0); }

        /* Footer Styles */
        .site-footer {
            background: #1e293b;
            color: #f1f5f9;
            padding: 80px 0 40px;
            border-top: 1px solid rgba(255,255,255,0.05);
            width: 100%;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1.2fr 1fr;
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 40px;
        }
        .footer-col h4 {
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 25px;
            color: white;
            position: relative;
            padding-bottom: 10px;
        }
        .footer-col h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 30px;
            height: 2px;
            background: var(--primary-blue);
        }
        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.95rem;
            transition: 0.2s;
        }
        .footer-links a:hover { color: var(--primary-blue); padding-left: 5px; }
        .footer-info {
            color: #94a3b8;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .social-icon {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }
        .social-icon:hover { background: var(--primary-blue); transform: translateY(-3px); }
        .footer-bottom {
            max-width: 1200px;
            margin: 60px auto 0;
            padding: 30px 40px 0;
            border-top: 1px solid rgba(255,255,255,0.05);
            text-align: center;
            color: #64748b;
            font-size: 0.85rem;
        }
        @media (max-width: 968px) {
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 640px) {
            .footer-grid { grid-template-columns: 1fr; }
        }

        /* --- Cart Dropdown Styling --- */
        .cart-dropdown-container {
            position: relative;
            z-index: 1000;
        }

        .cart-dropdown-menu {
            position: absolute;
            top: calc(100% + 15px);
            right: 0;
            width: 360px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.12);
            padding: 20px;
            display: flex;
            flex-direction: column;
            border: 1px solid #f1f5f9;
            transform-origin: top right;
        }

        .cart-dropdown-menu::before {
            content: '';
            position: absolute;
            top: -6px;
            right: 20px;
            width: 12px;
            height: 12px;
            background: white;
            transform: rotate(45deg);
            border-left: 1px solid #f1f5f9;
            border-top: 1px solid #f1f5f9;
        }

        .cart-dropdown-body {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .cart-dropdown-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f8fafc;
        }
        .cart-dropdown-item:last-child { border-bottom: none; }

        .cart-dropdown-img {
            width: 50px;
            height: 50px;
            background: #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .cart-dropdown-info { flex: 1; }
        .cart-dropdown-name { font-weight: 700; font-size: 0.9rem; color: #1e293b; margin-bottom: 2px; }
        .cart-dropdown-price { color: var(--primary-blue); font-weight: 600; font-size: 0.8rem; }

        .cart-dropdown-footer {
            padding-top: 15px;
            border-top: 1px solid #f1f5f9;
        }

        .btn-checkout-mini {
            width: 100%;
            background: var(--primary-blue);
            color: white;
            padding: 14px;
            border-radius: 12px;
            border: none;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: 0.2s;
        }
        .btn-checkout-mini:hover { background: #0066BA; transform: translateY(-1px); }

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

        .typing-indicator { font-style: italic; font-size: 0.8rem; color: #888; }

        /* --- Quick View Modal --- */
        .qv-modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(14px); z-index: 10000; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .qv-modal-content { 
            background: white; width: 100%; max-width: 900px; border-radius: 24px; 
            display: grid; grid-template-columns: 1fr 1fr; overflow: hidden; position: relative; 
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            will-change: transform, opacity;
        }
        .qv-modal-content > div { animation: qv-content-fade 0.6s cubic-bezier(0.23, 1, 0.32, 1) both; }
        @keyframes qv-content-fade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .qv-left { background: #f8fafc; display: flex; align-items: center; justify-content: center; padding: 40px; }
        .qv-right { padding: 40px; display: flex; flex-direction: column; }
        .qv-close { position: absolute; top: 20px; right: 20px; width: 36px; height: 36px; border-radius: 50%; border: none; background: #f1f5f9; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; transition: 0.2s; }
        .qv-close:hover { background: #fee2e2; color: #ef4444; }
        .qv-category { font-weight: 700; color: var(--primary-blue); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px; }
        .qv-title { font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 15px; line-height: 1.2; }
        .qv-price { font-size: 1.75rem; font-weight: 800; color: var(--primary-blue); margin-bottom: 25px; }
        .qv-desc { color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 30px; flex: 1; }
        .qv-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; padding: 20px; background: #f8fafc; border-radius: 16px; }
        .qv-meta-item { display: flex; flex-direction: column; gap: 4px; }
        .qv-meta-label { font-size: 0.75rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; }
        .qv-meta-val { font-size: 1rem; color: #1e293b; font-weight: 700; }

        @media (max-width: 768px) {
            .qv-modal-content { grid-template-columns: 1fr; max-height: 90vh; overflow-y: auto; }
            .qv-left { height: 250px; padding: 20px; }
            .qv-right { padding: 30px; }
        }

        /* Hover effect for grid items to show eye icon */
        .product-card:hover .quick-view-btn { opacity: 1 !important; transform: translateY(-5px); }
        
        @media (max-width: 1024px) {
            .quick-view-btn { opacity: 1 !important; }
        }
        /* SweetAlert2 Toast Adjustment */
        .swal2-container.swal2-top-end, .swal2-container.swal2-top-right {
            top: 70px !important;
            right: 20px !important;
        }
    </style>
    <script>
        @php
            $initialCart = session()->get('cart', []);
            $initialItems = [];
            $initialTotal = 0;
            $initialCount = 0;
            
            foreach($initialCart as $id => $details) {
                $item = \App\Models\Item::find($details['id']);
                if($item) {
                    $unitPrice = $item->price;
                    if($details['type'] === 'subscription') $unitPrice *= 0.9;
                    $subtotal = $unitPrice * $details['qty'];
                    $initialTotal += $subtotal;
                    $initialCount += $details['qty'];
                    $initialItems[] = [
                        'id' => $item->id,
                        'name' => $item->name,
                        'price' => $unitPrice,
                        'qty' => $details['qty'],
                        'subtotal' => $subtotal,
                        'image_path' => $item->image_path
                    ];
                }
            }
        @endphp

        // Global State Definition for Early Loading
        window.PharmacareState = {
            isAuthenticated: {{ Auth::check() ? 'true' : 'false' }},
            showAuthModal: false,
            authTab: 'login',
            showCartDropdown: false,
            showQuickView: false,
            cartItems: @json($initialItems),
            cartTotal: {{ $initialTotal }},
            cartCount: {{ $initialCount }},
            currentBasePrice: 0,
            notifications: [],
            unreadNotificationsCount: 0,
            showNotifications: false,
            showWellnessModal: false,
            activeWellnessArticle: {},

            init() {
                console.log('Alpine Storefront State Initialized.');
                window.StoreUI = this;

                // Auto-refresh cart on page load to ensure sync
                this.refreshCart();

                // Handle redirect with auth trigger
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('auth') === 'login') {
                    this.showAuthModal = true;
                    this.authTab = 'login';
                } else if (urlParams.get('auth') === 'register') {
                    this.showAuthModal = true;
                    this.authTab = 'register';
                }
            },

            async refreshCart() {
                try {
                    const res = await fetch('{{ route('cart.summary') }}');
                    const data = await res.json();
                    this.cartItems = data.items;
                    this.cartTotal = data.grand_total;
                    this.cartCount = data.cart_count;
                } catch (e) { console.error('Cart refresh error:', e); }
            },
            
            async fetchNotifications() {
                try {
                    const res = await fetch('{{ route('notifications.index') }}');
                    const data = await res.json();
                    this.notifications = data.notifications;
                    this.unreadNotificationsCount = data.unreadCount;
                } catch (e) { console.error('Notification fetch error:', e); }
            },

            async markNotificationsAsRead() {
                if (this.unreadNotificationsCount === 0) return;
                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                    await fetch('{{ route('notifications.read') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf }
                    });
                    this.unreadNotificationsCount = 0;
                } catch (e) { console.error('Notification read error:', e); }
            },

            openWellnessModal(article) {
                this.activeWellnessArticle = article;
                this.showWellnessModal = true;
            },

            async addToCart(itemId, qty = 1) {
                if (!this.isAuthenticated) {
                    this.showAuthModal = true;
                    this.authTab = 'login';
                    window.showToast('warning', 'Silakan masuk terlebih dahulu untuk berbelanja');
                    return;
                }

                console.log('Cart Action (Optimistic): Adding Item ID', itemId);
                
                // Optimistic UI Update: Increment counter immediately
                this.cartCount += parseInt(qty);
                window.showToast('success', 'Ditambahkan ke keranjang');

                const url = `{{ url('/cart/add-ajax') }}/${itemId}?qty=${qty}&type=once&interval=30`;
                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (res.status === 401) {
                        window.showToast('warning', 'Silakan login terlebih dahulu bro.');
                        this.showAuthModal = true;
                        this.cartCount -= parseInt(qty); // Revert optimistic update
                        return;
                    }

                    const data = await res.json();
                    if (data.success) {
                        // Sync real data from server
                        this.cartItems = data.items;
                        this.cartTotal = data.grand_total;
                        this.cartCount = data.cart_count;
                    } else {
                        this.cartCount -= parseInt(qty); // Revert optimistic update
                        window.showToast('error', data.message);
                    }
                } catch (e) {
                    console.error('Cart Error:', e);
                    this.cartCount -= parseInt(qty); // Revert optimistic update
                    window.showToast('error', 'Gagal menambah ke keranjang.');
                }
            },

            openQuickView(data) {
                console.log('Alpine: Opening Quick View for', data.name);
                this.currentBasePrice = parseFloat(data.price) || 0;
                this.showQuickView = true;
                
                this.$nextTick(() => {
                    const modal = document.getElementById('quick-view-modal-content');
                    if (!modal) return;
                    
                    modal.querySelector('#qv-name').innerText = data.name;
                    modal.querySelector('#qv-category').innerText = data.category;
                    modal.querySelector('#qv-price').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(data.price);
                    modal.querySelector('#qv-unit').innerText = data.unit || 'Pcs';
                    modal.querySelector('#qv-stock').innerText = `Stok: ${data.stock}`;
                    
                    // Logic for description
                    let desc = data.description;
                    if (!desc || desc.trim() === '' || desc.trim() === 'Tidak ada deskripsi.') {
                        desc = `${data.name} adalah solusi kesehatan utama untuk kategori ${data.category || 'Medis'}. Produk ini diproses dengan standar kualitas tinggi untuk efektivitas maksimal.`;
                    }
                    modal.querySelector('#qv-description').innerText = desc;

                    const imgContainer = modal.querySelector('#qv-img-container');
                    if (imgContainer && data.image_url) {
                        imgContainer.innerHTML = `<img src="${data.image_url}" style="width: 100%; height: 100%; object-fit: contain;">`;
                    }
                });
            }
        };
    </script>
</head>

<body>

    <div class="container" x-data="PharmacareState" 
         @open-quickview.window="openQuickView($event.detail)"
         @add-to-cart.window="addToCart($event.detail.id, $event.detail.qty)"
         @open-wellness.window="openWellnessModal($event.detail)">
        <!-- Top Bar Navigation -->
        <div class="top-bar">
            <a href="{{ route('store.index') }}" style="text-decoration: none;">
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--primary-blue); letter-spacing: -0.025em;">Pharma<span style="color: #333;">care</span></span>
            </a>

            <div class="search-container" style="flex: 1; max-width: 500px; margin: 0 30px;">
                <input type="text" class="search-input" id="main-search" placeholder="Cari obat..." autocomplete="off">
                <div id="search-results" class="search-results-dropdown"></div>
            </div>

            <div class="top-bar-right" style="display: flex; align-items: center; gap: 20px;">
                @php $cartCount = array_sum(session()->get('cart', [])); @endphp

                @guest
                    @if(request()->routeIs('store.index'))
                        <a href="javascript:void(0)" @click.prevent="showAuthModal = true; authTab = 'login'" class="btn-login-nav outline">Masuk</a>
                        <a href="javascript:void(0)" @click.prevent="showAuthModal = true; authTab = 'register'" class="btn-login-nav filled">Daftar</a>
                    @else
                        <a href="{{ route('store.login') }}" class="btn-login-nav outline">Masuk</a>
                        <a href="{{ route('store.register') }}" class="btn-login-nav filled">Daftar</a>
                    @endif
                @else
                    <!-- Notifications Bell -->
                    <div class="relative" style="position: relative; z-index: 1000;" x-init="fetchNotifications()">
                        <button type="button" @click.stop="showNotifications = !showNotifications; if(showNotifications) markNotificationsAsRead()" 
                                class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors relative"
                                style="width: 50px; height: 50px; background: #E6F3FF; color: var(--primary-blue); border-radius: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                            <i class="fas fa-bell text-lg"></i>
                            <template x-if="unreadNotificationsCount > 0">
                                <span class="absolute" 
                                      style="position: absolute; top: 10px; right: 12px; height: 10px; width: 10px; background-color: #EF5350; border-radius: 50%; border: 2px solid #E6F3FF;"></span>
                            </template>
                        </button>

                        <!-- Notifications Dropdown -->
                        <div x-show="showNotifications" x-cloak @click.away="showNotifications = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             style="position: absolute; top: calc(100% + 15px); right: 0; width: 320px; background: white; border-radius: 20px; box-shadow: 0 15px 50px rgba(0,0,0,0.12); border: 1px solid #f1f5f9; padding: 20px; transform-origin: top right;">
                            
                            <div style="font-weight: 800; font-size: 1.1rem; margin-bottom: 15px; color: #1e293b; display: flex; justify-content: space-between; align-items: center;">
                                <span>Notifikasi</span>
                                <span x-show="unreadNotificationsCount > 0" class="text-xs px-2 py-1 bg-red-100 text-red-600 rounded-full" x-text="`${unreadNotificationsCount} Baru`"></span>
                            </div>

                            <div style="max-height: 350px; overflow-y: auto;">
                                <template x-if="notifications.length === 0">
                                    <div style="text-align: center; padding: 30px 10px; color: #94a3b8;">
                                        <i class="fas fa-bell-slash text-3xl mb-3 opacity-20"></i>
                                        <p style="font-size: 0.9rem;">Belum ada notifikasi baru.</p>
                                    </div>
                                </template>

                                <template x-for="notif in notifications" :key="notif.id">
                                    <div style="padding: 12px; border-radius: 12px; margin-bottom: 8px; transition: background 0.2s; border-bottom: 1px solid #f8fafc;" 
                                         class="hover:bg-gray-50">
                                        <div style="display: flex; gap: 12px;">
                                            <div style="width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"
                                                 :class="notif.data.status === 'completed' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600'">
                                                <i class="fas" :class="notif.data.icon"></i>
                                            </div>
                                            <div style="flex: 1;">
                                                <div style="font-size: 0.9rem; font-weight: 700; color: #1e293b; line-height: 1.3;" x-text="notif.data.message"></div>
                                                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;" x-text="notif.created_at"></div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="user-dropdown" x-data="{ open: false }">
                        <button type="button" class="user-trigger" @click.stop="open = !open">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}"
                                     alt="{{ Auth::user()->name }}"
                                     style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-blue);">
                            @else
                                <div style="width: 32px; height: 32px; background: var(--primary-blue); border-radius: 50%; color: white; display:flex; align-items:center; justify-content:center; font-weight: 800; font-size: 0.9rem;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                            {{ Auth::user()->name }}
                        </button>
                        <div class="dropdown-menu" x-show="open" x-cloak @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-2" 
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95">
                            <a href="{{ route('account.profile') }}"><i class="fas fa-user-circle"></i> Profil</a>
                            <a href="{{ url('/account/wallet') }}"><i class="fas fa-wallet"></i> Dompet Saya</a>
                            <a href="{{ route('account.orders') }}"><i class="fas fa-box"></i> Pesanan</a>
                            <form action="{{ route('store.logout') }}" method="POST">@csrf<button
                                    type="submit">Keluar</button></form>
                        </div>
                    </div>
                @endguest

                <div class="cart-dropdown-container">
                    <a href="javascript:void(0)" @click.prevent="showCartDropdown = !showCartDropdown; refreshCart()" class="cart-btn" title="Keranjang">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                        <span x-show="cartCount > 0" x-text="cartCount"
                        style="font-size: 0.9rem; font-weight: 800; margin-left: 4px;"></span>
                    </a>

                    <!-- Cart Pop-up Dropdown -->
                    <div class="cart-dropdown-menu" x-show="showCartDropdown" x-cloak @click.away="showCartDropdown = false"
                         x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2" 
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95">
                        
                        <div class="cart-dropdown-body">
                            <template x-if="cartItems.length === 0">
                                <div style="text-align: center; padding: 20px; color: #94a3b8;">
                                    <p style="font-size: 0.9rem;">Keranjang kosong.</p>
                                </div>
                            </template>
                            
                            <template x-for="item in cartItems" :key="item.id">
                                <div class="cart-dropdown-item">
                                    <div class="cart-dropdown-img">
                                        <img :src="item.image_path ? `{{ asset('') }}${item.image_path}` : 'https://via.placeholder.com/100?text=Obat'" 
                                             style="width: 100%; height: 100%; object-fit: contain;">
                                    </div>
                                    <div class="cart-dropdown-info">
                                        <div class="cart-dropdown-name" x-text="item.name"></div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <div class="cart-dropdown-price" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.price)"></div>
                                            <div style="font-size: 0.8rem; font-weight: 600; color: #64748b;" x-text="`x${item.qty}`"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="cart-dropdown-footer" x-show="cartItems.length > 0">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                <span style="font-size: 0.9rem; color: #64748b;">Subtotal</span>
                                <span style="font-weight: 800; color: #1e293b;" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(cartTotal)"></span>
                            </div>
                            <a href="{{ route('cart.index') }}" class="btn-checkout-mini">Lihat Keranjang</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main style="flex: 1;">
            @yield('content')
        </main>

        <!-- Footer Section -->
        <footer class="site-footer">
            <div class="footer-grid">
                <div class="footer-col">
                    <div style="font-size: 1.5rem; font-weight: 800; color: white; margin-bottom: 20px;">
                        Pharma<span style="color: var(--primary-blue);">care</span>
                    </div>
                    <p class="footer-info">
                        Solusi kesehatan digital terpercaya di Indonesia. Kami menyediakan obat-obatan berkualitas dengan layanan konsultasi apoteker real-time.
                    </p>
                    <div class="social-icons">
                        <a href="https://www.facebook.com/10969ipan" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/10969ipan/" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="https://wa.me/6281234567890" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Layanan</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('store.index') }}">Beranda Toko</a></li>
                        <li><a href="{{ route('ongkir.index') }}">Cek Ongkir</a></li>
                        <li><a href="javascript:void(0)" onclick="toggleChat()">Konsultasi AI</a></li>
                        <li><a href="{{ route('cart.index') }}">Keranjang</a></li>
                        <li><a href="{{ route('account.dashboard') }}">Akun Saya</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Kontak Kami</h4>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-phone-alt mr-2"></i> +62 812-3456-7890</a></li>
                        <li><a href="#"><i class="fas fa-envelope mr-2"></i> care@pharmacare.id</a></li>
                        <li><a href="#"><i class="fas fa-clock mr-2"></i> 24/7 Pelayanan Online</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Alamat Toko</h4>
                    <p class="footer-info">
                        <i class="fas fa-map-marker-alt mr-2" style="color: var(--primary-blue);"></i>
                        Jl. Pelayanan Kesehatan No. 88, Kota Tekno, Jawa Barat 40123
                    </p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Pharmacare Indonesia. All rights reserved.</p>
            </div>
        </footer>

        <!-- Auth Modal (Unified Login/Register) -->
        <div class="auth-modal-overlay" x-show="showAuthModal" x-cloak
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="showAuthModal = false">
            <div class="auth-modal-content"
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 scale-95 translate-y-6" 
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200" 
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
                 x-transition:leave-end="opacity-0 scale-95 translate-y-6">
                <button class="auth-modal-close" @click="showAuthModal = false">
                    <i class="fas fa-times"></i>
                </button>

                <!-- Tabs -->
                <div class="auth-tabs">
                    <button class="auth-tab-btn" :class="{ 'active': authTab === 'login' }" @click="authTab = 'login'">Masuk</button>
                    <button class="auth-tab-btn" :class="{ 'active': authTab === 'register' }" @click="authTab = 'register'">Daftar</button>
                </div>

                <!-- Login Form -->
                <div x-show="authTab === 'login'">
                    <div class="modal-form-header">
                        <h2>Masuk Akun</h2>
                        <p>Akses pesanan dan konsultasi kesehatan Anda.</p>
                    </div>
                    <form action="{{ route('store.login.post') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-input" placeholder="contoh@email.com" required>
                        </div>
                        <div class="form-group">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <label class="form-label" style="margin-bottom: 0;">Password</label>
                                <a href="#" style="font-size: 0.8rem; color: var(--primary-blue); text-decoration: none; font-weight: 700;">Lupa Password?</a>
                            </div>
                            <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn-auth-submit">Masuk ke Akun</button>
                    </form>

                    <!-- Divider Google -->
                    <div class="modal-google-divider">
                        <span>atau</span>
                    </div>

                    <!-- Tombol Google Login -->
                    <a href="{{ route('auth.google') }}" class="modal-btn-google">
                        <svg width="18" height="18" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M47.532 24.552c0-1.636-.132-3.196-.388-4.692H24.48v8.875h12.971c-.56 3.013-2.245 5.565-4.78 7.278v6.048h7.74c4.527-4.168 7.121-10.31 7.121-17.509z" fill="#4285F4"/>
                            <path d="M24.48 48c6.51 0 11.969-2.158 15.957-5.839l-7.74-6.048c-2.15 1.44-4.898 2.29-8.217 2.29-6.316 0-11.665-4.266-13.578-10.001H2.89v6.248C6.862 42.591 15.068 48 24.48 48z" fill="#34A853"/>
                            <path d="M10.902 28.402A14.63 14.63 0 0 1 10.08 24c0-1.529.262-3.015.822-4.402v-6.248H2.89A23.985 23.985 0 0 0 .48 24c0 3.875.92 7.544 2.41 10.65l8.012-6.248z" fill="#FBBC05"/>
                            <path d="M24.48 9.597c3.558 0 6.748 1.223 9.26 3.627l6.942-6.942C36.44 2.445 30.99 0 24.48 0 15.068 0 6.862 5.41 2.89 13.35l8.012 6.248C12.815 13.863 18.164 9.597 24.48 9.597z" fill="#EA4335"/>
                        </svg>
                        Masuk dengan Google
                    </a>

                    <div style="margin-top: 20px; text-align: center; font-size: 0.9rem; color: #64748b;">
                        Belum punya akun? <a href="javascript:void(0)" @click="authTab = 'register'" style="color: var(--primary-blue); font-weight: 800; text-decoration: none;">Daftar Sekarang</a>
                    </div>
                </div>

                <!-- Register Form -->
                <div x-show="authTab === 'register'" x-cloak>
                    <div class="modal-form-header">
                        <h2>Daftar Akun</h2>
                        <p>Mulai perjalanan sehat Anda bersama Pharmacare.</p>
                    </div>
                    <form action="{{ route('store.register.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-input" placeholder="Irfan Arfian" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alamat Email</label>
                            <input type="email" name="email" class="form-input" placeholder="user@email.com" required>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Konfirmasi</label>
                                <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-auth-submit">Buat Akun Sekarang</button>
                    </form>

                    <!-- Divider Google -->
                    <div class="modal-google-divider">
                        <span>atau daftar dengan</span>
                    </div>

                    <!-- Tombol Google Register -->
                    <a href="{{ route('auth.google') }}" class="modal-btn-google">
                        <svg width="18" height="18" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M47.532 24.552c0-1.636-.132-3.196-.388-4.692H24.48v8.875h12.971c-.56 3.013-2.245 5.565-4.78 7.278v6.048h7.74c4.527-4.168 7.121-10.31 7.121-17.509z" fill="#4285F4"/>
                            <path d="M24.48 48c6.51 0 11.969-2.158 15.957-5.839l-7.74-6.048c-2.15 1.44-4.898 2.29-8.217 2.29-6.316 0-11.665-4.266-13.578-10.001H2.89v6.248C6.862 42.591 15.068 48 24.48 48z" fill="#34A853"/>
                            <path d="M10.902 28.402A14.63 14.63 0 0 1 10.08 24c0-1.529.262-3.015.822-4.402v-6.248H2.89A23.985 23.985 0 0 0 .48 24c0 3.875.92 7.544 2.41 10.65l8.012-6.248z" fill="#FBBC05"/>
                            <path d="M24.48 9.597c3.558 0 6.748 1.223 9.26 3.627l6.942-6.942C36.44 2.445 30.99 0 24.48 0 15.068 0 6.862 5.41 2.89 13.35l8.012 6.248C12.815 13.863 18.164 9.597 24.48 9.597z" fill="#EA4335"/>
                        </svg>
                        Daftar dengan Google
                    </a>

                    <div style="margin-top: 20px; text-align: center; font-size: 0.9rem; color: #64748b;">
                        Sudah punya akun? <a href="javascript:void(0)" @click="authTab = 'login'" style="color: var(--primary-blue); font-weight: 800; text-decoration: none;">Login Disini</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick View Modal -->
        <div class="qv-modal-overlay" x-show="showQuickView" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-250"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="showQuickView = false">
            <div class="qv-modal-content"
                 x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-500"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-12"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-8"
                 id="quick-view-modal-content">
                <button class="qv-close" @click="showQuickView = false">
                    <i class="fas fa-times"></i>
                </button>
                
                <div class="qv-left">
                    <div id="qv-img-container" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                    </div>
                </div>
                
                <div class="qv-right">
                    <div class="qv-category" id="qv-category">Kategori</div>
                    <h2 class="qv-title" id="qv-name">Nama Produk</h2>
                    <div id="qv-price" class="qv-price" style="color: var(--primary-blue); font-size: 1.5rem; margin-bottom: 15px;">
                        Rp 0
                    </div>
                    
                    <div class="qv-desc" id="qv-description" style="line-height: 1.6; color: #475569; font-size: 0.95rem; margin-bottom: 25px;">
                        Deskripsi produk...
                    </div>
                    
                    <div class="qv-meta">
                        <div class="qv-meta-item">
                            <span class="qv-meta-label">Kemasan</span>
                            <span class="qv-meta-val" id="qv-unit">Pcs</span>
                        </div>
                        <div class="qv-meta-item">
                            <span class="qv-meta-label">Status Stok</span>
                            <span class="qv-meta-val" id="qv-stock">Tersedia</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    <!-- Floating AI Chatbot Widget -->
    <div id="chatbot-widget">
        <div class="chat-window" id="chatWindow">
            <div class="chat-header">
                <div>
                    <h4><i class="fas fa-robot"></i> SIMA</h4>
                    <small><span class="status-dot"></span> Aktif & Siap Membantu 24 Jam    </small>
                </div>
                <button onclick="toggleChat()" class="chat-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="chat-body" id="chatBody">
                <div class="chat-bubble bot">Halo! Saya **SIMA**, asisten pintar Pharmacare. Ada yang bisa saya bantu hari ini?</div>
            </div>
            <form class="chat-input-area" id="chatbotForm" data-url="{{ route('telemedicine.ai-reply') }}">
                <input type="text" id="chatbotInput" placeholder="Tanya tentang obat..." autocomplete="off">
                <button type="submit">➤</button>
            </form>
        </div>
        <!-- Greeting Bubble -->
        <div id="chat-greeting-bubble">
            <button class="bubble-close" onclick="dismissGreetingBubble(event)">✕</button>
            Bingung mulai dari mana? <strong>SIMA</strong> bisa bantu!
        </div>

        <!-- Chat FAB Card Launcher -->
        <div class="chat-fab-card" onclick="toggleChat()">
            <div class="chat-fab-logo">
                <i class="fas fa-robot"></i>
            </div>
            <div class="chat-fab-text">
                <h5>Chat SIMA</h5>
                <p><span class="status-dot" style="margin-right: 4px;"></span> Online</p>
            </div>
        </div>
    </div>

    <script>
        window.PharmacareConfig = {
            assetBase: "{{ asset('') }}",
            routes: {
                search: "{{ route('store.search-ajax') }}",
                itemDetail: "{{ url('/store/item') }}",
                quickView: "{{ url('/store/quick-view') }}",
                cartSummary: "{{ route('cart.summary') }}",
                addToCart: "{{ url('/cart/add-ajax') }}"
            }
        };
        window.showToast = function(icon, title) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                icon: icon,
                title: title,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        };
    </script>
    <script src="{{ asset('assets/vendor/alpinejs/alpine.min.js') }}" defer></script>
    <script src="{{ asset('assets/vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/js/pharmacare.js') }}?v=3.1" defer></script>
    @vite(['resources/js/frontend/chatbot_widget.js'])
    @stack('scripts')

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const wm = document.getElementById('wellnessModal');
            if (wm) {
                wm.addEventListener('click', function(e) {
                    if (e.target === this) this.style.display = 'none';
                });
            }
        });
    </script>

    <!-- WELLNESS DETAIL MODAL (GLOBAL - Pure Vanilla JS) -->
    <div id="wellnessModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:999999; background:rgba(15,23,42,0.8); backdrop-filter:blur(8px); align-items:center; justify-content:center; padding:20px; transition: opacity 0.3s ease;"
         onclick="if(event.target===this) window.closeArticleModal()">
        <div style="background:white; border-radius:30px; width:100%; max-width:600px; overflow:hidden; position:relative; box-shadow: 0 25px 50px rgba(0,0,0,0.3); animation: slideUp 0.4s cubic-bezier(0.23,1,0.32,1);">
            <button onclick="window.closeArticleModal()" style="position:absolute; right:20px; top:20px; border:none; background:white; color:#1e293b; width:36px; height:36px; border-radius:50%; cursor:pointer; font-size:1.1rem; z-index:10; display:flex; align-items:center; justify-content:center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">✕</button>
            <div style="height:250px; overflow:hidden;">
                <img id="wm-img" src="" style="width:100%; height:100%; object-fit:cover;">
            </div>
            <div style="padding:40px;">
                <div style="font-size:0.75rem; font-weight:800; color:var(--primary-blue); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:12px;">Tips & Insight Kesehatan</div>
                <h2 id="wm-title" style="font-size:1.8rem; font-weight:800; color:#1e293b; margin-bottom:20px; line-height:1.2;"></h2>
                <p id="wm-content" style="font-size:1.1rem; color:#475569; line-height:1.7; white-space:pre-wrap;"></p>
                <div style="margin-top:30px; padding-top:20px; border-top:1px solid #f1f5f9;">
                    <button onclick="window.closeArticleModal()" style="width:100%; padding:14px; background:var(--primary-blue); color:white; border:none; border-radius:15px; font-weight:700; cursor:pointer;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <script>
    window.openArticleModal = function(article) {
        if (!article) return;
        document.getElementById('wm-title').textContent = article.title || '';
        document.getElementById('wm-content').textContent = article.content || '';
        document.getElementById('wm-img').src = '/' + (article.image_path || '');
        var m = document.getElementById('wellnessModal');
        m.style.display = 'flex';
    };
    window.closeArticleModal = function() {
        document.getElementById('wellnessModal').style.display = 'none';
    };
    // Keep Alpine compatibility
    window.openWellnessModal = window.openArticleModal;
    </script>
</div>
    <style>@keyframes slideUp { from { transform: translateY(20px); opacity:0; } to { transform: translateY(0); opacity:1; } }</style>
    @if (session('success')) <script>window.addEventListener('DOMContentLoaded', () => window.showToast('success', '{{ session('success') }}'));</script> @endif
    @if (session('warning')) <script>window.addEventListener('DOMContentLoaded', () => window.showToast('warning', '{{ session('warning') }}'));</script> @endif
    @if (session('error')) <script>window.addEventListener('DOMContentLoaded', () => window.showToast('error', '{{ session('error') }}'));</script> @endif
    @if ($errors->any()) <script>window.addEventListener('DOMContentLoaded', () => window.showToast('error', '{{ $errors->first() }}'));</script> @endif
</body>

</html>