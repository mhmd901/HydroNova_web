@extends('layouts.app')

@section('title', 'HydroNova AI Assistant')

@section('content')
<style>
    .assistant-hero {
        background: linear-gradient(135deg, rgba(45, 170, 158, 0.12), rgba(33, 141, 131, 0.12));
        border-radius: 18px;
    }

    .chat-window {
        background: #f5fbf9;
        border: 1px solid rgba(33, 141, 131, 0.08);
        border-radius: 16px;
        padding: 18px;
        max-height: 520px;
        min-height: 420px;
        overflow-y: auto;
        scroll-behavior: smooth;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .chat-bubble {
        border-radius: 14px;
        padding: 12px 14px;
        max-width: 85%;
        font-size: 0.97rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
        line-height: 1.5;
        word-break: break-word;
    }

    .chat-bubble.assistant {
        background: #e7f7f4;
        color: #0f4d45;
        border: 1px solid rgba(45, 170, 158, 0.16);
    }

    .chat-bubble.user {
        background: linear-gradient(135deg, #2daa9e, #218d83);
        color: #fff;
    }

    .chat-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #2daa9e;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-right: 10px;
        flex-shrink: 0;
        box-shadow: 0 8px 24px rgba(45, 170, 158, 0.2);
    }

    .chat-input-area {
        background: #f8fbfa;
        border: 1px solid rgba(33, 141, 131, 0.12);
        border-radius: 14px;
    }

    .chat-input-area textarea {
        border: none;
        resize: none;
        background: transparent;
        box-shadow: none;
    }

    .chat-input-area textarea:focus {
        box-shadow: none;
    }

    .typing-indicator {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #0f4d45;
    }

    .typing-dots {
        display: inline-flex;
        gap: 6px;
    }

    .typing-dots span {
        width: 6px;
        height: 6px;
        background: #218d83;
        border-radius: 50%;
        display: inline-block;
        animation: dotPulse 1.3s infinite ease-in-out;
    }

    .typing-dots span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-dots span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes dotPulse {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.6; }
        30% { transform: translateY(-4px); opacity: 1; }
    }

    .connection-log {
        min-height: 140px;
        max-height: 240px;
        overflow-y: auto;
    }
</style>

<div class="container py-5" style="min-height: calc(100vh - 120px);">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div>
                    <h2 class="fw-bold text-teal mb-1">HydroNova AI Assistant</h2>
                    <p class="text-muted mb-0">Chat safely with the  assistant. </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 assistant-hero h-100">
                        <div class="card-body">
                            <div id="chatWindow" class="chat-window mb-3" aria-live="polite">
                                <div class="d-flex align-items-start">
                                    <div class="chat-avatar">HN</div>
                                    <div class="chat-bubble assistant">Hello, I'm the HydroNova Assistant. Ask me anything about systems, nutrients, or plans.</div>
                                </div>
                            </div>

                            <form id="assistantForm" class="chat-input-area p-3">
                                @csrf
                                <label for="assistantMessage" class="form-label text-muted small mb-2">Press Enter to send, Shift + Enter for a new line</label>
                                <div class="d-flex align-items-end gap-2">
                                    <textarea
                                        id="assistantMessage"
                                        class="form-control"
                                        rows="3"
                                        maxlength="2000"
                                        placeholder="Type your question..."
                                        required
                                        aria-label="Your message to the HydroNova assistant"
                                    ></textarea>
                                    <button type="submit" id="assistantSend" class="btn btn-teal px-4 d-flex align-items-center gap-2">
                                        <span id="assistantSendSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        <span id="assistantSendText">Send</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-stars text-teal"></i>
                                <span>Tips for better replies</span>
                            </h6>
                            <ul class="small text-muted mb-0 ps-3">
                                <li>Describe your setup or room size for tailored guidance.</li>
                                <li>Mention your goals (yield, budget, maintenance time).</li>
                                <li>Ask follow-ups to refine nutrient schedules.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const endpoint = @json(route('assistant.message'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const chatWindow = document.getElementById('chatWindow');
        const form = document.getElementById('assistantForm');
        const messageInput = document.getElementById('assistantMessage');
        const sendButton = document.getElementById('assistantSend');
        const sendText = document.getElementById('assistantSendText');
        const sendSpinner = document.getElementById('assistantSendSpinner');

        let typingNode = null;
        let isSending = false;

        const escapeHtml = (unsafe) => unsafe
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        const formatMessage = (text) => escapeHtml(text).replace(/\n/g, '<br>');

        const scrollToBottom = () => {
            chatWindow.scrollTop = chatWindow.scrollHeight;
        };

        const createBubble = (text, role) => {
            const wrapper = document.createElement('div');
            wrapper.className = role === 'user' ? 'd-flex justify-content-end text-end' : 'd-flex align-items-start';

            const bubble = document.createElement('div');
            bubble.className = `chat-bubble ${role === 'user' ? 'user' : 'assistant'}`;
            bubble.innerHTML = formatMessage(text);

            if (role === 'user') {
                wrapper.appendChild(bubble);
            } else {
                const avatar = document.createElement('div');
                avatar.className = 'chat-avatar';
                avatar.textContent = 'HN';
                wrapper.appendChild(avatar);
                wrapper.appendChild(bubble);
            }

            chatWindow.appendChild(wrapper);
            scrollToBottom();
        };

        const showTypingIndicator = () => {
            if (typingNode) {
                typingNode.remove();
            }

            const wrapper = document.createElement('div');
            wrapper.className = 'd-flex align-items-start';

            const avatar = document.createElement('div');
            avatar.className = 'chat-avatar';
            avatar.textContent = 'HN';

            const bubble = document.createElement('div');
            bubble.className = 'chat-bubble assistant';

            const text = document.createElement('div');
            text.className = 'typing-indicator';
            text.textContent = 'HydroNova Assistant is typing...';

            const dots = document.createElement('div');
            dots.className = 'typing-dots';
            dots.innerHTML = '<span></span><span></span><span></span>';

            text.appendChild(dots);
            bubble.appendChild(text);
            wrapper.appendChild(avatar);
            wrapper.appendChild(bubble);
            chatWindow.appendChild(wrapper);
            typingNode = wrapper;
            scrollToBottom();
        };

        const removeTypingIndicator = () => {
            if (typingNode) {
                typingNode.remove();
                typingNode = null;
            }
        };

        const setSendingState = (state) => {
            isSending = state;
            sendButton.disabled = state;
            messageInput.disabled = state;
            sendSpinner.classList.toggle('d-none', !state);
            sendText.textContent = state ? 'Sending...' : 'Send';
        };

        const sendMessage = async () => {
            const message = messageInput.value.trim();
            if (!message || isSending) {
                return;
            }

            createBubble(message, 'user');
            messageInput.value = '';
            showTypingIndicator();
            setSendingState(true);

            let responseJson = null;

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                    },
                    body: JSON.stringify({ message }),
                });

                responseJson = await response.json().catch(() => null);

                if (!response.ok || !responseJson || typeof responseJson.output !== 'string') {
                    throw new Error(responseJson?.error || `Unexpected response (HTTP ${response.status})`);
                }

                removeTypingIndicator();
                createBubble(responseJson.output, 'assistant');
            } catch (error) {
                console.error('Assistant request failed', { error, response: responseJson });
                removeTypingIndicator();
                createBubble('I could not reach HydroNova Assistant right now. Please try again.', 'assistant');
            } finally {
                setSendingState(false);
                messageInput.focus();
            }
        };

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            sendMessage();
        });

        messageInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        });

        scrollToBottom();
    });
</script>
@endsection
