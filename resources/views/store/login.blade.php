<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pharmacare</title>
    <style>
        :root { --primary-blue: #0076D6; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        
        body {
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #E6F3FF 0%, #F8F9FB 60%, #E6F3FF 100%);
        }

        /* Left Panel - Promotional */
        .left-panel {
            flex: 1;
            background: linear-gradient(160deg, #0076D6 0%, #00C6FF 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px;
            color: white;
        }

        .left-panel .logo { font-size: 3rem; font-weight: 800; margin-bottom: 15px; display: flex; align-items: center; gap: 15px; }
        .left-panel .tagline { font-size: 1.3rem; opacity: 0.9; margin-bottom: 50px; line-height: 1.5; text-align: center; }

        .feature-list { display: flex; flex-direction: column; gap: 20px; width: 100%; max-width: 380px; }
        .feature-item { background: rgba(255,255,255,0.15); border-radius: 14px; padding: 20px 25px; display: flex; align-items: center; gap: 15px; }
        .feature-icon { font-size: 1.8rem; }
        .feature-text strong { display: block; font-size: 1.05rem; margin-bottom: 3px; }
        .feature-text span { font-size: 0.9rem; opacity: 0.85; }

        /* Right Panel - Form */
        .right-panel {
            width: 480px;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 50px;
            box-shadow: -10px 0 40px rgba(0,0,0,0.05);
        }

        .back-to-store { align-self: flex-start; text-decoration: none; color: #888; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; margin-bottom: 40px; }
        .back-to-store:hover { color: var(--primary-blue); }

        .form-header { text-align: center; margin-bottom: 40px; width: 100%; }
        .form-header h1 { font-size: 2rem; font-weight: 700; color: #1a1a1a; margin-bottom: 8px; }
        .form-header p { color: #888; font-size: 1rem; }

        .alert-error { background: #FFEBEE; color: #C62828; border-left: 4px solid #EF5350; padding: 15px 20px; border-radius: 10px; margin-bottom: 25px; font-size: 0.95rem; width: 100%; }
        .alert-success { background: #E8F5E9; color: #2E7D32; border-left: 4px solid #4CAF50; padding: 15px 20px; border-radius: 10px; margin-bottom: 25px; font-size: 0.95rem; width: 100%; }

        .form-group { width: 100%; margin-bottom: 20px; }
        .form-label { display: block; font-size: 0.95rem; font-weight: 600; color: #444; margin-bottom: 8px; }
        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #E0E0E0;
            border-radius: 12px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(0, 118, 214, 0.08);
        }

        .btn-login {
            width: 100%;
            background: var(--primary-blue);
            color: white;
            padding: 16px;
            border-radius: 12px;
            border: none;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            margin-top: 10px;
        }
        .btn-login:hover { background: #005FA3; }
        .btn-login:active { transform: scale(0.99); }

        .divider { display: flex; align-items: center; gap: 15px; margin: 25px 0; width: 100%; color: #ccc; font-size: 0.9rem; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #E0E0E0; }

        .register-hint { text-align: center; width: 100%; font-size: 0.95rem; color: #888; }
        .register-hint a { color: var(--primary-blue); font-weight: 700; text-decoration: none; }

        .note-box { background: #FFF8E1; border: 1px solid #FFC107; border-radius: 10px; padding: 15px 18px; font-size: 0.9rem; color: #795548; margin-bottom: 25px; width: 100%; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .left-panel { display: none; }
            .right-panel { width: 100%; padding: 40px 30px; }
        }
    </style>
</head>
<body>

    <!-- Left Promo Panel -->
    <div class="left-panel">
        <div class="logo">💊 Pharmacare</div>
        <p class="tagline">Platform farmasi terpercaya untuk kebutuhan kesehatan Anda, kapan saja dan di mana saja.</p>

        <div class="feature-list">
            <div class="feature-item">
                <div class="feature-icon">🩺</div>
                <div class="feature-text">
                    <strong>Konsultasi Dokter Online</strong>
                    <span>Chat dengan dokter tepercaya, gratis & cepat.</span>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">💊</div>
                <div class="feature-text">
                    <strong>Obat Lengkap & Bergaransi</strong>
                    <span>Ribuan produk farmasi resmi tersedia.</span>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">⚡</div>
                <div class="feature-text">
                    <strong>Instant Delivery</strong>
                    <span>Tiba dalam 2 jam, sesuai radius pengiriman.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Form Panel -->
    <div class="right-panel">
        <a href="{{ route('store.index') }}" class="back-to-store">❮ Kembali ke Etalase Apotek</a>

        <div class="form-header">
            <h1>Masuk ke Akun</h1>
            <p>Wajib login untuk memesan obat di Pharmacare</p>
        </div>

        <form action="{{ route('store.login.post') }}" method="POST" style="width: 100%;">
            @csrf
            <!-- Simpan URL redirect setelah login (kembali ke halaman sebelumnya) -->
            @if(request()->has('redirect'))
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            @endif

            <div class="form-group">
                <label class="form-label" for="email">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="contoh@email.com" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password Anda" required>
            </div>

            <button type="submit" class="btn-login">Masuk ke Pharmacare</button>
        </form>

        <div class="divider">atau</div>

        <div class="register-hint">
            Belum punya akun? <a href="{{ route('store.register') }}">Daftar Sekarang Gratis →</a>
        </div>
    </div>

    <!-- SweetAlert2 -->
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

        @if (session('warning'))
            Toast.fire({
                icon: 'warning',
                title: '{{ session('warning') }}'
            });
        @endif

        @if ($errors->any())
            Toast.fire({
                icon: 'error',
                title: '{{ $errors->first() }}'
            });
        @endif
    </script>

</body>
</html>
