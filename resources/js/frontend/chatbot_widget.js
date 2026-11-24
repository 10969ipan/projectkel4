document.addEventListener('DOMContentLoaded', function() {
    const chatWindow = document.getElementById('chatWindow');
    const chatBody = document.getElementById('chatBody');
    const chatbotForm = document.getElementById('chatbotForm');
    const chatbotInput = document.getElementById('chatbotInput');
    const greetingBubble = document.getElementById('chat-greeting-bubble');

    if (!chatbotForm) return;

    const aiReplyUrl = chatbotForm.dataset.url;
    const csrfToken = chatbotForm.dataset.csrf;
    const HISTORY_KEY = 'pharmacare_sima_chat_history_v1';

    let chatHistory = [];
    try {
        const saved = sessionStorage.getItem(HISTORY_KEY);
        if (saved) {
            const parsed = JSON.parse(saved);
            if (Array.isArray(parsed)) {
                chatHistory = parsed.slice(-8);
                chatHistory.forEach((entry) => {
                    if (entry && entry.content && (entry.role === 'user' || entry.role === 'assistant')) {
                        appendBubble(entry.content, entry.role === 'user' ? 'user' : 'bot');
                    }
                });
            }
        }
    } catch (e) {
        chatHistory = [];
    }

    function saveHistory() {
        try {
            chatHistory = chatHistory.slice(-8);
            sessionStorage.setItem(HISTORY_KEY, JSON.stringify(chatHistory));
        } catch (e) {
            // Ignore storage quota errors
        }
    }

    window.toggleChat = function() {
        chatWindow.classList.toggle('open');
        if (chatWindow.classList.contains('open')) {
            chatbotInput.focus();
            scrollToBottom();
            if (greetingBubble) greetingBubble.style.display = 'none';
        }
    };

    // --- Greeting Bubble Logic ---
    window.dismissGreetingBubble = function(e) {
        if (e) e.stopPropagation();
        if (greetingBubble) greetingBubble.style.display = 'none';
        localStorage.setItem('pharmacare_sima_bubble_dismissed', 'true');
    };

    const hasBeenDismissed = localStorage.getItem('pharmacare_sima_bubble_dismissed');
    if (!hasBeenDismissed && greetingBubble) {
        setTimeout(() => {
            if (!chatWindow.classList.contains('open')) {
                greetingBubble.style.display = 'block';
            }
        }, 5000);
    }

    function scrollToBottom() {
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    function formatChatMessage(text, side) {
        const escaped = String(text || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        if (side === 'user') {
            return escaped.replace(/\n/g, '<br>');
        }

        return escaped
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.+?)\*/g, '<em>$1</em>')
            .replace(/^[•\-]\s+(.+)$/gm, '&bull; $1')
            .replace(/\n/g, '<br>');
    }

    const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

    async function requestAiWithRetry(msg, historySnapshot) {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }

        let lastData = null;
        for (let attempt = 1; attempt <= 3; attempt++) {
            if (attempt > 1) {
                await sleep(900 * (attempt - 1));
            }

            const response = await fetch(aiReplyUrl, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({
                    message: msg,
                    history: historySnapshot
                })
            });

            if (!response.ok) {
                if (attempt < 3) continue;
                throw new Error('Server returned error ' + response.status);
            }

            lastData = await response.json();
            if (!lastData.retryable) {
                return lastData;
            }
        }

        return lastData;
    }

    chatbotForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = chatbotInput.value.trim();
        if (!msg) return;

        const historySnapshot = chatHistory.slice(-6);

        // Add user bubble
        appendBubble(msg, 'user');
        chatbotInput.value = '';

        // Add typing indicator
        const typingId = 'typing-' + Date.now();
        const typingElem = document.createElement('div');
        typingElem.id = typingId;
        typingElem.className = 'typing-indicator';
        typingElem.style.padding = '0 10px 10px';
        typingElem.innerText = 'SIMA sedang mengetik...';
        chatBody.appendChild(typingElem);
        scrollToBottom();

        try {
            const data = await requestAiWithRetry(msg, historySnapshot);

            const typingIndicator = document.getElementById(typingId);
            if (typingIndicator) typingIndicator.remove();

            appendBubble(data.reply, 'bot');

            if (data.reply && !data.retryable && !data.reply.startsWith('Maaf,') && !data.reply.startsWith('Gagal terhubung')) {
                chatHistory.push({ role: 'user', content: msg });
                chatHistory.push({ role: 'assistant', content: data.reply });
                saveHistory();
            }

        } catch (error) {
            console.error('Chatbot Error:', error);
            const typingIndicator = document.getElementById(typingId);
            if (typingIndicator) typingIndicator.remove();
            appendBubble('Gagal menghubungi SIMA. Silakan coba lagi atau periksa koneksi Anda.', 'bot');
        }
    });

    function appendBubble(text, side) {
        const bubble = document.createElement('div');
        bubble.className = `chat-bubble ${side}`;
        bubble.innerHTML = formatChatMessage(text, side);
        chatBody.appendChild(bubble);
        scrollToBottom();
    }
});
