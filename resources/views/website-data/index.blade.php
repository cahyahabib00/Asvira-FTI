<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Website Universitas - Asvira FTI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

                <!-- Navigation Links -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin') }}" class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class="fas fa-cog mr-2"></i>Admin Panel
                    </a>
                    <a href="{{ route('welcome') }}" class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-20 pb-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Data Website Universitas Aisyah
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300">
                        Kelola dan monitor data hasil scraping dari website Universitas Aisyah Pringsewu
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="updateData()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>Update Data
                    </button>
                    <button onclick="testData()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-vial mr-2"></i>Test Data
                    </button>
                </div>
            </div>

            <!-- Data Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(!empty($data))
                    @foreach($data as $section => $content)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white capitalize">
                                    <i class="fas fa-file-alt mr-2 text-blue-600"></i>{{ $section }}
                                </h2>
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                    {{ strlen($content) }} karakter
                                </span>
                            </div>
                            <div class="text-gray-700 dark:text-gray-300 text-sm max-h-48 overflow-y-auto whitespace-pre-line leading-relaxed">
                                {{ Str::limit($content, 500) }}
                                @if(strlen($content) > 500)
                                    <span class="text-blue-600 text-xs">... (truncated)</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-2 text-center py-12">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
                            <i class="fas fa-database text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada data</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Klik tombol "Update Data" untuk mengambil data dari website Universitas Aisyah Pringsewu.
                            </p>
                            <button onclick="updateData()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-sync-alt mr-2"></i>Update Data Sekarang
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Status Info -->
            <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                    <div>
                        <h4 class="font-medium text-blue-900 dark:text-blue-100">Informasi</h4>
                        <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                            Data diambil dari website Universitas Aisyah Pringsewu dan di-cache untuk performa optimal. 
                            Update data setiap 1 jam untuk mendapatkan informasi terbaru.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateData() {
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
            button.disabled = true;

            fetch('/website-data/update')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Data berhasil diupdate!');
                        location.reload();
                    } else {
                        alert('❌ Gagal update data: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('❌ Error: ' + error.message);
                })
                .finally(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
        }

        function testData() {
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';
            button.disabled = true;

            fetch('/website-data/test')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Test berhasil!\n\n' +
                              'Pertanyaan: ' + data.test_question + '\n\n' +
                              'Konten relevan:\n' + data.relevant_content);
                    } else {
                        alert('❌ Gagal test data: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('❌ Error: ' + error.message);
                })
                .finally(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
        }
    </script>
</body>
</html>