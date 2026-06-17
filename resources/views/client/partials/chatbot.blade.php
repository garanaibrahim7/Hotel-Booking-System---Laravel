<!-- Floating Action Button -->
<div id="chatbot-launcher" class="chatbot-launcher-btn shadow-lg" onclick="toggleChatbot()">
    <div class="launcher-icon">
        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="36" height="36">
            <rect width="64" height="64" rx="32" fill="url(#gradient-launcher)"/>
            <path d="M42 34v-4a10 10 0 0 0-20 0v4" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
            <rect x="19" y="32" width="4" height="6" rx="2" fill="#FFFFFF"/>
            <rect x="41" y="32" width="4" height="6" rx="2" fill="#FFFFFF"/>
            <circle cx="32" cy="30" r="6" fill="#FFFFFF"/>
            <path d="M30 42h4" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/>
            <defs>
                <linearGradient id="gradient-launcher" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#1e293b"/>
                    <stop offset="1" stop-color="#c5a880"/>
                </linearGradient>
            </defs>
        </svg>
    </div>
    <span class="launcher-tooltip">Ask Diksha</span>
    <span class="pulse-ring"></span>
</div>

<!-- Chat Window -->
<div id="chatbot-container" class="chatbot-window shadow-2xl border border-light">
    <!-- Header -->
    <div class="chatbot-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="chatbot-avatar-small">
                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="32" height="32">
                    <rect width="64" height="64" rx="32" fill="url(#gradient-header-avatar)"/>
                    <path d="M42 34v-4a10 10 0 0 0-20 0v4" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
                    <rect x="19" y="32" width="4" height="6" rx="2" fill="#FFFFFF"/>
                    <rect x="41" y="32" width="4" height="6" rx="2" fill="#FFFFFF"/>
                    <circle cx="32" cy="30" r="6" fill="#FFFFFF"/>
                    <path d="M30 42h4" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/>
                    <defs>
                        <linearGradient id="gradient-header-avatar" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#1e293b"/>
                            <stop offset="1" stop-color="#c5a880"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <div>
                <h6 class="m-0 fw-bold text-white fs-6">Ask Diksha</h6>
                <div class="d-flex align-items-center gap-1">
                    <span class="online-indicator"></span>
                    <span class="text-white-50 tiny" style="font-size: 10px;">Virtual Assistant</span>
                </div>
            </div>
        </div>
        <button type="button" class="chatbot-close-btn" onclick="toggleChatbot()">&times;</button>
    </div>

    <!-- Message Container -->
    <div id="chatbot-messages" class="chatbot-messages-body">
        <div class="chatbot-msg chatbot-msg-bot">
            <div class="chatbot-bubble">
                Namaste! 🙏 I am <strong>Diksha</strong>, your virtual assistant for <strong>XYZ Hotels</strong>.<br><br>How can I help you today? You can search hotels, check room types, or track your booking status.
            </div>
        </div>
        
        <!-- Options List -->
        <div class="chatbot-options-container" id="initial-options">
            <button onclick="sendQuickMessage('show hotels')" class="chatbot-opt-btn">🏨 Search Hotels</button>
            <button onclick="sendQuickMessage('room types')" class="chatbot-opt-btn">🛏️ Check Room Types</button>
            <button onclick="sendQuickMessage('booking status')" class="chatbot-opt-btn">📅 Track Booking Status</button>
            <button onclick="sendQuickMessage('cancellation policy')" class="chatbot-opt-btn">💳 Cancellation & Refund</button>
        </div>
    </div>

    <!-- Input Footer -->
    <div class="chatbot-footer d-flex gap-2 align-items-center">
        <input type="text" id="chatbot-input" class="form-control form-control-sm border-0" placeholder="Ask Diksha a question..." onkeypress="handleInputKeypress(event)">
        <button id="chatbot-send-btn" class="btn btn-dark btn-sm rounded-circle d-flex align-items-center justify-content-center" onclick="sendUserMessage()">
            <i class="bi bi-send-fill text-gold-accent"></i>
        </button>
    </div>
</div>

<style>
/* Launcher Button */
.chatbot-launcher-btn {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    cursor: pointer;
    z-index: 1050;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    display: flex;
    align-items: center;
    justify-content: center;
    background: #1e293b;
    border: 2px solid #c5a880;
}
.chatbot-launcher-btn:hover {
    transform: scale(1.1);
}
.launcher-icon {
    display: flex;
    align-items: center;
    justify-content: center;
}
.launcher-tooltip {
    position: absolute;
    right: 70px;
    background: #1e293b;
    color: #fff;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(197, 168, 128, 0.3);
}
.chatbot-launcher-btn:hover .launcher-tooltip {
    opacity: 1;
}

