document.addEventListener('DOMContentLoaded', function() {
    const chatArea = document.querySelector('.chat-area');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');

    if (!chatForm) return;

    const aiReplyUrl = chatForm.dataset.url;
    const doctorId = chatForm.dataset.doctorId;

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
            const response = await fetch(aiReplyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    message: message,
                    doctor_id: doctorId
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
});
