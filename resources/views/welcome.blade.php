<!DOCTYPE html>
<html lang="id">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Asvira - Asisten Virtual FTI UAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
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
<body class="bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 min-h-screen">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <h1 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Asvira
                    </h1>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin') }}" 
                       class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                        <i class="fas fa-sign-in-alt text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
                </nav>

    <!-- Hero Section -->
    <section class="pt-20 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                Selamat Datang di
                <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Asvira</span>
            </h1>
            <p class="text-base sm:text-lg md:text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-3xl mx-auto">
                Asisten Virtual FTI UAP yang siap membantu Anda dengan informasi seputar Fakultas Teknologi Informasi
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                <a href="{{ route('chatbot') }}" 
                   class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-semibold text-base sm:text-lg transition-all transform hover:scale-105 shadow-lg">
                    🚀 Mulai Chat dengan Asvira
                </a>
                <a href="#features" 
                   class="w-full sm:w-auto border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-semibold text-base sm:text-lg transition-all">
                    📋 Lihat Fitur
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 max-w-4xl mx-auto">
                <div class="text-center p-4 bg-white/50 dark:bg-gray-800/50 rounded-xl backdrop-blur-sm">
                    <div class="text-2xl sm:text-3xl font-bold text-blue-600 mb-2">24/7</div>
                    <div class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Tersedia</div>
                </div>
                <div class="text-center p-4 bg-white/50 dark:bg-gray-800/50 rounded-xl backdrop-blur-sm">
                    <div class="text-2xl sm:text-3xl font-bold text-purple-600 mb-2">100+</div>
                    <div class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Informasi</div>
                </div>
                <div class="text-center p-4 bg-white/50 dark:bg-gray-800/50 rounded-xl backdrop-blur-sm">
                    <div class="text-2xl sm:text-3xl font-bold text-green-600 mb-2">Real-time</div>
                    <div class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Respons</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-16 px-4 sm:px-6 lg:px-8 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12 sm:mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Fitur Unggulan Asvira
                </h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    Nikmati pengalaman chat yang cerdas dan responsif dengan teknologi AI terkini
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Feature Cards -->
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-800 p-6 sm:p-8 rounded-2xl border border-gray-200 dark:border-gray-600 hover:shadow-xl transition-all">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-600 rounded-xl flex items-center justify-center mb-4 sm:mb-6">
                        <span class="text-xl sm:text-2xl">🤖</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">AI Cerdas</h3>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">
                        Ditenagai oleh teknologi AI terkini untuk memberikan jawaban yang akurat dan relevan
                    </p>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-800 p-6 sm:p-8 rounded-2xl border border-gray-200 dark:border-gray-600 hover:shadow-xl transition-all">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-purple-600 rounded-xl flex items-center justify-center mb-4 sm:mb-6">
                        <span class="text-xl sm:text-2xl">⚡</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">Respons Cepat</h3>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">
                        Mendapatkan jawaban instan dalam hitungan detik untuk semua pertanyaan Anda
                    </p>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-blue-50 dark:from-gray-700 dark:to-gray-800 p-6 sm:p-8 rounded-2xl border border-gray-200 dark:border-gray-600 hover:shadow-xl transition-all">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-green-600 rounded-xl flex items-center justify-center mb-4 sm:mb-6">
                        <span class="text-xl sm:text-2xl">📚</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">Informasi Lengkap</h3>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">
                        Akses informasi lengkap seputar FTI UAP, dari akademik hingga fasilitas
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-blue-600 to-purple-600">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-6">
                Siap Memulai?
            </h2>
            <p class="text-base sm:text-lg md:text-xl text-blue-100 mb-8">
                Mulai percakapan dengan Asvira sekarang dan dapatkan informasi yang Anda butuhkan
            </p>
            <a href="{{ route('chatbot') }}" 
               class="inline-block bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-xl font-semibold text-lg transition-all transform hover:scale-105 shadow-lg">
                🚀 Mulai Chat Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center">
            <p class="text-sm sm:text-base">
                © 2024 Asvira - Asisten Virtual FTI UAP. Dibuat dengan ❤️ untuk kemudahan informasi.
            </p>
        </div>
    </footer>
    </body>
</html>