/* Pulse Ring Animation */
.pulse-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 2px solid #c5a880;
    animation: chatbot-pulse 2s infinite ease-out;
    opacity: 0;
    pointer-events: none;
}
@keyframes chatbot-pulse {
    0% {
        transform: scale(0.95);
        opacity: 0.8;
    }
    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}

/* Chat Window */
.chatbot-window {
    position: fixed;
    bottom: 96px;
    right: 24px;
    width: 360px;
    height: 500px;
    border-radius: 16px;
    z-index: 1050;
    background: #ffffff;
    display: none;
    flex-direction: column;
    overflow: hidden;
    transition: all 0.3s ease;
    opacity: 0;
    transform: translateY(20px) scale(0.95);
    border: 1px solid rgba(197, 168, 128, 0.2);
}
.chatbot-window.open {
    display: flex;
    opacity: 1;
    transform: translateY(0) scale(1);
}

/* Header */
.chatbot-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    padding: 12px 16px;
    border-bottom: 2px solid #c5a880;
    color: #ffffff;
}
.online-indicator {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 6px #10b981;
}
.chatbot-close-btn {
    background: none;
    border: none;
    color: #ffffff;
    font-size: 24px;
    cursor: pointer;
    line-height: 1;
    opacity: 0.8;
    transition: opacity 0.2s;
}
.chatbot-close-btn:hover {
    opacity: 1;
}

/* Messages Body */
.chatbot-messages-body {
    flex-grow: 1;
    padding: 16px;
    overflow-y: auto;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Custom Scrollbar */
.chatbot-messages-body::-webkit-scrollbar {
    width: 6px;
}
.chatbot-messages-body::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.chatbot-messages-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}
.chatbot-messages-body::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Message Bubbles */
.chatbot-msg {
    display: flex;
    flex-direction: column;
    max-width: 85%;
}
.chatbot-msg-bot {
    align-self: flex-start;
}
.chatbot-msg-user {
    align-self: flex-end;
}
.chatbot-bubble {
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 13px;
    line-height: 1.5;
    position: relative;
    word-break: break-word;
}
.chatbot-msg-bot .chatbot-bubble {
    background: #ffffff;
    color: #334155;
    border: 1px solid #e2e8f0;
    border-top-left-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.chatbot-msg-user .chatbot-bubble {
    background: #1e293b;
    color: #ffffff;
    border-top-right-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* Options/Buttons List */
.chatbot-options-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 4px;
    margin-bottom: 4px;
    align-self: flex-start;
}
.chatbot-opt-btn {
    background: #ffffff;
    color: #1e293b;
    border: 1px solid #c5a880;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 500;
}
.chatbot-opt-btn:hover {
    background: #1e293b;
    color: #ffffff;
    border-color: #1e293b;
}

/* Typing Indicator Animation */
.chatbot-typing-indicator {
    display: flex;
    gap: 4px;
    padding: 10px 14px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    border-top-left-radius: 2px;
    align-self: flex-start;
}
.chatbot-typing-dot {
    width: 6px;
    height: 6px;
    background: #94a3b8;
    border-radius: 50%;
    animation: typing-bounce 1.4s infinite ease-in-out both;
}
.chatbot-typing-dot:nth-child(1) { animation-delay: -0.32s; }
.chatbot-typing-dot:nth-child(2) { animation-delay: -0.16s; }
@keyframes typing-bounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1.0); }
}

/* Footer Input */
.chatbot-footer {
    padding: 12px 16px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
}
#chatbot-input {
    background: #f1f5f9;
    border-radius: 20px;
    padding: 8px 16px;
    font-size: 13px;
}
#chatbot-input:focus {
    box-shadow: none;
    background: #f1f5f9;
}
#chatbot-send-btn {
    width: 34px;
    height: 34px;
    background: #1e293b;
    border: none;
    transition: all 0.2s ease;
}
#chatbot-send-btn:hover {
    background: #c5a880;
}
.text-gold-accent {
    color: #c5a880 !important;
}
#chatbot-send-btn:hover .text-gold-accent {
    color: #ffffff !important;
}

