<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direktori Telemedisin Pintar</title>
    <style>
        /* Desktop-First Telemedicine Directory Design */
        :root {
            --primary-blue: #0076D6; 
            --bg-color: #F8F9FB;
            --text-main: #333333;
            --text-muted: #888888;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); }
        
        .container { max-width: 1200px; margin: 40px auto; padding: 0 40px; }
        
        /* Top Banner */
        .header-banner { background: white; padding: 40px; border-radius: 20px; text-align: center; margin-bottom: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .header-banner h1 { font-size: 2.2rem; margin-bottom: 15px; color: var(--text-main); }
        .header-banner p { font-size: 1.1rem; color: var(--text-muted); max-width: 600px; margin: 0 auto; line-height: 1.5; }

        .search-doctors { margin-top: 30px; display: flex; justify-content: center; }
        .search-doctors input { width: 400px; padding: 15px 25px; border-radius: 30px; border: 2px solid #E0E0E0; font-size: 1rem; outline: none; transition: border-color 0.2s;}
        .search-doctors input:focus { border-color: var(--primary-blue); }

        /* Navigation Top Bar */
        .top-nav { display: flex; align-items: center; margin-bottom: 30px; }
        .top-nav a { text-decoration: none; color: var(--primary-blue); font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 8px; }

        /* Doctor Grid */
        .doctor-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; }

        /* Doctor Card */
        .doctor-card { background: white; border-radius: 20px; padding: 30px; display: flex; flex-direction: column; align-items: center; text-align: center; border: 1px solid #E0E0E0; transition: transform 0.2s, box-shadow 0.2s; position: relative; overflow: hidden; }
        .doctor-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-color: var(--primary-blue); }

        .status-badge { position: absolute; top: 15px; right: 15px; background: #E8F5E9; color: #2E7D32; font-size: 0.8rem; font-weight: 700; padding: 5px 12px; border-radius: 20px; display: flex; align-items: center; gap: 5px; }
        .status-badge::before { content: ''; width: 8px; height: 8px; background: #4CAF50; border-radius: 50%; display: block; }

        .doctor-avatar { width: 100px; height: 100px; border-radius: 50%; background: #E6F3FF; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin-bottom: 20px; }
        
        .doctor-name { font-size: 1.25rem; font-weight: 700; margin-bottom: 5px; }
        .doctor-sp { color: var(--primary-blue); font-weight: 600; font-size: 0.95rem; margin-bottom: 15px; }
        
        .doctor-info { display: flex; justify-content: center; gap: 20px; margin-bottom: 25px; width: 100%; }
        .info-tab { display: flex; flex-direction: column; }
        .info-tab span:first-child { font-size: 0.8rem; color: var(--text-muted); }
        .info-tab span:last-child { font-weight: 700; font-size: 0.95rem; }

        .btn-chat { width: 100%; background: var(--primary-blue); color: white; border: none; padding: 12px; border-radius: 12px; font-weight: 600; font-size: 1rem; cursor: pointer; text-decoration: none; transition: background 0.2s; }
        .btn-chat:hover { background: #005FA3; }

        @media (max-width: 768px) { .doctor-grid { grid-template-columns: 1fr; } .search-doctors input { width: 100%; } }
    </style>
</head>
<body>

<div class="container">
    <div class="top-nav">
        <a href="{{ route('store.index') }}">❮ Kembali ke Etalase Apotek</a>
    </div>

    <!-- Header Banner -->
    <div class="header-banner">
        <h1>Konsultasi Telemedisin Cepat</h1>
        <p>Apabila Anda ingin membeli Obat Keras bergaris merah, Anda diwajibkan untuk berkonsultasi terlebih dahulu bersama Dokter tersumpah di ruang obrolan kami.</p>
        
        <div class="search-doctors">
            <input type="text" placeholder="🔍 Cari Dokter Spesialis atau Gejala...">
        </div>
    </div>

    <!-- Doctor List -->
    <div class="doctor-grid">
        @if(isset($doctors) && count($doctors) > 0)
            @foreach($doctors as $doctor)
                <div class="doctor-card">
                    <div class="status-badge">Online</div>
                    <div class="doctor-avatar">👨‍⚕️</div>
                    <h2 class="doctor-name">{{ $doctor->name }}</h2>
                    <div class="doctor-sp">Apoteker / Dokter Telemedisin</div>
                    
                    <div class="doctor-info">
                        <div class="info-tab">
                            <span>Rating</span>
                            <span>⭐ 4.9</span>
                        </div>
                        <div class="info-tab">
                            <span>Pasien</span>
                            <span>210+</span>
                        </div>
                    </div>

                    <a href="{{ route('telemedicine.chat', $doctor->id) }}" class="btn-chat">Chat & Temui Dokter</a>
                </div>
            @endforeach
        @else
            <!-- Dummy Doctor Data jika database Kosong -->
            <div class="doctor-card">
                <div class="status-badge">Online</div>
                <div class="doctor-avatar">👨‍⚕️</div>
                <h2 class="doctor-name">Dr. Budi Santoso, Sp.PD</h2>
                <div class="doctor-sp">Spesialis Penyakit Dalam Khusus</div>
                
                <div class="doctor-info">
                    <div class="info-tab">
                        <span>Rating</span>
                        <span>⭐ 4.9</span>
                    </div>
                    <div class="info-tab">
                        <span>Pasien</span>
                        <span>420+</span>
                    </div>
                </div>

                <a href="{{ route('telemedicine.chat', 1) }}" class="btn-chat">Chat & Temui Dokter</a>
            </div>

            <div class="doctor-card">
                <div class="status-badge">Online</div>
                <div class="doctor-avatar">👨‍⚕️</div>
                <h2 class="doctor-name">Dr. Sarah Wijaya, Sp.A</h2>
                <div class="doctor-sp">Spesialis Anak & Alergi</div>
                
                <div class="doctor-info">
                    <div class="info-tab">
                        <span>Rating</span>
                        <span>⭐ 5.0</span>
                    </div>
                    <div class="info-tab">
                        <span>Pasien</span>
                        <span>180+</span>
                    </div>
                </div>

                <a href="{{ route('telemedicine.chat', 2) }}" class="btn-chat">Chat & Temui Dokter</a>
            </div>

            <div class="doctor-card">
                <div class="status-badge">Online</div>
                <div class="doctor-avatar">👩‍⚕️</div>
                <h2 class="doctor-name">Apt. Lestari Putri, S.Farm</h2>
                <div class="doctor-sp">Apoteker Klinis Perizinan</div>
                
                <div class="doctor-info">
                    <div class="info-tab">
                        <span>Rating</span>
                        <span>⭐ 4.8</span>
                    </div>
                    <div class="info-tab">
                        <span>Konsultasi</span>
                        <span>600+</span>
                    </div>
                </div>

                <a href="{{ route('telemedicine.chat', 3) }}" class="btn-chat">Chat & Temui Dokter</a>
            </div>
        @endif
    </div>
</div>

</body>
</html>
