<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\KnowledgeBase;

class OpenRouterService
{
    public function sendMessage($message)
    {
        // Ambil semua data knowledge base
        $knowledge = KnowledgeBase::pluck('content')->implode("\n");

        // Prompt awal
        $systemPrompt = "Gunakan pengetahuan berikut untuk membantu menjawab pertanyaan pendaftaran UAP. 
Jika tidak ada di dalam database, jawab dengan sopan bahwa Anda tidak tahu. 
Posisikan diri sebagai karyawan profesional bernama Asvira (Aisyah Virtual Asisten). 
Data: " . $knowledge;

        // Kirim request ke AKBXR
        $response = Http::withHeaders([
            'Authorization' => 'Bearer UNLIMITED-BETA', // Ganti dengan AK-FREE-LampungDev kalau perlu
            'Content-Type' => 'application/json',
        ])->post('https://api.akbxr.com/v1/chat/completions', [
            'model' => 'auto',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $message]
            ],
            'stream' => false
        ]);

        // Cek isi response dan kembalikan content-nya
        $data = $response->json();

        if (isset($data['choices'][0]['message']['content'])) {
            return $data['choices'][0]['message']['content'];
        }

        // Tampilkan error untuk debugging
        return 'Maaf, tidak ada respons dari AI. Debug: ' . json_encode($data);
    }
}