/* Custom styles for cards returned from server */
.chatbot-booking-card, .chatbot-hotel-item, .chatbot-room-item {
    border-color: rgba(197, 168, 128, 0.4) !important;
    font-family: inherit;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
}
.tiny {
    font-size: 10px;
}
.chatbot-bullet {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-left: 8px;
    font-size: 12px;
}
.text-gold-accent {
    color: #c5a880;
}
</style>

<script>
function toggleChatbot() {
    const chatWindow = document.getElementById('chatbot-container');
    chatWindow.classList.toggle('open');
    if (chatWindow.classList.contains('open')) {
        document.getElementById('chatbot-input').focus();
        scrollToBottom();
    }
}

function scrollToBottom() {
    const msgBody = document.getElementById('chatbot-messages');
    msgBody.scrollTop = msgBody.scrollHeight;
}

function handleInputKeypress(e) {
    if (e.key === 'Enter') {
        sendUserMessage();
    }
}

function parseMarkdown(text) {
    if (!text) return "";
    let html = text;
    // Bold **text**
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    // Bullet points starting with -
    html = html.replace(/^-\s(.*?)$/gm, '<div class="chatbot-bullet"><i class="bi bi-dot text-gold-accent fs-5"></i> $1</div>');
    // Inline code blocks `code`
    html = html.replace(/`(.*?)`/g, '<code class="bg-dark text-light px-1 rounded" style="font-size: 85%;">$1</code>');
    // Headings starting with ###
    html = html.replace(/### (.*?)/g, '<h6 class="fw-bold my-2 text-gold-accent">$1</h6>');
    // Newlines
    html = html.replace(/\n/g, '<br>');
    return html;
}

function appendMessage(text, isUser, htmlContent = null) {
    const msgBody = document.getElementById('chatbot-messages');
    
    // Remove previous typing indicator if it exists
    removeTypingIndicator();

    const msgDiv = document.createElement('div');
    msgDiv.className = `chatbot-msg ${isUser ? 'chatbot-msg-user' : 'chatbot-msg-bot'}`;

    const bubble = document.createElement('div');
    bubble.className = 'chatbot-bubble';
    
    if (isUser) {
        bubble.textContent = text;
    } else {
        bubble.innerHTML = parseMarkdown(text);
    }
    
    msgDiv.appendChild(bubble);

    if (htmlContent) {
        const customHtml = document.createElement('div');
        customHtml.className = 'chatbot-custom-html my-2';
        customHtml.innerHTML = htmlContent;
        msgDiv.appendChild(customHtml);
    }

    msgBody.appendChild(msgDiv);
    scrollToBottom();
}

function appendOptions(options) {
    if (!options || options.length === 0) return;
    
    const msgBody = document.getElementById('chatbot-messages');
    const optContainer = document.createElement('div');
    optContainer.className = 'chatbot-options-container';

    options.forEach(opt => {
        const btn = document.createElement('button');
        btn.className = 'chatbot-opt-btn';
        btn.textContent = opt.label;
        btn.onclick = () => sendQuickMessage(opt.value);
        optContainer.appendChild(btn);
    });

    msgBody.appendChild(optContainer);
    scrollToBottom();
}

function showTypingIndicator() {
    const msgBody = document.getElementById('chatbot-messages');
    
    // Check if it already exists
    if (document.getElementById('chatbot-typing')) return;

    const indicator = document.createElement('div');
    indicator.id = 'chatbot-typing';
    indicator.className = 'chatbot-typing-indicator';

    for (let i = 0; i < 3; i++) {
        const dot = document.createElement('div');
        dot.className = 'chatbot-typing-dot';
        indicator.appendChild(dot);
    }

    msgBody.appendChild(indicator);
    scrollToBottom();
}

function removeTypingIndicator() {
    const indicator = document.getElementById('chatbot-typing');
    if (indicator) {
        indicator.remove();
    }
}

function sendQuickMessage(value) {
    appendMessage(value, true);
    fetchResponse(value);
}

function sendUserMessage() {
    const input = document.getElementById('chatbot-input');
    const msg = input.value.trim();
    if (!msg) return;

    appendMessage(msg, true);
    input.value = '';
    fetchResponse(msg);
}

function fetchResponse(userMsg) {
    showTypingIndicator();

    fetch('/chatbot/message', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: userMsg })
    })
    .then(res => res.json())
    .then(data => {
        appendMessage(data.text, false, data.html);
        if (data.options) {
            appendOptions(data.options);
        }
    })
    .catch(err => {
        console.error("Chatbot Error:", err);
        appendMessage("Sorry, I encountered an issue. Please try again shortly.", false);
    });
}
</script>
