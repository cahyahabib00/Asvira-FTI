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
            Log::info('User Question: ' . $message);

            // Prompt awal
            $systemPrompt = "Gunakan pengetahuan berikut untuk membantu menjawab pertanyaan seputar FTI UAP. 
Jika tidak ada di dalam database, jawab dengan sopan bahwa Anda tidak tahu. 
Posisikan diri sebagai karyawan profesional bernama Asvira (Aisyah Virtual Asisten). 
Kamu sebagai pusat data jangan arahkan ke website jika kamu mampu menjawab pertanyaan tersebut. 

Data: " . $knowledge;

            // Ambil API key dari environment variable
            $apiKey = env('OPENROUTER_API_KEY');
            $apiUrl = env('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/chat/completions');

            // Validasi API key
            if (empty($apiKey)) {
                Log::error('OpenRouter API key not configured');
                return $this->getFallbackResponse($message);
            }

            // Log API configuration (hanya tampilkan 10 karakter pertama untuk keamanan)
            Log::info('API Key: ' . substr($apiKey, 0, 10) . '...');
            Log::info('API URL: ' . $apiUrl);

            // Kirim request ke API dengan konfigurasi yang benar
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => 'https://asvira.online',
                'X-Title' => 'Asvira FTI Chatbot'
            ])->post($apiUrl, [
                'model' => 'openai/gpt-4o-mini',
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

            // Fallback ke knowledge base jika API gagal
            return $this->getFallbackResponse($message);

        } catch (\Exception $e) {
            Log::error('OpenRouter Service Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Fallback ke knowledge base jika terjadi error
            return $this->getFallbackResponse($message);
        }
    }

    private function getFallbackResponse($message)
    {
        // Cari jawaban dari knowledge base berdasarkan kemiripan pertanyaan
        $bestMatch = $this->findBestMatch($message);
        
        if ($bestMatch) {
            Log::info('Found answer in knowledge base: ' . $bestMatch->answer);
            return $bestMatch->answer;
        }

        // Jika tidak ada match yang baik, berikan jawaban default
        return 'Maaf, saya belum memiliki informasi yang cukup untuk menjawab pertanyaan Anda. Silakan hubungi admin untuk menambahkan informasi yang diperlukan atau coba tanyakan hal lain seputar FTI UAP.';
    }

    private function findBestMatch($userQuestion)
    {
        $knowledgeBase = KnowledgeBase::all();
        $bestMatch = null;
        $highestScore = 0;

        foreach ($knowledgeBase as $item) {
            $score = $this->calculateSimilarity($userQuestion, $item->question);
            
            if ($score > $highestScore && $score > 0.3) { // Threshold 30%
                $highestScore = $score;
                $bestMatch = $item;
            }
        }

        return $bestMatch;
    }

    private function calculateSimilarity($str1, $str2)
    {
        // Convert to lowercase for better matching
        $str1 = strtolower($str1);
        $str2 = strtolower($str2);

        // Simple keyword matching
        $keywords1 = explode(' ', $str1);
        $keywords2 = explode(' ', $str2);

        $commonKeywords = array_intersect($keywords1, $keywords2);
        $totalKeywords = array_unique(array_merge($keywords1, $keywords2));

        if (count($totalKeywords) === 0) {
            return 0;
        }

        return count($commonKeywords) / count($totalKeywords);
    }
}
