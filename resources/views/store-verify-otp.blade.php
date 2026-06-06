<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - Pharmacare</title>
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

        .form-header { margin-bottom: 30px; text-align: center; }
        .form-header h1 { font-size: 1.6rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px; }
        .form-header p { color: var(--text-light); font-size: 0.95rem; line-height: 1.5; }

        .form-group { margin-bottom: 20px; text-align: center; }
        .form-label { display: block; font-size: 0.9rem; font-weight: 600; color: var(--text-dark); margin-bottom: 8px; text-align: left; }
        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid #EAEAEA;
            border-radius: 14px;
            font-size: 1.2rem;
            text-align: center;
            letter-spacing: 5px;
            font-weight: bold;
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

        @media (max-width: 480px) {
            .auth-card { padding: 40px 25px; border-radius: 0; border: none; box-shadow: none; background: transparent; }
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <a href="{{ route('store.password.request') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>

        <div class="logo-container">
            <div class="logo-text">Pharma<span>care</span></div>
        </div>

        <div class="form-header">
            <h1>Verifikasi OTP</h1>
            <p>Masukkan 6 digit kode OTP yang telah dikirim ke WhatsApp Anda ({{ session('reset_phone') }}).</p>
        </div>

        <form action="{{ route('store.password.verify.post') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <input type="text" id="otp" name="otp" class="form-control" placeholder="••••••" maxlength="6" required autofocus>
            </div>

            <button type="submit" class="btn-auth">Verifikasi Kode</button>
        </form>
    </div>

    <!-- SweetAlert2 -->
    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            iconColor: '#0076D6',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if (session('success'))
            Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
        @endif

        @if ($errors->any())
            Toast.fire({ icon: 'error', title: '{{ $errors->first() }}' });
        @endif

        @if (session('error'))
            Toast.fire({ icon: 'error', title: '{{ session('error') }}' });
        @endif
    </script>

</body>
</html>
