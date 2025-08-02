<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\KnowledgeBase;

class OpenRouterService
{
    public function sendMessage($message)
    {
        // Ambil semua data knowledge base
        $knowledge = KnowledgeBase::select('question', 'answer')
            ->get()
            ->map(function ($item) {
                return "Pertanyaan: {$item->question}\nJawaban: {$item->answer}";
            })
            ->implode("\n\n");

        // Prompt awal
        $systemPrompt = "Gunakan pengetahuan berikut untuk membantu menjawab pertanyaan seputar FTI UAP. 
Jika tidak ada di dalam database, jawab dengan sopan bahwa Anda tidak tahu. 
Posisikan diri sebagai karyawan profesional bernama Asvira (Aisyah Virtual Asisten). 
Kamu sebagai pusat data jangan arahkan ke website jika kamu mampu menjawab pertanyaan tersebut. 

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

        // Fallback jika tidak ada data knowledge base
        if (KnowledgeBase::count() === 0) {
            return 'Maaf, saya belum memiliki data yang cukup untuk menjawab pertanyaan Anda. Silakan hubungi admin untuk menambahkan informasi yang diperlukan.';
        }

        // Tampilkan error untuk debugging
        return 'Maaf, tidak ada respons dari AI. Silakan coba lagi nanti.';
    }
}
