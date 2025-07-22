<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Asvira - Chatbot FTI UAP</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Tailwind Config -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif']
          },
          fontSize: {
            'xs': '0.75rem',
            'sm': '0.875rem',
            'base': '1rem',
            'lg': '1.125rem',
            'xl': '1.25rem',
            '2xl': '1.5rem',
            '3xl': '1.875rem',
            '4xl': '2.25rem',
            '5xl': '3rem',
            '6xl': '3.75rem'
          }
        }
      }
    }
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/tsparticles@2.9.3/tsparticles.bundle.min.js"></script>

  <style>
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
      background: #666;
      border-radius: 3px;
    }

    #tsparticles {
      position: fixed;
      z-index: -1;
      width: 100%;
      height: 100%;
    }

    .hero-title span {
      background: linear-gradient(to right, #8b5cf6, #3b82f6);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    #inputContainer {
      z-index: 30;
      backdrop-filter: blur(5px);
      background-color: rgba(255, 255, 255, 0.2);
    }

    #chatLog {
      z-index: 20;
    }
  </style>
</head>
<body class="min-h-screen bg-[#0e0b20] text-white font-sans flex flex-col items-center relative overflow-hidden">

  <!-- Particle Background -->
  <div id="tsparticles"></div>

  <!-- Header -->
  <header class="w-full px-6 py-4 backdrop-blur-sm bg-white/5 border-b border-white/10 flex justify-between items-center fixed top-0 z-10">
    <div class="flex items-center space-x-3">
      <div class="bg-white text-black font-bold px-2 py-1 rounded-full">🤖</div>
      <h1 class="text-xl font-bold">Asvira AI</h1>
    </div>
    <button class="bg-gradient-to-r from-purple-600 to-blue-500 px-4 py-2 rounded-xl text-base font-semibold shadow hover:scale-105 transition">
      Get Started
    </button>
  </header>

  <!-- Hero Section -->
  <section id="heroSection" class="mt-32 text-center max-w-xl px-6 space-y-4">
    <h1 class="text-5xl sm:text-6xl font-extrabold leading-tight hero-title">
      Halo Saya <span>Asvira</span> Asisten Virtual FTI
    </h1>
    <p class="text-gray-300 text-base sm:text-lg font-medium">Tanyakan apapun seputar FTI UAP dengan AI Asvira, cepat & responsif!</p>
    <div class="flex flex-wrap justify-center gap-3 mt-4">
      <button onclick="handleFAQ('Apakah FTI UAP sudah terakreditasi?')" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg text-base font-medium">Akreditasi?</button>
      <button onclick="handleFAQ('Berapa biaya kuliah per semester?')" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg text-base font-medium">Biaya Kuliah?</button>
      <button onclick="handleFAQ('Apakah tersedia kelas malam atau kelas karyawan?')" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg text-base font-medium">Kelas Karyawan?</button>
      <button onclick="handleFAQ('Kapan jadwal pendaftaran dibuka?')" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg text-base font-medium">Jadwal Pendaftaran?</button>
    </div>
  </section>

  <!-- Chat Bubble Container -->
  <main id="chatLog" class="w-full max-w-2xl mt-20 px-4 space-y-3 overflow-y-auto" style="max-height: calc(100vh - 160px);"></main>

  <!-- Input -->
  <div id="inputContainer" class="fixed w-full max-w-2xl px-4 bottom-4 transition-all duration-300">
    <div class="relative">
      <input id="inputMessage" type="text" placeholder="Tulis pertanyaan..."
        class="w-full py-3 pl-4 pr-12 rounded-full bg-transparent border border-white/20 text-white placeholder:font-medium placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      <button id="sendBtn"
        class="absolute top-1/2 right-2 -translate-y-1/2 w-10 h-10 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition-all flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-indigo-500">
        ➜
      </button>
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

    function addBubble(message, isUser = true) {
      const bubble = document.createElement('div');
      bubble.className = `max-w-[80%] px-4 py-3 rounded-xl shadow-md ${
        isUser ? 'bg-indigo-600 text-white ml-auto' : 'bg-white text-black mr-auto'
      }`;

      // Replace **text** with <strong>text</strong> for bold formatting
      let formattedMessage = message.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
      bubble.innerHTML = formattedMessage;

      chatLog.appendChild(bubble);
      chatLog.scrollTop = chatLog.scrollHeight;
    }

    function addTypingIndicator(id) {
      const typing = document.createElement('div');
      typing.id = id;
      typing.className = 'max-w-[80%] px-4 py-3 rounded-xl bg-white text-gray-800 mr-auto shadow-md flex items-center';
      typing.innerHTML = `<span class="dot-flashing"><span>.</span><span>.</span><span>.</span></span>`;
      chatLog.appendChild(typing);
      chatLog.scrollTop = chatLog.scrollHeight;
    }

    function removeTypingIndicator(id) {
      const el = document.getElementById(id);
      if (el) el.remove();
    }

    function handleFAQ(question) {
      input.value = question;
      sendBtn.click();
    }

    function adjustInputPosition() {
      const viewport = window.visualViewport || window;
      const currentHeight = viewport.height || window.innerHeight;
      const initialHeight = window.innerHeight;
      const keyboardHeight = initialHeight - currentHeight;

      if (keyboardHeight > 50) {
        inputContainer.style.position = 'fixed';
        inputContainer.style.bottom = `${keyboardHeight + 16}px`;
      } else {
        inputContainer.style.position = 'fixed';
        inputContainer.style.bottom = '16px';
      }

      chatLog.style.maxHeight = `calc(100vh - ${inputContainer.offsetHeight + 160}px)`;
      chatLog.scrollTop = chatLog.scrollHeight;
    }

    sendBtn.addEventListener('click', () => {
      const message = input.value.trim();
      if (!message) return;

      addBubble(message, true);
      input.value = '';
      if (isFirstMessage) {
        heroSection.style.display = 'none';
        isFirstMessage = false;
      }

      const typingId = `typing-${Date.now()}`;
      addTypingIndicator(typingId);

      fetch('/chatbot/send', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message })
      })
        .then(res => res.json())
        .then(data => {
          removeTypingIndicator(typingId);
          addBubble(data.reply, false);
        })
        .catch(() => {
          removeTypingIndicator(typingId);
          addBubble("Maaf, terjadi kesalahan saat mengambil jawaban.", false);
        });
    });

    input.addEventListener('keydown', e => {
      if (e.key === 'Enter') sendBtn.click();
    });

    if (window.visualViewport) {
      window.visualViewport.addEventListener('resize', adjustInputPosition);
    } else {
      window.addEventListener('resize', adjustInputPosition);
    }
    window.addEventListener('orientationchange', adjustInputPosition);
    input.addEventListener('focus', adjustInputPosition);
    input.addEventListener('blur', adjustInputPosition);
    adjustInputPosition();

    tsParticles.load("tsparticles", {
      particles: {
        number: { value: 50 },
        size: { value: 2 },
        color: { value: "#ffffff" },
        opacity: { value: 0.1 },
        move: { enable: true, speed: 0.5 },
        links: { enable: true, distance: 130, color: "#ffffff", opacity: 0.1, width: 1 }
      },
      background: { color: "transparent" }
    });
  </script>
</body>
</html>