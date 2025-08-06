<?php

namespace App\Http\Controllers;

use App\Services\WebScrapingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WebsiteDataController extends Controller
{
    private $webScrapingService;

    public function __construct(WebScrapingService $webScrapingService)
    {
        $this->webScrapingService = $webScrapingService;
    }

    public function index()
    {
        $data = Cache::get('aisyah_university_data', []);
        return view('website-data.index', compact('data'));
    }

    public function update()
    {
        try {
            $data = $this->webScrapingService->scrapeUniversityData();
            return response()->json([
                'success' => true,
                'message' => 'Data website berhasil diupdate',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function test()
    {
        try {
            $testQuestion = "Apa itu FTI UAP?";
            $relevantContent = $this->webScrapingService->getRelevantContent($testQuestion);
            return response()->json([
                'success' => true,
                'test_question' => $testQuestion,
                'relevant_content' => $relevantContent
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}