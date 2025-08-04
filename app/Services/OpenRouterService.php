<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\KnowledgeBase;

class OpenRouterService
{
    public function sendMessage($message)
    {
        try {
            // Ambil semua data knowledge base
            $knowledge = KnowledgeBase::select('question', 'answer')
                ->get()
                ->map(function ($item) {
                    return "Pertanyaan: {$item->question}\nJawaban: {$item->answer}";
                })
                ->implode("\n\n");

            // Log untuk debugging
            Log::info('Knowledge Base Count: ' . KnowledgeBase::count());
            Log::info('Knowledge Data: ' . $knowledge);

            // Prompt awal
            $systemPrompt = "Gunakan pengetahuan berikut untuk membantu menjawab pertanyaan seputar FTI UAP. 
Jika tidak ada di dalam database, jawab dengan sopan bahwa Anda tidak tahu. 
Posisikan diri sebagai karyawan profesional bernama Asvira (Aisyah Virtual Asisten). 
Kamu sebagai pusat data jangan arahkan ke website jika kamu mampu menjawab pertanyaan tersebut. 

Data: " . $knowledge;

            // Ambil API key dari environment variable
            $apiKey = env('OPENROUTER_API_KEY', 'UNLIMITED-BETA');
            $apiUrl = env('OPENROUTER_API_URL', 'https://api.akbxr.com/v1/chat/completions');

            // Log API configuration
            Log::info('API Key: ' . substr($apiKey, 0, 10) . '...');
            Log::info('API URL: ' . $apiUrl);

            // Kirim request ke API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'model' => 'auto',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $message]
                ],
                'stream' => false,
                'max_tokens' => 1000,
                'temperature' => 0.7
            ]);

            // Log response status
            Log::info('API Response Status: ' . $response->status());
            Log::info('API Response Body: ' . $response->body());

            // Cek isi response dan kembalikan content-nya
            $data = $response->json();

            if (isset($data['choices'][0]['message']['content'])) {
                $reply = $data['choices'][0]['message']['content'];
                Log::info('AI Reply: ' . $reply);
                return $reply;
            }

            // Log error jika tidak ada content
            Log::error('No content in API response', $data);

            // Fallback jika tidak ada data knowledge base
            if (KnowledgeBase::count() === 0) {
                return 'Maaf, saya belum memiliki data yang cukup untuk menjawab pertanyaan Anda. Silakan hubungi admin untuk menambahkan informasi yang diperlukan.';
            }

            // Tampilkan error untuk debugging
            return 'Maaf, tidak ada respons dari AI. Silakan coba lagi nanti. Error: ' . json_encode($data);

        } catch (\Exception $e) {
            Log::error('OpenRouter Service Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return 'Maaf, terjadi kesalahan sistem. Silakan coba lagi nanti. Error: ' . $e->getMessage();
        }
    }
}
