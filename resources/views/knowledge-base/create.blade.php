<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Pengetahuan - Asvira FTI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('knowledge-base.index') }}" class="text-white hover:text-gray-200">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <h1 class="text-2xl font-bold">Tambah Data Pengetahuan</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm opacity-90">
                        <i class="fas fa-user mr-1"></i>
                        Admin: {{ auth()->user()->email }}
                    </span>
                    <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="{{ route('knowledge-base.store') }}" method="POST">
                    @csrf
                    
                    <!-- Question Field -->
                    <div class="mb-6">
                        <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                            Pertanyaan <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            id="question" 
                            name="question" 
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('question') border-red-500 @enderror"
                            placeholder="Masukkan pertanyaan yang sering diajukan..."
                            required
                        >{{ old('question') }}</textarea>
                        @error('question')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Answer Field -->
                    <div class="mb-6">
                        <label for="answer" class="block text-sm font-medium text-gray-700 mb-2">
                            Jawaban <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            id="answer" 
                            name="answer" 
                            rows="6"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('answer') border-red-500 @enderror"
                            placeholder="Masukkan jawaban yang lengkap dan informatif..."
                            required
                        >{{ old('answer') }}</textarea>
                        @error('answer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category Field -->
                    <div class="mb-6">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori
                        </label>
                        <select 
                            id="category" 
                            name="category"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category') border-red-500 @enderror"
                        >
                            <option value="">Pilih kategori (opsional)</option>
                            <option value="Biaya" {{ old('category') == 'Biaya' ? 'selected' : '' }}>Biaya</option>
                            <option value="Program" {{ old('category') == 'Program' ? 'selected' : '' }}>Program</option>
                            <option value="Beasiswa" {{ old('category') == 'Beasiswa' ? 'selected' : '' }}>Beasiswa</option>
                            <option value="Fasilitas" {{ old('category') == 'Fasilitas' ? 'selected' : '' }}>Fasilitas</option>
                            <option value="Akademik" {{ old('category') == 'Akademik' ? 'selected' : '' }}>Akademik</option>
                            <option value="Lainnya" {{ old('category') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('knowledge-base.index') }}" 
                           class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html> 