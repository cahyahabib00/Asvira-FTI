import './bootstrap';

// Global utility functions
window.AsviraUtils = {
    // Debounce function for performance
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Throttle function for performance
    throttle: function(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    // Smooth scroll to element
    smoothScrollTo: function(element, offset = 0) {
        if (element) {
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - offset;
            
            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    },

    // Format date
    formatDate: function(date) {
        return new Intl.DateTimeFormat('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(date);
    },

    // Copy to clipboard
    copyToClipboard: function(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text);
        } else {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        }
    },

    // Show notification
    showNotification: function(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all transform translate-x-full ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-black' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
};

// Chatbot functionality
window.AsviraChatbot = {
    chatLog: null,
    input: null,
    sendBtn: null,
    heroSection: null,
    inputContainer: null,
    isFirstMessage: true,

    init: function() {
        this.chatLog = document.getElementById('chatLog');
        this.input = document.getElementById('inputMessage');
        this.sendBtn = document.getElementById('sendBtn');
        this.heroSection = document.getElementById('heroSection');
        this.inputContainer = document.getElementById('inputContainer');

        if (this.chatLog && this.input && this.sendBtn) {
            this.setupEventListeners();
            this.fixMobileViewportHeight();
            this.adjustLayout();
        }
    },

    setupEventListeners: function() {
        this.sendBtn.addEventListener('click', () => this.sendMessage());
        this.input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });

        window.addEventListener('resize', AsviraUtils.debounce(() => this.adjustLayout(), 100));
        this.input.addEventListener('focus', () => this.adjustLayout());
        this.input.addEventListener('blur', () => {
            this.inputContainer.style.bottom = `calc(env(safe-area-inset-bottom,0px) + 16px)`;
            this.adjustLayout();
        });
    },

    fixMobileViewportHeight: function() {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    },

    addBubble: function(message, isUser = true) {
        const bubble = document.createElement('div');
        bubble.className = `message-bubble max-w-[85%] px-6 py-4 rounded-2xl shadow-lg ${
            isUser ? 'user-message ml-auto' : 'bot-message mr-auto'
        }`;

        // Format message with markdown-like syntax
        let formattedMessage = message
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');

        bubble.innerHTML = formattedMessage;
        this.chatLog.appendChild(bubble);
        this.chatLog.scrollTop = this.chatLog.scrollHeight;
    },

    addTypingIndicator: function(id) {
        const typing = document.createElement('div');
        typing.id = id;
        typing.className = 'message-bubble max-w-[85%] px-6 py-4 rounded-2xl mr-auto typing-indicator shadow-lg flex items-center';
        typing.innerHTML = `
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-pulse"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
            </div>
        `;
        this.chatLog.appendChild(typing);
        this.chatLog.scrollTop = this.chatLog.scrollHeight;
    },

    removeTypingIndicator: function(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    },

    handleFAQ: function(question) {
        this.input.value = question;
        this.sendMessage();
    },

    adjustLayout: function() {
        this.fixMobileViewportHeight();
        const keyboardHeight = window.innerHeight - document.documentElement.clientHeight;
        const offset = keyboardHeight > 100 ? keyboardHeight + 8 : 0;
        this.inputContainer.style.bottom = `${offset + 16}px`;
        this.chatLog.style.maxHeight = `calc(var(--vh,1vh)*100 - ${this.inputContainer.offsetHeight + 120}px)`;
        this.chatLog.scrollTop = this.chatLog.scrollHeight;
    },

    sendMessage: function() {
        const message = this.input.value.trim();
        if (!message) return;

        this.addBubble(message, true);
        this.input.value = '';
        
        if (this.isFirstMessage) {
            this.heroSection.style.display = 'none';
            this.isFirstMessage = false;
        }

        const typingId = `typing-${Date.now()}`;
        this.addTypingIndicator(typingId);

        fetch('/chatbot/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ message })
        })
        .then(res => res.json())
        .then(data => {
            this.removeTypingIndicator(typingId);
            this.addBubble(data.reply, false);
        })
        .catch(() => {
            this.removeTypingIndicator(typingId);
            this.addBubble("Maaf, terjadi kesalahan saat mengambil jawaban. Silakan coba lagi.", false);
        });
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize chatbot if on chatbot page
    if (document.getElementById('chatLog')) {
        AsviraChatbot.init();
    }

    // Initialize smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                AsviraUtils.smoothScrollTo(target, 80);
            }
        });
    });

    // Initialize navigation scroll effect
    const nav = document.querySelector('nav');
    if (nav) {
        window.addEventListener('scroll', AsviraUtils.throttle(function() {
            if (window.scrollY > 50) {
                nav.classList.add('bg-white/90', 'dark:bg-gray-900/90');
            } else {
                nav.classList.remove('bg-white/90', 'dark:bg-gray-900/90');
            }
        }, 100));
    }
});
