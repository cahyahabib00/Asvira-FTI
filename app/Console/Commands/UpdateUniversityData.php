<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WebScrapingService;
use Illuminate\Support\Facades\Log;

class UpdateUniversityData extends Command
{
    protected $signature = 'university:update-data';
    protected $description = 'Update university data from website';

    public function handle(WebScrapingService $webScrapingService)
    {
        $this->info('🔄 Updating university data from website...');
        try {
            $data = $webScrapingService->scrapeUniversityData();
            if (!empty($data)) {
                $this->info('✅ University data updated successfully!');
                $this->info('📦 Data sections: ' . count($data));
                foreach ($data as $section => $content) {
                    $this->info("   • {$section}: " . strlen($content) . " characters");
                }
            } else {
                $this->warn('⚠️ No data retrieved from website');
            }
        } catch (\Exception $e) {
            $this->error('❌ Error updating university data: ' . $e->getMessage());
            Log::error('University data update error: ' . $e->getMessage());
        }
    }
}