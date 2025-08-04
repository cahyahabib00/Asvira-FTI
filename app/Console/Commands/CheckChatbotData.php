<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\Log;

class CheckChatbotData extends Command
{
    protected $signature = 'chatbot:check-data';
    protected $description = 'Check and fix chatbot knowledge base data';

    public function handle()
    {
        $this->info('🔍 Checking Chatbot Data...');

        // Check database connection
        try {
            $count = KnowledgeBase::count();
            $this->info("✅ Database connected. Knowledge base count: {$count}");
        } catch (\Exception $e) {
            $this->error("❌ Database error: " . $e->getMessage());
            return 1;
        }

        // Check if data exists
        if ($count === 0) {
            $this->warn("⚠️ No knowledge base data found. Creating sample data...");
            
            $sampleData = [
                [
                    'question' => 'Apa itu FTI UAP?',
                    'answer' => 'FTI UAP adalah Fakultas Teknologi Informasi Universitas Al Azhar Pekanbaru. FTI UAP merupakan salah satu fakultas yang ada di Universitas Al Azhar Pekanbaru yang fokus pada pengembangan teknologi informasi.',
                    'category' => 'Umum'
                ],
                [
                    'question' => 'Program studi apa saja yang ada di FTI UAP?',
                    'answer' => 'FTI UAP memiliki beberapa program studi, termasuk Teknik Informatika, Sistem Informasi, dan program studi lainnya yang berkaitan dengan teknologi informasi.',
                    'category' => 'Program Studi'
                ],
                [
                    'question' => 'Siapa kamu?',
                    'answer' => 'Saya adalah Asvira (Aisyah Virtual Asisten), asisten virtual yang membantu memberikan informasi seputar FTI UAP. Saya siap membantu menjawab pertanyaan Anda tentang fakultas, program studi, dan informasi lainnya terkait FTI UAP.',
                    'category' => 'Umum'
                ],
                [
                    'question' => 'Bagaimana cara mendaftar di FTI UAP?',
                    'answer' => 'Untuk mendaftar di FTI UAP, Anda dapat mengunjungi website resmi universitas atau menghubungi bagian akademik FTI UAP untuk informasi lebih lanjut mengenai persyaratan dan prosedur pendaftaran.',
                    'category' => 'Pendaftaran'
                ],
                [
                    'question' => 'Apa visi dan misi FTI UAP?',
                    'answer' => 'FTI UAP memiliki visi untuk menjadi fakultas teknologi informasi yang unggul dalam pengembangan ilmu pengetahuan dan teknologi, serta misi untuk menghasilkan lulusan yang berkualitas dan siap bersaing di era digital.',
                    'category' => 'Visi Misi'
                ]
            ];

            foreach ($sampleData as $data) {
                KnowledgeBase::create($data);
            }

            $this->info("✅ Sample data created successfully!");
        } else {
            $this->info("✅ Knowledge base data exists. Showing first 3 entries:");
            
            $entries = KnowledgeBase::take(3)->get();
            foreach ($entries as $entry) {
                $this->line("Q: {$entry->question}");
                $this->line("A: {$entry->answer}");
                $this->line("---");
            }
        }

        // Check environment variables
        $apiKey = env('OPENROUTER_API_KEY');
        $apiUrl = env('OPENROUTER_API_URL');

        $this->info("🔑 API Key: " . ($apiKey ? substr($apiKey, 0, 10) . '...' : 'NOT SET'));
        $this->info("🌐 API URL: " . ($apiUrl ?: 'Using default'));

        $this->info("✅ Data check completed!");
        return 0;
    }
} 