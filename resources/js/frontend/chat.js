document.addEventListener('DOMContentLoaded', function() {
    const chatArea = document.querySelector('.chat-area');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');

    if (!chatForm) return;

    const aiReplyUrl = chatForm.dataset.url;
    const doctorId = chatForm.dataset.doctorId;

    const scrollToBottom = () => {
        chatArea.scrollTop = chatArea.scrollHeight;
    };
    scrollToBottom();

    const formatBotHtml = (text) => {
        return String(text || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.+?)\*/g, '<em>$1</em>')
            .replace(/^[•\-]\s+(.+)$/gm, '&bull; $1')
            .replace(/\n/g, '<br>');
    };

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

        // 2. Tambah loading bubble
        const loadingBubble = document.createElement('div');
        loadingBubble.className = 'bubble doctor loading';
        loadingBubble.innerHTML = '<em>SIMA sedang mengetik...</em>';
        chatArea.appendChild(loadingBubble);
        scrollToBottom();

        try {
            const response = await fetch(aiReplyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
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
            loadingBubble.innerHTML = formatBotHtml(data.reply);
            scrollToBottom();

        } catch (error) {
            loadingBubble.innerHTML = '<span style="color:red">Gagal terhubung ke AI. Silakan coba lagi.</span>';
        }
    });
});
