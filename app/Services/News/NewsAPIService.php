<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class NewsAPIService
{
    private $apiKey;
    private $baseUrl = 'https://newsapi.org/v2';
    private $pageSize = 100;

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.api_key', env('NEWSAPI_API_KEY'));
    }

    /**
     * Fetch everything about AI
     */
    public function fetchAiNews($language = 'en')
    {
        $params = [
            'q' => 'artificial intelligence OR "machine learning" OR "deep learning" OR AI',
            'language' => $language,
            'pageSize' => $this->pageSize,
            'sortBy' => 'publishedAt',
            'apiKey' => $this->apiKey
        ];

        return $this->makeRequest('everything', $params);
    }

    /**
     * Fetch technology news
     */
    public function fetchTechNews($language = 'en', $country = null)
    {
        $params = [
            'category' => 'technology',
            'language' => $language,
            'pageSize' => $this->pageSize,
            'apiKey' => $this->apiKey
        ];

        if ($country) {
            $params['country'] = $country;
        }

        return $this->makeRequest('top-headlines', $params);
    }

    /**
     * Fetch news from specific sources
     */
    public function fetchFromSources($sources, $language = 'en')
    {
        $params = [
            'sources' => is_array($sources) ? implode(',', $sources) : $sources,
            'pageSize' => $this->pageSize,
            'language' => $language,
            'apiKey' => $this->apiKey
        ];

        return $this->makeRequest('everything', $params);
    }

    /**
     * Search news by query
     */
    public function searchNews($query, $language = 'en', $from = null, $to = null)
    {
        $params = [
            'q' => $query,
            'language' => $language,
            'pageSize' => $this->pageSize,
            'sortBy' => 'publishedAt',
            'apiKey' => $this->apiKey
        ];

        if ($from) {
            $params['from'] = $from;
        }
        if ($to) {
            $params['to'] = $to;
        }

        return $this->makeRequest('everything', $params);
    }

    /**
     * Get available sources
     */
    public function getSources($category = null, $language = 'en', $country = null)
    {
        $params = ['apiKey' => $this->apiKey];

        if ($category) $params['category'] = $category;
        if ($language) $params['language'] = $language;
        if ($country) $params['country'] = $country;

        return $this->makeRequest('sources', $params);
    }

    /**
     * Make HTTP request to NewsAPI
     */
    private function makeRequest($endpoint, $params)
    {
        try {
            $cacheKey = 'newsapi_' . md5($endpoint . serialize($params));
            
            return Cache::remember($cacheKey, 900, function () use ($endpoint, $params) { // 15 minutes cache
                $response = Http::timeout(30)
                    ->retry(3, 1000)
                    ->get("{$this->baseUrl}/{$endpoint}", $params);

                if ($response->failed()) {
                    Log::error('NewsAPI request failed', [
                        'endpoint' => $endpoint,
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    return null;
                }

                $data = $response->json();
                
                if ($data['status'] !== 'ok') {
                    Log::warning('NewsAPI returned error', ['data' => $data]);
                    return null;
                }

                return $data;
            });
        } catch (\Exception $e) {
            Log::error('NewsAPI exception', [
                'endpoint' => $endpoint,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Transform NewsAPI article to our format
     */
    public function transformArticle($article, $categoryId = null, $sourceId = null)
    {
        return [
            'title' => $article['title'] ?? '',
            'content' => $article['content'] ?? $article['description'] ?? '',
            'summary' => $article['description'] ?? '',
            'url' => $article['url'] ?? '',
            'image_url' => $article['urlToImage'] ?? null,
            'published_at' => isset($article['publishedAt']) ? 
                \Carbon\Carbon::parse($article['publishedAt']) : now(),
            'category_id' => $categoryId,
            'source_id' => $sourceId,
            'author' => $article['author'] ?? ($article['source']['name'] ?? 'Unknown'),
            'language' => 'en', // Default, can be detected
            'external_id' => md5($article['url'] ?? ''),
            'is_published' => false, // Will be reviewed by admin
            'ai_score' => $this->calculateAiScore($article),
            'is_ai_related' => $this->isAiRelated($article),
        ];
    }

    /**
     * Calculate AI relevance score (same logic as GNewsService)
     */
    private function calculateAiScore($article)
    {
        $aiKeywords = [
            'artificial intelligence' => 1.0,
            'machine learning' => 0.9,
            'deep learning' => 0.9,
            'neural network' => 0.8,
            'chatgpt' => 0.9,
            'openai' => 0.8,
            'ai' => 0.7,
            'automation' => 0.6,
            'algorithm' => 0.5,
            'data science' => 0.5
        ];

        $content = strtolower(($article['title'] ?? '') . ' ' . ($article['description'] ?? ''));
        $score = 0;
        $maxScore = 0;

        foreach ($aiKeywords as $keyword => $weight) {
            if (strpos($content, $keyword) !== false) {
                $score += $weight;
            }
            $maxScore += $weight;
        }

        return $maxScore > 0 ? min(1.0, $score / $maxScore * 2) : 0;
    }

    /**
     * Check if article is AI-related (same logic as GNewsService)
     */
    private function isAiRelated($article)
    {
        $aiKeywords = ['artificial intelligence', 'machine learning', 'ai ', 'chatgpt', 'openai', 'neural network'];
        $content = strtolower(($article['title'] ?? '') . ' ' . ($article['description'] ?? ''));
        
        foreach ($aiKeywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
}