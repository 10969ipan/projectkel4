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

    const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

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
            let data = null;
            for (let attempt = 1; attempt <= 3; attempt++) {
                if (attempt > 1) {
                    await sleep(900 * (attempt - 1));
                }

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

                if (!response.ok) {
                    if (attempt < 3) continue;
                    throw new Error('Server error ' + response.status);
                }

                data = await response.json();
                if (!data.retryable) break;
            }

            loadingBubble.classList.remove('loading');
            loadingBubble.innerHTML = formatBotHtml(data.reply);
            scrollToBottom();

        } catch (error) {
            loadingBubble.innerHTML = '<span style="color:red">Gagal terhubung ke AI. Silakan coba lagi.</span>';
        }
    });
});
