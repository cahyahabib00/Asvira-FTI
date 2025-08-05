<?php

namespace Tests\Asvira;

use Tests\TestCase;
use App\Models\KnowledgeBase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_chatbot_page_loads()
    {
        $response = $this->get('/chatbot');

        $response->assertStatus(200);
        $response->assertSee('Asvira');
        $response->assertSee('Asisten Virtual FTI UAP');
    }

    public function test_chatbot_send_message()
    {
        // Create test knowledge base
        KnowledgeBase::create([
            'question' => 'Apakah FTI UAP sudah terakreditasi?',
            'answer' => 'Ya, FTI UAP sudah terakreditasi BAN-PT dengan peringkat B.',
            'category' => 'Akreditasi'
        ]);

        $response = $this->postJson('/chatbot/send', [
            'message' => 'Apakah FTI UAP sudah terakreditasi?'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['reply']);
        $this->assertStringContainsString('terakreditasi', $response->json('reply'));
    }

    public function test_chatbot_handles_empty_message()
    {
        $response = $this->postJson('/chatbot/send', [
            'message' => ''
        ]);

        $response->assertStatus(422);
    }

    public function test_chatbot_handles_long_message()
    {
        $longMessage = str_repeat('a', 1001); // More than 1000 characters

        $response = $this->postJson('/chatbot/send', [
            'message' => $longMessage
        ]);

        $response->assertStatus(422);
    }

    public function test_chatbot_logs_questions()
    {
        $this->postJson('/chatbot/send', [
            'message' => 'Test question'
        ]);

        $this->assertDatabaseHas('question_logs', [
            'question' => 'Test question'
        ]);
    }

    public function test_chatbot_returns_helpful_response_for_unknown_questions()
    {
        $response = $this->postJson('/chatbot/send', [
            'message' => 'Pertanyaan yang tidak ada dalam database'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['reply']);
        $this->assertStringContainsString('Maaf', $response->json('reply'));
    }
} 