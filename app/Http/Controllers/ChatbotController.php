<?php

namespace App\Http\Controllers;

use App\Services\OpenRouterService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
        public function index() {
        return view('chatbot');
    }

    public function send(Request $request, OpenRouterService $openRouterService) {
        $question = $request->input('message');
        $response = $openRouterService->sendMessage($question);

        return response()->json(['reply' => $response]);
    }

}
