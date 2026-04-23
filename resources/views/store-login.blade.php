<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pharmacare</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/inter/inter.css') }}">
    <style>
        :root { --primary-blue: #0076D6; --text-dark: #333; --text-light: #888; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', 'Segoe UI', Arial, sans-serif; }
        
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #F8F9FB;
            padding: 20px;
        }

        .auth-card {
            width: 100%;
            max-width: 440px;
            background: white;
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.04);
            border: 1px solid #F0F0F0;
        }

        .logo-container { text-align: center; margin-bottom: 40px; }
        .logo-text { font-size: 1.8rem; font-weight: 800; color: var(--primary-blue); letter-spacing: -0.02em; }
        .logo-text span { color: var(--text-dark); }

        .back-link { display: flex; align-items: center; gap: 8px; text-decoration: none; color: var(--text-light); font-size: 0.9rem; margin-bottom: 25px; transition: color 0.2s; }
        .back-link:hover { color: var(--primary-blue); }

        .form-header { margin-bottom: 30px; }
        .form-header h1 { font-size: 1.6rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px; }
        .form-header p { color: var(--text-light); font-size: 0.95rem; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 0.9rem; font-weight: 600; color: var(--text-dark); margin-bottom: 8px; }
        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid #EAEAEA;
            border-radius: 14px;
            font-size: 0.95rem;
            transition: all 0.2s;
            outline: none;
            background-color: #FAFAFA;
        }
        .form-control:focus {
            border-color: var(--primary-blue);
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(0, 118, 214, 0.08);
        }

        .btn-auth {
            width: 100%;
            background: var(--primary-blue);
            color: white;
            padding: 16px;
            border-radius: 14px;
            border: none;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-auth:hover { background: #0066BA; transform: translateY(-1px); box-shadow: 0 8px 20px rgba(0, 118, 214, 0.2); }
        .btn-auth:active { transform: translateY(0); }

        .auth-footer { margin-top: 30px; text-align: center; font-size: 0.95rem; color: var(--text-light); }
        .auth-footer a { color: var(--primary-blue); font-weight: 700; text-decoration: none; }

        @media (max-width: 480px) {
            .auth-card { padding: 40px 25px; border-radius: 0; border: none; box-shadow: none; background: transparent; }
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: var(--text-light);
            font-size: 0.85rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #EAEAEA;
        }

        /* Tombol Google */
        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 14px 20px;
            border: 1.5px solid #EAEAEA;
            border-radius: 14px;
            background: #fff;
            color: #333;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s;
            cursor: pointer;
            margin-bottom: 8px;
        }
        .btn-google:hover {
            border-color: #4285F4;
            background: #F8F9FF;
            box-shadow: 0 4px 15px rgba(66, 133, 244, 0.12);
            transform: translateY(-1px);
        }
        .btn-google:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <a href="{{ route('store.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Etalase
        </a>

        <div class="logo-container">
            <div class="logo-text">Pharma<span>care</span></div>
        </div>

        <div class="form-header">
            <h1>Masuk Akun</h1>
            <p>Silakan masuk untuk melanjutkan pesanan Anda.</p>
        </div>

        <form action="{{ route('store.login.post') }}" method="POST">
            @csrf
            @if(request()->has('redirect'))
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            @endif

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="user@email.com" required autofocus>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label class="form-label" for="password" style="margin-bottom: 0;">Password</label>
                    <a href="#" style="font-size: 0.85rem; color: var(--primary-blue); text-decoration: none; font-weight: 600;">Lupa Password?</a>
                </div>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-auth">Masuk ke Akun</button>
        </form>

        <!-- Divider -->
        <div class="divider">
            <span>atau</span>
        </div>

        <!-- Tombol Login Google -->
        <a href="{{ route('auth.google') }}" class="btn-google">
            <svg width="20" height="20" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M47.532 24.552c0-1.636-.132-3.196-.388-4.692H24.48v8.875h12.971c-.56 3.013-2.245 5.565-4.78 7.278v6.048h7.74c4.527-4.168 7.121-10.31 7.121-17.509z" fill="#4285F4"/>
                <path d="M24.48 48c6.51 0 11.969-2.158 15.957-5.839l-7.74-6.048c-2.15 1.44-4.898 2.29-8.217 2.29-6.316 0-11.665-4.266-13.578-10.001H2.89v6.248C6.862 42.591 15.068 48 24.48 48z" fill="#34A853"/>
                <path d="M10.902 28.402A14.63 14.63 0 0 1 10.08 24c0-1.529.262-3.015.822-4.402v-6.248H2.89A23.985 23.985 0 0 0 .48 24c0 3.875.92 7.544 2.41 10.65l8.012-6.248z" fill="#FBBC05"/>
                <path d="M24.48 9.597c3.558 0 6.748 1.223 9.26 3.627l6.942-6.942C36.44 2.445 30.99 0 24.48 0 15.068 0 6.862 5.41 2.89 13.35l8.012 6.248C12.815 13.863 18.164 9.597 24.48 9.597z" fill="#EA4335"/>
            </svg>
            Masuk dengan Google
        </a>

        <div class="auth-footer">
            Belum punya akun? <a href="{{ route('store.register') }}">Daftar Gratis</a>
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
            iconColor: '#0076D6',
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

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        @endif
    </script>

</body>
</html>
