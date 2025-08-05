<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="description" content="Asvira - Chatbot AI untuk informasi FTI UAP">
    <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Asvira - Chatbot FTI UAP</title>

  <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif']
          },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'bounce-in': 'bounceIn 0.6s ease-out',
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-glow': 'pulseGlow 2s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        bounceIn: {
                            '0%': { transform: 'scale(0.3)', opacity: '0' },
                            '50%': { transform: 'scale(1.05)' },
                            '70%': { transform: 'scale(0.9)' },
                            '100%': { transform: 'scale(1)', opacity: '1' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' }
                        },
                        pulseGlow: {
                            'from': { boxShadow: '0 0 20px rgba(102, 126, 234, 0.4)' },
                            'to': { boxShadow: '0 0 30px rgba(102, 126, 234, 0.8)' }
                        }
          }
        }
      }
    }
  </script>

    <!-- Particles.js -->
  <script src="https://cdn.jsdelivr.net/npm/tsparticles@2.9.3/tsparticles.bundle.min.js"></script>

  <style>
    :root {
      --vh: 100%;
    }

    @keyframes blink {
      0%, 80%, 100% { opacity: 0; }
      40% { opacity: 1; }
    }

    .dot-flashing span {
      animation: blink 1.4s infinite;
      animation-delay: calc(var(--delay, 0s));
      display: inline-block;
      font-weight: bold;
    }

    .dot-flashing span:nth-child(2) { --delay: 0.2s; }
    .dot-flashing span:nth-child(3) { --delay: 0.4s; }

    ::-webkit-scrollbar {
      width: 6px;
    }

    ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
      border-radius: 3px;
    }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
    }

    #tsparticles {
      position: fixed;
      z-index: -1;
      width: 100%;
      height: 100%;
    }

        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .message-bubble {
            animation: slideUp 0.3s ease-out;
        }

        .user-message {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .bot-message {
            background: rgba(255, 255, 255, 0.95);
            color: #1f2937;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .typing-indicator {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .hero-section {
            animation: fadeIn 1s ease-out;
        }

        .faq-button {
            transition: all 0.3s ease;
        }

        .faq-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .send-button {
            transition: all 0.3s ease;
        }

        .send-button:hover {
            transform: scale(1.1);
        }

        .send-button:active {
            transform: scale(0.95);
        }

        /* Mobile optimizations */
        @media (max-width: 640px) {
            .hero-title {
                font-size: 1.75rem;
                line-height: 1.2;
            }
            
            .message-bubble {
                max-width: 90% !important;
            }
            
            .faq-button {
                font-size: 0.75rem;
                padding: 0.5rem 0.75rem;
            }
            
            #heroSection {
                align-items: flex-start;
                padding-top: 2rem;
            }
            
            #heroSection > div {
                margin-top: 3rem !important;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 1.5rem;
            }
            
            .message-bubble {
                max-width: 95% !important;
                padding: 0.75rem 1rem;
            }
            
            .faq-button {
                font-size: 0.7rem;
                padding: 0.4rem 0.6rem;
            }
            
            #heroSection {
                padding-top: 1.5rem;
            }
            
            #heroSection > div {
                margin-top: 2.5rem !important;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .glass-effect {
                background: rgba(0, 0, 0, 0.2);
                border-color: rgba(255, 255, 255, 0.1);
            }
        }

        /* iOS Safari specific fixes */
        @supports (-webkit-touch-callout: none) {
            .min-h-screen {
                min-height: -webkit-fill-available;
            }
            
            body {
                min-height: -webkit-fill-available;
            }
    }
  </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 text-white font-sans relative overflow-hidden">

  <!-- Particle Background -->
  <div id="tsparticles"></div>

  <!-- Header -->
    <header class="w-full px-4 py-3 glass-effect flex justify-between items-center fixed top-0 z-10">
    <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                <span class="text-white font-bold text-lg">🤖</span>
            </div>
            <div>
                <h1 class="text-lg font-bold">Asvira AI</h1>
                <p class="text-xs text-gray-300">Asisten Virtual FTI UAP</p>
            </div>
    </div>
    
        <a href="{{ url('/') }}" class="text-gray-300 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
  </header>

    <!-- Main Content -->
    <div class="flex flex-col h-screen pt-16">
  <!-- Hero Section -->
        <section id="heroSection" class="flex-1 flex flex-col justify-center items-center text-center px-4 hero-section">
            <div class="max-w-md mx-auto space-y-6">
                <!-- Avatar -->
                <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl">🤖</span>
                </div>
                
                <!-- Greeting -->
                <div class="space-y-3">
                    <h1 class="text-2xl sm:text-3xl font-bold">
                        Halo! Saya <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">Asvira</span>
    </h1>
                    <p class="text-sm sm:text-base text-gray-300 leading-relaxed">
                        Asisten Virtual FTI UAP yang siap membantu Anda dengan informasi seputar Fakultas Teknologi Informasi
                    </p>
                </div>
                
                <!-- Quick Actions -->
                <div class="space-y-3">
                    <p class="text-xs text-gray-400 font-medium">Pertanyaan Umum:</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="handleFAQ('Apakah FTI UAP sudah terakreditasi?')" 
                                class="faq-button glass-effect hover:bg-white/20 px-3 py-2.5 rounded-lg text-xs font-medium transition-all">
                            🎓 Akreditasi
                        </button>
                        <button onclick="handleFAQ('Berapa biaya kuliah per semester?')" 
                                class="faq-button glass-effect hover:bg-white/20 px-3 py-2.5 rounded-lg text-xs font-medium transition-all">
                            💰 Biaya
                        </button>
                        <button onclick="handleFAQ('Apakah tersedia kelas malam atau kelas karyawan?')" 
                                class="faq-button glass-effect hover:bg-white/20 px-3 py-2.5 rounded-lg text-xs font-medium transition-all">
                            🌙 Kelas Karyawan
                        </button>
                        <button onclick="handleFAQ('Kapan jadwal pendaftaran dibuka?')" 
                                class="faq-button glass-effect hover:bg-white/20 px-3 py-2.5 rounded-lg text-xs font-medium transition-all">
                            📅 Pendaftaran
                        </button>
                    </div>
                </div>
    </div>
  </section>

        <!-- Chat Container -->
        <main id="chatLog" class="flex-1 w-full max-w-4xl mx-auto px-4 space-y-4 overflow-y-auto pb-24" style="display: none;"></main>
    </div>

    <!-- Input Container -->
    <div id="inputContainer" class="fixed bottom-0 left-0 right-0 z-30 bg-gradient-to-t from-slate-900/95 to-transparent pt-6 pb-4">
        <div class="w-full max-w-4xl mx-auto px-4">
    <div class="relative">
                <input id="inputMessage" 
                       type="text" 
                       placeholder="Tulis pertanyaan Anda di sini..."
                       class="w-full py-3 pl-4 pr-12 rounded-2xl glass-effect text-white placeholder:text-gray-300 placeholder:font-medium focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all text-sm" />
      <button id="sendBtn"
                        class="send-button absolute top-1/2 right-2 -translate-y-1/2 w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
      </button>
            </div>
    </div>
  </div>

  <!-- Script -->
  <script>
    const chatLog = document.getElementById('chatLog');
    const input = document.getElementById('inputMessage');
    const sendBtn = document.getElementById('sendBtn');
    const heroSection = document.getElementById('heroSection');
    const inputContainer = document.getElementById('inputContainer');
    let isFirstMessage = true;
        let initialViewportHeight = window.innerHeight;

    function fixMobileViewportHeight() {
      const vh = window.innerHeight * 0.01;
      document.documentElement.style.setProperty('--vh', `${vh}px`);
    }

        function handleKeyboard() {
            const currentHeight = window.innerHeight;
            const heightDifference = initialViewportHeight - currentHeight;
            
            if (heightDifference > 150) {
                // Keyboard is visible
                inputContainer.style.bottom = `${heightDifference}px`;
            } else {
                // Keyboard is hidden
                inputContainer.style.bottom = '0px';
            }
        }

        // Initialize particles
        tsParticles.load("tsparticles", {
            fpsLimit: 60,
            particles: {
                number: {
                    value: 50,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: ["#667eea", "#764ba2"]
                },
                shape: {
                    type: "circle"
                },
                opacity: {
                    value: 0.3,
                    random: false
                },
                size: {
                    value: 3,
                    random: true
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#667eea",
                    opacity: 0.2,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: "none",
                    random: false,
                    straight: false,
                    out_mode: "out",
                    bounce: false
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: {
                        enable: true,
                        mode: "repulse"
                    },
                    onclick: {
                        enable: true,
                        mode: "push"
                    },
                    resize: true
                },
                modes: {
                    repulse: {
                        distance: 100,
                        duration: 0.4
                    },
                    push: {
                        particles_nb: 4
                    }
                }
            },
            retina_detect: true
        });

        // Event listeners
        input.addEventListener('focus', () => {
            setTimeout(handleKeyboard, 100);
        });

        input.addEventListener('blur', () => {
            setTimeout(handleKeyboard, 100);
        });

        window.addEventListener('resize', handleKeyboard);
        window.addEventListener('orientationchange', () => {
            setTimeout(handleKeyboard, 100);
        });

        // Fix viewport height on load
      fixMobileViewportHeight();
        window.addEventListener('resize', fixMobileViewportHeight);

        function sendMessage() {
      const message = input.value.trim();
      if (!message) return;

            // Hide hero section and show chat on first message
      if (isFirstMessage) {
        heroSection.style.display = 'none';
                chatLog.style.display = 'block';
        isFirstMessage = false;
      }

            // Add user message
            addMessage(message, 'user');
            input.value = '';

            // Show typing indicator
            showTypingIndicator();

            // Send to server
      fetch('/api/chatbot/send', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
                body: JSON.stringify({ message: message })
      })
            .then(response => response.json())
        .then(data => {
                hideTypingIndicator();
                addMessage(data.reply, 'bot');
            })
            .catch(error => {
                hideTypingIndicator();
                addMessage('Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot');
                console.error('Error:', error);
            });
        }

        function addMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message-bubble ${sender}-message rounded-2xl px-4 py-3 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg ${sender === 'user' ? 'ml-auto' : 'mr-auto'}`;
            messageDiv.textContent = text;
            chatLog.appendChild(messageDiv);
            chatLog.scrollTop = chatLog.scrollHeight;
        }

        function showTypingIndicator() {
            const typingDiv = document.createElement('div');
            typingDiv.id = 'typingIndicator';
            typingDiv.className = 'typing-indicator rounded-2xl px-4 py-3 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mr-auto';
            typingDiv.innerHTML = '<div class="dot-flashing text-gray-600"><span>.</span><span>.</span><span>.</span></div>';
            chatLog.appendChild(typingDiv);
            chatLog.scrollTop = chatLog.scrollHeight;
        }

        function hideTypingIndicator() {
            const typingIndicator = document.getElementById('typingIndicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }

        function handleFAQ(question) {
            input.value = question;
            sendMessage();
        }

        // Event listeners for send
        sendBtn.addEventListener('click', sendMessage);
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
    });
  </script>
</body>
</html>
