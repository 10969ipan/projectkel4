<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi Medis Online</title>
    <style>
        /* Desktop-First Chat UI */
        :root {
            --primary-blue: #0076D6; 
            --bg-color: #F8F9FB;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background-color: #E2E8F0; display: flex; align-items: center; justify-content: center; height: 100vh; }
        
        .chat-container { 
            width: 100%; max-width: 1000px; 
            background: #fff; height: 90vh; 
            display: flex; flex-direction: row;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* Sidebar Info */
        .sidebar {
            width: 300px;
            background: #FAFAFA;
            border-right: 1px solid #E0E0E0;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header { padding: 30px 20px; text-align: center; border-bottom: 1px solid #E0E0E0; }
        .doctor-avatar-large { width: 100px; height: 100px; border-radius: 50%; background: #E6F3FF; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 15px; border: 4px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .doctor-name { font-size: 1.2rem; font-weight: 700; margin-bottom: 5px; color: #333; }
        .doctor-sp { font-size: 0.95rem; color: var(--primary-blue); font-weight: 600; }

        .info-list { padding: 20px; display: flex; flex-direction: column; gap: 15px; }
        .info-item { display: flex; justify-content: space-between; font-size: 0.95rem; border-bottom: 1px dashed #E0E0E0; padding-bottom: 10px; }
        .info-item span:first-child { color: #888; }
        .info-item span:last-child { font-weight: 600; color: #333; }

        /* Main Chat Window */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #F0F4F8; /* Light chat background */
        }

        .chat-header { 
            padding: 20px 30px; 
            background: white; 
            display: flex; 
            align-items: center; 
            border-bottom: 1px solid #E0E0E0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }

        .status-dot { width: 12px; height: 12px; border-radius: 50%; background: #4CAF50; margin-right: 10px; }

        .chat-area {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .bubble {
            max-width: 60%;
            padding: 15px 20px;
            border-radius: 18px;
            font-size: 1rem;
            line-height: 1.5;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .bubble.doctor {
            background: #fff;
            color: #333;
            border-bottom-left-radius: 4px;
            align-self: flex-start;
        }

        .bubble.patient {
            background: var(--primary-blue);
            color: white;
            border-bottom-right-radius: 4px;
            align-self: flex-end;
        }

        .card-resep {
            background: #FAFAFA;
            border: 2px solid var(--primary-blue);
            padding: 20px;
            border-radius: 12px;
            margin-top: 15px;
            font-size: 0.95rem;
            color: #333;
            text-align: center;
        }

        .input-area {
            padding: 20px 30px;
            background: #fff;
            border-top: 1px solid #E0E0E0;
            display: flex;
            gap: 15px;
        }

        .chat-input {
            flex: 1;
            padding: 15px 25px;
            border: 2px solid #E0E0E0;
            border-radius: 30px;
            outline: none;
            font-size: 1rem;
            transition: border 0.3s;
        }
        .chat-input:focus { border-color: var(--primary-blue); }

        .btn-send {
            width: 55px; height: 55px;
            border-radius: 50%;
            background: var(--primary-blue);
            color: white;
            border: none;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            transition: background 0.3s, transform 0.1s;
        }
        .btn-send:hover { background: #005FA3; }
        .btn-send:active { transform: scale(0.95); }

        .btn-back { display: none; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .btn-back { display: block; margin-right: 15px; font-size: 1.5rem; text-decoration: none; color: #333; }
        }
    </style>
</head>
<body>

<div class="chat-container">
    <!-- Doctor Profile Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="doctor-avatar-large">👨‍⚕️</div>
            <div class="doctor-name">Dr. Budi Santoso</div>
            <div class="doctor-sp">Spesialis Penyakit Dalam</div>
        </div>
        <div class="info-list">
            <div class="info-item"><span>No. STR</span> <span>9921 3302 11</span></div>
            <div class="info-item"><span>Pengalaman</span> <span>10 Tahun</span></div>
            <div class="info-item"><span>Rating</span> <span>⭐ 4.9 (200 Ulasan)</span></div>
        </div>
        
        <div style="margin-top: auto; padding: 20px;">
            <a href="javascript:history.back()" style="display:block; text-align:center; padding:15px; border:2px solid #E0E0E0; border-radius:12px; text-decoration:none; color:#333; font-weight:bold;">Tutup Konsultasi</a>
        </div>
    </div>

    <!-- Main Chat UI -->
    <div class="chat-main">
        <div class="chat-header">
            <a href="javascript:history.back()" class="btn-back">❮</a>
            <div class="status-dot"></div>
            <h3 style="font-size: 1.1rem; color: #333;">Live Chat Sedang Berlangsung...</h3>
        </div>

        <div class="chat-area">
            <div style="text-align: center; color: #888; font-size: 0.85rem; margin-bottom: 10px;">Hari ini, 09:41 AM</div>
            
            <div class="bubble doctor">
                Halo! Ada yang bisa saya bantu dengan keluhan kesehatan Anda hari ini? Sampaikan keluhan secara detail ya 😊
            </div>
            
            @if(isset($chats))
                @foreach($chats as $c)
                    @if($c->is_from_doctor)
                        <div class="bubble doctor">{{ $c->message }}</div>
                    @else
                        <div class="bubble patient">{{ $c->message }}</div>
                    @endif
                @endforeach
            @else
                <div class="bubble patient">
                    Selamat pagi Dok, asam lambung saya naik sejak semalam dan dada terasa perih. Boleh diresepkan obat keras?
                </div>
                
                <div class="bubble doctor">
                    Selamat pagi. Mengingat riwayat Anda, saya akan berikan resep digital untuk Pantoprazole. Obat ini wajib menggunakan resep dokter.
                    
                    <div class="card-resep">
                        <strong style="color: var(--primary-blue); font-size: 1.1rem; display:block; margin-bottom:10px;">⚕️ Resep Resmi Diterbitkan</strong>
                        Akses keranjang obat keras Anda telah di-unlock.
                        <br><br>
                        <a href="{{ route('checkout.index') }}" style="display:inline-block; padding:12px 25px; background:var(--primary-blue); color:white; border-radius:8px; text-decoration:none; font-size:1rem; font-weight:bold;">Lanjut ke Pembayaran Obat</a>
                    </div>
                </div>
            @endif
        </div>

        <form class="input-area" id="chatForm">
            @csrf
            <input type="text" id="messageInput" name="message" class="chat-input" placeholder="Ketik keluhan atau tanya tentang obat..." autocomplete="off">
            <button type="submit" class="btn-send" id="sendBtn">➤</button>
        </form>
    </div>
</div>

<script>
    const chatArea = document.querySelector('.chat-area');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');

    // Auto-scroll to bottom
    const scrollToBottom = () => {
        chatArea.scrollTop = chatArea.scrollHeight;
    };
    scrollToBottom();

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (!message) return;

        // 1. Tambah bubble pasien ke UI
        const patientBubble = document.createElement('div');
        patientBubble.className = 'bubble patient';
        patientBubble.textContent = message;
        chatArea.appendChild(patientBubble);
        
        messageInput.value = '';
        scrollToBottom();

        // 2. Tambah loading bubble (Dokter sedang mengetik)
        const loadingBubble = document.createElement('div');
        loadingBubble.className = 'bubble doctor loading';
        loadingBubble.innerHTML = '<em>Apoteker Digital sedang mengetik...</em>';
        chatArea.appendChild(loadingBubble);
        scrollToBottom();

        try {
            const response = await fetch("{{ route('telemedicine.ai-reply') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    message: message,
                    doctor_id: "{{ $doctor->id }}"
                })
            });

            const data = await response.json();

            // 3. Ganti loading bubble dengan jawaban AI
            loadingBubble.classList.remove('loading');
            loadingBubble.innerHTML = data.reply.replace(/\n/g, '<br>');
            scrollToBottom();

        } catch (error) {
            loadingBubble.innerHTML = '<span style="color:red">Gagal terhubung ke AI. Silakan coba lagi.</span>';
        }
    });
</script>

</body>
</html>
