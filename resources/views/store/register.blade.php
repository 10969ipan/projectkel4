<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Pharmacare</title>
    <style>
        :root { --primary-blue: #0076D6; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        
        body {
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #E6F3FF 0%, #F8F9FB 60%, #E6F3FF 100%);
        }

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

        .left-panel .logo { font-size: 3rem; font-weight: 800; margin-bottom: 15px; }
        .left-panel .tagline { font-size: 1.2rem; opacity: 0.9; text-align: center; line-height: 1.6; }

        .right-panel {
            width: 500px;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px;
            box-shadow: -10px 0 40px rgba(0,0,0,0.05);
        }

        .back-to-store { align-self: flex-start; text-decoration: none; color: #888; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; margin-bottom: 30px; }
        .back-to-store:hover { color: var(--primary-blue); }

        .form-header { text-align: center; margin-bottom: 30px; width: 100%; }
        .form-header h1 { font-size: 1.8rem; font-weight: 700; margin-bottom: 8px; }
        .form-header p { color: #888; }

        .alert-error { background: #FFEBEE; color: #C62828; border-left: 4px solid #EF5350; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 0.95rem; width: 100%; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group { width: 100%; margin-bottom: 18px; }
        .form-label { display: block; font-size: 0.9rem; font-weight: 600; color: #444; margin-bottom: 8px; }
        .form-control {
            width: 100%;
            padding: 13px 18px;
            border: 2px solid #E0E0E0;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: border-color 0.2s;
            outline: none;
        }
        .form-control:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(0,118,214,0.08); }

        .btn-register {
            width: 100%;
            background: var(--primary-blue);
            color: white;
            padding: 15px;
            border-radius: 12px;
            border: none;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 5px;
        }
        .btn-register:hover { background: #005FA3; }

        .login-hint { text-align: center; width: 100%; font-size: 0.95rem; color: #888; margin-top: 20px; }
        .login-hint a { color: var(--primary-blue); font-weight: 700; text-decoration: none; }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; padding: 40px 25px; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="left-panel">
        <div class="logo">💊 Pharmacare</div>
        <p class="tagline">Bergabunglah dengan ribuan pelanggan yang mempercayakan kebutuhan farmasi mereka kepada kami.</p>
    </div>

    <div class="right-panel">
        <a href="{{ route('store.index') }}" class="back-to-store">❮ Kembali ke Etalase</a>

        <div class="form-header">
            <h1>Buat Akun Baru</h1>
            <p>Daftar gratis dan mulai belanja obat dengan mudah</p>
        </div>

        <form action="{{ route('store.register.store') }}" method="POST" style="width: 100%;">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap Anda" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Email</label>
                <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-register">Daftar Sekarang</button>
        </form>

        <div class="login-hint">
            Sudah punya akun? <a href="{{ route('store.login') }}">Login di sini</a>
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
