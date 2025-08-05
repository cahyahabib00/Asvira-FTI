<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Kelola Data Pengetahuan - Asvira FTI</title>
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
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4 sm:py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('welcome') }}" class="text-white hover:text-gray-200 p-2 rounded-lg hover:bg-white/10">
                        <i class="fas fa-arrow-left text-lg sm:text-xl"></i>
                    </a>
                    <h1 class="text-lg sm:text-xl md:text-2xl font-bold">Kelola Data Pengetahuan</h1>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <span class="text-xs sm:text-sm opacity-90 hidden sm:block">
                        <i class="fas fa-user mr-1"></i>
                        Admin: {{ auth()->user()->email }}
                    </span>
                    <a href="{{ route('chatbot') }}" class="bg-white/20 hover:bg-white/30 px-2 sm:px-4 py-2 rounded-lg transition-colors text-xs sm:text-sm">
                        <i class="fas fa-robot mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Test Chatbot</span>
                    </a>
                    <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 px-2 sm:px-4 py-2 rounded-lg transition-colors text-xs sm:text-sm">
                            <i class="fas fa-sign-out-alt mr-1 sm:mr-2"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6 sm:py-8">
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Add New Button -->
        <div class="mb-6">
            <a href="{{ route('knowledge-base.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg inline-flex items-center transition-colors text-sm sm:text-base">
                <i class="fas fa-plus mr-2"></i>
                Tambah Data Pengetahuan
            </a>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h2 class="text-base sm:text-lg font-semibold text-gray-800">Daftar Data Pengetahuan</h2>
            </div>
            
            @if($knowledgeBases->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertanyaan</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Jawaban</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Tanggal</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($knowledgeBases as $index => $kb)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-900">
                                        <div class="max-w-xs truncate" title="{{ $kb->question }}">
                                            {{ $kb->question }}
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-900 hidden sm:table-cell">
                                        <div class="max-w-xs truncate" title="{{ $kb->answer }}">
                                            {{ $kb->answer }}
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                        @if($kb->category)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $kb->category }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden md:table-cell">
                                        {{ $kb->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('knowledge-base.edit', $kb) }}" 
                                               class="text-blue-600 hover:text-blue-900 p-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('knowledge-base.destroy', $kb) }}" method="POST" 
                                                  class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 p-1">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-4 sm:px-6 py-8 text-center">
                    <i class="fas fa-database text-3xl sm:text-4xl text-gray-300 mb-4"></i>
                    <p class="text-sm sm:text-base text-gray-500">Belum ada data pengetahuan. Silakan tambahkan data pertama.</p>
                </div>
            @endif
        </div>
    </main>
</body>
</html> 