@extends('layouts.app')

@section('title', 'HydroNova AI Assistant')

@section('content')
<style>
    .assistant-hero {
        background: linear-gradient(135deg, rgba(45, 170, 158, 0.12), rgba(33, 141, 131, 0.14));
    }

    .chat-window {
        background: #f5fbf9;
        border: 1px solid rgba(33, 141, 131, 0.08);
        border-radius: 16px;
        padding: 18px;
        max-height: 520px;
        min-height: 380px;
        overflow-y: auto;
        scroll-behavior: smooth;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .chat-bubble {
        border-radius: 14px;
        padding: 12px 14px;
        max-width: 80%;
        font-size: 0.97rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
        white-space: pre-wrap;
        line-height: 1.5;
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
</style>

<div class="container py-5" style="min-height: calc(100vh - 120px);">
    <div class="row justify-content-center h-100">
        <div class="col-xl-7 col-lg-8 d-flex">
            <div class="card shadow-sm border-0 assistant-hero w-100 h-100">
                <div class="card-header bg-transparent border-0 py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="chat-avatar mb-0">HN</div>
                        <div>
                            <h5 class="mb-0 fw-bold text-teal">HydroNova AI Assistant</h5>
                            <small class="text-muted">Personalized hydroponic advice for your space and budget</small>
                        </div>
                    </div>
                    <span class="badge text-bg-light border text-secondary">Beta</span>
                </div>
                <div class="card-body">
                    <div id="chatWindow" class="chat-window mb-3">
                        <div class="d-flex align-items-start">
                            <div class="chat-avatar">HN</div>
                            <div class="chat-bubble assistant">Hello, I'm the HydroNova AI Assistant. Ask me anything.</div>
                        </div>
                    </div>

                    <form id="assistantForm" class="chat-input-area p-3">
                        <label for="assistantMessage" class="form-label text-muted small mb-2">Ask anything about HydroNova systems, nutrients, or plans</label>
                        <div class="d-flex align-items-end gap-2">
                            <textarea
                                id="assistantMessage"
                                class="form-control"
                                rows="3"
                                placeholder="Type your question..."
                                required
                                aria-label="Your message to the HydroNova assistant"
                            ></textarea>
                            <button type="submit" id="assistantSend" class="btn btn-teal px-4 d-flex align-items-center gap-2">
                                <span id="assistantSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span id="assistantSendText">Send</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const endpoint = 'http://192.168.0.104:5678/webhook/hydronova-chat';
        const sessionId = `hydronova-${Date.now()}-${Math.random().toString(16).slice(2, 8)}`;
        const chatWindow = document.getElementById('chatWindow');
        const form = document.getElementById('assistantForm');
        const messageInput = document.getElementById('assistantMessage');
        const sendButton = document.getElementById('assistantSend');
        const sendText = document.getElementById('assistantSendText');
        const spinner = document.getElementById('assistantSpinner');
        let typingNode = null;
        let isSending = false;

        const scrollToBottom = () => {
            chatWindow.scrollTop = chatWindow.scrollHeight;
        };

        const setLoading = (isLoading) => {
            sendButton.disabled = isLoading;
            spinner.classList.toggle('d-none', !isLoading);
            sendText.textContent = isLoading ? 'Sending' : 'Send';
            messageInput.disabled = isLoading;
        };

        const createBubble = (text, role) => {
            const wrapper = document.createElement('div');
            const bubble = document.createElement('div');

            bubble.className = `chat-bubble ${role === 'user' ? 'user' : 'assistant'}`;
            bubble.textContent = text;

            if (role === 'user') {
                wrapper.className = 'd-flex justify-content-end text-end';
                wrapper.appendChild(bubble);
            } else {
                wrapper.className = 'd-flex align-items-start';
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
            text.textContent = 'HydroNova Assistant is thinking';

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

        const sendMessage = async () => {
            const message = messageInput.value.trim();
            if (!message || isSending) {
                return;
            }

            createBubble(message, 'user');
            messageInput.value = '';
            setLoading(true);
            isSending = true;
            showTypingIndicator();

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message, sessionId }),
                });

                const data = await response.json().catch(() => null);
                const assistantReply = data && typeof data.output === 'string' ? data.output.trim() : '';

                removeTypingIndicator();

                if (!response.ok || !assistantReply) {
                    throw new Error('Invalid response');
                }

                createBubble(assistantReply, 'assistant');
            } catch (error) {
                removeTypingIndicator();
                createBubble('Sorry, I had a problem answering. Please try again.', 'assistant');
            } finally {
                isSending = false;
                setLoading(false);
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
