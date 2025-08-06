<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use DOMDocument;
use DOMXPath;

class WebScrapingService
{
    private $baseUrl = 'https://www.aisyahuniversity.ac.id/';
    private $cacheTime = 3600; // 1 jam

    public function scrapeUniversityData()
    {
        try {
            $cacheKey = 'aisyah_university_data';
            $cachedData = Cache::get($cacheKey);
            if ($cachedData) {
                Log::info('Using cached university data');
                return $cachedData;
            }
            Log::info('Scraping university data from: ' . $this->baseUrl);
            $data = [
                'homepage' => $this->scrapeHomepage(),
                'about' => $this->scrapeAboutPage(),
                'academic' => $this->scrapeAcademicPage(),
                'faculty' => $this->scrapeFacultyPage(),
                'news' => $this->scrapeNewsPage(),
            ];
            Cache::put($cacheKey, $data, $this->cacheTime);
            Log::info('University data scraped and cached successfully');
            return $data;
        } catch (\Exception $e) {
            Log::error('Web scraping error: ' . $e->getMessage());
            return [];
        }
    }

    private function getHttpHeaders()
    {
        return [
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.9,id;q=0.8',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Connection' => 'keep-alive',
            'Upgrade-Insecure-Requests' => '1',
            'Sec-Fetch-Dest' => 'document',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Site' => 'none',
            'Cache-Control' => 'max-age=0',
        ];
    }

    private function scrapeHomepage()
    {
        try {
            $response = Http::withHeaders($this->getHttpHeaders())
                ->timeout(30)
                ->get($this->baseUrl);
            
            if ($response->successful()) {
                $html = $response->body();
                return $this->extractTextContent($html);
            } else {
                Log::error('Homepage scraping failed with status: ' . $response->status());
                return $this->getFallbackData('homepage');
            }
        } catch (\Exception $e) {
            Log::error('Error scraping homepage: ' . $e->getMessage());
            return $this->getFallbackData('homepage');
        }
    }

    private function scrapeAboutPage()
    {
        try {
            $response = Http::withHeaders($this->getHttpHeaders())
                ->timeout(30)
                ->get($this->baseUrl . 'tentang-kami/');
            
            if ($response->successful()) {
                $html = $response->body();
                return $this->extractTextContent($html);
            } else {
                Log::error('About page scraping failed with status: ' . $response->status());
                return $this->getFallbackData('about');
            }
        } catch (\Exception $e) {
            Log::error('Error scraping about page: ' . $e->getMessage());
            return $this->getFallbackData('about');
        }
    }

    private function scrapeAcademicPage()
    {
        try {
            $response = Http::withHeaders($this->getHttpHeaders())
                ->timeout(30)
                ->get($this->baseUrl . 'akademik/');
            
            if ($response->successful()) {
                $html = $response->body();
                return $this->extractTextContent($html);
            } else {
                Log::error('Academic page scraping failed with status: ' . $response->status());
                return $this->getFallbackData('academic');
            }
        } catch (\Exception $e) {
            Log::error('Error scraping academic page: ' . $e->getMessage());
            return $this->getFallbackData('academic');
        }
    }

    private function scrapeFacultyPage()
    {
        try {
            $response = Http::withHeaders($this->getHttpHeaders())
                ->timeout(30)
                ->get($this->baseUrl . 'fakultas/');
            
            if ($response->successful()) {
                $html = $response->body();
                return $this->extractTextContent($html);
            } else {
                Log::error('Faculty page scraping failed with status: ' . $response->status());
                return $this->getFallbackData('faculty');
            }
        } catch (\Exception $e) {
            Log::error('Error scraping faculty page: ' . $e->getMessage());
            return $this->getFallbackData('faculty');
        }
    }

    private function scrapeNewsPage()
    {
        try {
            $response = Http::withHeaders($this->getHttpHeaders())
                ->timeout(30)
                ->get($this->baseUrl . 'berita/');
            
            if ($response->successful()) {
                $html = $response->body();
                return $this->extractTextContent($html);
            } else {
                Log::error('News page scraping failed with status: ' . $response->status());
                return $this->getFallbackData('news');
            }
        } catch (\Exception $e) {
            Log::error('Error scraping news page: ' . $e->getMessage());
            return $this->getFallbackData('news');
        }
    }

