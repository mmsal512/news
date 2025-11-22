<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GNewsService
{
    private $apiKey;
    private $baseUrl = 'https://gnews.io/api/v4';
    private $maxResults = 100;

    public function __construct()
    {
        $this->apiKey = config('services.gnews.api_key', env('GNEWS_API_KEY'));
    }

    /**
     * Fetch AI-related news
     */
    public function fetchAiNews($language = 'en', $country = null)
    {
        $params = [
            'q' => 'artificial intelligence OR AI OR machine learning OR deep learning OR ChatGPT OR OpenAI',
            'lang' => $language,
            'max' => $this->maxResults,
            'apikey' => $this->apiKey,
            'sortby' => 'publishedAt'
        ];

        if ($country) {
            $params['country'] = $country;
        }

        return $this->makeRequest('search', $params);
    }

    /**
     * Fetch general tech news
     */
    public function fetchTechNews($language = 'en', $country = null)
    {
        $params = [
            'category' => 'technology',
            'lang' => $language,
            'max' => $this->maxResults,
            'apikey' => $this->apiKey,
            'sortby' => 'publishedAt'
        ];

        if ($country) {
            $params['country'] = $country;
        }

        return $this->makeRequest('top-headlines', $params);
    }

    /**
     * Fetch news by specific query
     */
    public function fetchNewsByQuery($query, $language = 'en', $country = null)
    {
        $params = [
            'q' => $query,
            'lang' => $language,
            'max' => $this->maxResults,
            'apikey' => $this->apiKey,
            'sortby' => 'publishedAt'
        ];

        if ($country) {
            $params['country'] = $country;
        }

        return $this->makeRequest('search', $params);
    }

    /**
     * Fetch top headlines by category
     */
    public function fetchTopHeadlines($category = 'technology', $language = 'en', $country = null)
    {
        $params = [
            'category' => $category,
            'lang' => $language,
            'max' => $this->maxResults,
            'apikey' => $this->apiKey,
            'sortby' => 'publishedAt'
        ];

        if ($country) {
            $params['country'] = $country;
        }

        return $this->makeRequest('top-headlines', $params);
    }

    /**
     * Make HTTP request to GNews API
     */
    private function makeRequest($endpoint, $params)
    {
        try {
            $cacheKey = 'gnews_' . md5($endpoint . serialize($params));
            
            return Cache::remember($cacheKey, 900, function () use ($endpoint, $params) { // 15 minutes cache
                $response = Http::timeout(30)
                    ->retry(3, 1000)
                    ->get("{$this->baseUrl}/{$endpoint}", $params);

                if ($response->failed()) {
                    Log::error('GNews API request failed', [
                        'endpoint' => $endpoint,
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    return null;
                }

                $data = $response->json();
                
                if (!isset($data['articles'])) {
                    Log::warning('GNews API returned unexpected format', ['data' => $data]);
                    return null;
                }

                return $data;
            });
        } catch (\Exception $e) {
            Log::error('GNews API exception', [
                'endpoint' => $endpoint,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Transform GNews article to our format
     */
    public function transformArticle($article, $categoryId = null, $sourceId = null)
    {
        return [
            'title' => $article['title'] ?? '',
            'content' => $article['content'] ?? $article['description'] ?? '',
            'summary' => $article['description'] ?? '',
            'url' => $article['url'] ?? '',
            'image_url' => $article['image'] ?? null,
            'published_at' => isset($article['publishedAt']) ? 
                \Carbon\Carbon::parse($article['publishedAt']) : now(),
            'category_id' => $categoryId,
            'source_id' => $sourceId,
            'author' => $article['source']['name'] ?? 'Unknown',
            'language' => 'en', // Default, can be detected
            'external_id' => md5($article['url'] ?? ''),
            'is_published' => config('news.auto_publish', true), // Auto-publish by default
            'ai_score' => $this->calculateAiScore($article),
            'is_ai_related' => $this->isAiRelated($article),
        ];
    }

    /**
     * Calculate AI relevance score
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
     * Check if article is AI-related
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

    /**
     * Get API usage info
     */
    public function getApiInfo()
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/usage", [
                'apikey' => $this->apiKey
            ]);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('GNews API info request failed', ['message' => $e->getMessage()]);
            return null;
        }
    }
}