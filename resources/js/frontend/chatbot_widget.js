document.addEventListener('DOMContentLoaded', function() {
    const chatWindow = document.getElementById('chatWindow');
    const chatBody = document.getElementById('chatBody');
    const chatbotForm = document.getElementById('chatbotForm');
    const chatbotInput = document.getElementById('chatbotInput');
    const greetingBubble = document.getElementById('chat-greeting-bubble');

    if (!chatbotForm) return;

    const aiReplyUrl = chatbotForm.dataset.url;
    const csrfToken = chatbotForm.dataset.csrf;

    window.toggleChat = function() {
        chatWindow.classList.toggle('open');
        if (chatWindow.classList.contains('open')) {
            chatbotInput.focus();
            scrollToBottom();
            // Hide bubble when chat is opened
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
        }, 5000); // Show after 5 seconds
    }

    function scrollToBottom() {
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    chatbotForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = chatbotInput.value.trim();
        if (!msg) return;

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
            const headers = {
                'Content-Type': 'application/json'
            };

            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            const response = await fetch(aiReplyUrl, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({ message: msg })
            });

            if (!response.ok) {
                throw new Error('Server returned error ' + response.status);
            }

            const data = await response.json();
            
            // Remove typing indicator
            const typingIndicator = document.getElementById(typingId);
            if (typingIndicator) typingIndicator.remove();

            // Add bot bubble
            appendBubble(data.reply, 'bot');

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
        bubble.innerHTML = text.replace(/\n/g, '<br>');
        chatBody.appendChild(bubble);
        scrollToBottom();
    }
});