    private function getFallbackData($section)
    {
        $fallbackData = [
            'homepage' => 'Universitas Aisyah Pringsewu adalah perguruan tinggi swasta yang berlokasi di Pringsewu, Lampung. Universitas ini menyediakan berbagai program studi termasuk Fakultas Teknologi Informatika (FTI).',
            'about' => 'Universitas Aisyah Pringsewu didirikan dengan visi menjadi perguruan tinggi unggul yang menghasilkan lulusan berkualitas dan berakhlak mulia. Universitas ini berkomitmen untuk memberikan pendidikan berkualitas dengan dukungan fasilitas modern.',
            'academic' => 'Universitas Aisyah Pringsewu menawarkan berbagai program studi di tingkat sarjana (S1) dan diploma (D3). Program studi mencakup bidang teknologi, kesehatan, ekonomi, dan pendidikan.',
            'faculty' => 'Fakultas Teknologi Informatika (FTI) adalah salah satu fakultas unggulan di Universitas Aisyah Pringsewu. FTI menawarkan program studi Teknik Informatika dan Sistem Informasi dengan kurikulum yang disesuaikan dengan kebutuhan industri.',
            'news' => 'Universitas Aisyah Pringsewu secara aktif mengadakan berbagai kegiatan akademik, seminar, workshop, dan event lainnya untuk mendukung pengembangan mahasiswa dan dosen.'
        ];
        
        return $fallbackData[$section] ?? 'Data tidak tersedia untuk section ini.';
    }

    private function extractTextContent($html)
    {
        try {
            $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
            $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $html);
            $dom = new DOMDocument();
            @$dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
            $xpath = new DOMXPath($dom);
            $textElements = [
                '//h1', '//h2', '//h3', '//h4', '//h5', '//h6',
                '//p', '//div[@class="content"]', '//div[@class="description"]',
                '//article', '//section', '//main'
            ];
            $content = '';
            foreach ($textElements as $xpathQuery) {
                $elements = $xpath->query($xpathQuery);
                foreach ($elements as $element) {
                    $text = trim($element->textContent);
                    if (!empty($text) && strlen($text) > 10) {
                        $content .= $text . "\n\n";
                    }
                }
            }
            $content = preg_replace('/\s+/', ' ', $content);
            $content = trim($content);
            return substr($content, 0, 5000);
        } catch (\Exception $e) {
            Log::error('Error extracting text content: ' . $e->getMessage());
            return '';
        }
    }

    public function getRelevantContent($userQuestion)
    {
        $data = $this->scrapeUniversityData();
        if (empty($data)) {
            return '';
        }
        $allContent = '';
        foreach ($data as $section => $content) {
            if (!empty($content)) {
                $allContent .= "=== {$section} ===\n{$content}\n\n";
            }
        }
        $relevantContent = $this->findRelevantContent($allContent, $userQuestion);
        return $relevantContent;
    }

    private function findRelevantContent($content, $userQuestion)
    {
        $keywords = [
            'fti', 'fakultas teknologi', 'informatika', 'teknik informatika',
            'sistem informasi', 'manajemen informatika', 'teknologi informasi',
            'program studi', 'jurusan', 'akademik', 'pendidikan'
        ];
        $userQuestionLower = strtolower($userQuestion);
        $relevantSections = [];
        $sections = explode('===', $content);
        foreach ($sections as $section) {
            $sectionLower = strtolower($section);
            foreach ($keywords as $keyword) {
                if (strpos($sectionLower, $keyword) !== false) {
                    $relevantSections[] = trim($section);
                    break;
                }
            }
        }
        if (empty($relevantSections) && !empty($sections)) {
            $relevantSections[] = trim($sections[0]);
        }
        return implode("\n\n", $relevantSections);
    }
}