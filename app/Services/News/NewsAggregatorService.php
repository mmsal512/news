<?php

namespace App\Services\News;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use App\Services\Content\CategorizationService;
use App\Services\Content\AIRankingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewsAggregatorService
{
    private $gNewsService;
    private $newsApiService;
    private $categorizationService;
    private $rankingService;

    public function __construct(
        GNewsService $gNewsService, 
        NewsAPIService $newsApiService,
        CategorizationService $categorizationService,
        AIRankingService $rankingService
    ) {
        $this->gNewsService = $gNewsService;
        $this->newsApiService = $newsApiService;
        $this->categorizationService = $categorizationService;
        $this->rankingService = $rankingService;
    }

    /**
     * Fetch and store all types of news
     */
    public function fetchAllNews()
    {
        $results = [
            'ai_news' => 0,
            'tech_news' => 0,
            'general_news' => 0,
            'errors' => []
        ];

        // Create default categories if they don't exist
        $this->ensureDefaultCategories();

        // Fetch AI News
        try {
            $results['ai_news'] = $this->fetchAndStoreAiNews();
        } catch (\Exception $e) {
            $results['errors'][] = 'AI News: ' . $e->getMessage();
            Log::error('Failed to fetch AI news', ['error' => $e->getMessage()]);
        }

        // Fetch Tech News
        try {
            $results['tech_news'] = $this->fetchAndStoreTechNews();
        } catch (\Exception $e) {
            $results['errors'][] = 'Tech News: ' . $e->getMessage();
            Log::error('Failed to fetch tech news', ['error' => $e->getMessage()]);
        }

        // Fetch General News
        try {
            $results['general_news'] = $this->fetchAndStoreGeneralNews();
        } catch (\Exception $e) {
            $results['errors'][] = 'General News: ' . $e->getMessage();
            Log::error('Failed to fetch general news', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Fetch and store AI-related news
     */
    public function fetchAndStoreAiNews()
    {
        $aiCategory = Category::where('slug', 'ai-news')->first();
        $count = 0;

        // Fetch from multiple languages
        $languages = ['en', 'ar'];
        
        foreach ($languages as $language) {
            $newsData = $this->gNewsService->fetchAiNews($language);
            
            if ($newsData && isset($newsData['articles'])) {
                $count += $this->storeArticles($newsData['articles'], $aiCategory->id, $language);
            }
        }

        return $count;
    }

    /**
     * Fetch and store tech news
     */
    public function fetchAndStoreTechNews()
    {
        $techCategory = Category::where('slug', 'technology')->first();
        $count = 0;

        $languages = ['en', 'ar'];
        
        foreach ($languages as $language) {
            $newsData = $this->gNewsService->fetchTechNews($language);
            
            if ($newsData && isset($newsData['articles'])) {
                $count += $this->storeArticles($newsData['articles'], $techCategory->id, $language);
            }
        }

        return $count;
    }

    /**
     * Fetch and store general news
     */
    public function fetchAndStoreGeneralNews()
    {
        $generalCategory = Category::where('slug', 'general')->first();
        $count = 0;

        $categories = ['science', 'business', 'health'];
        $languages = ['en', 'ar'];

        foreach ($categories as $category) {
            foreach ($languages as $language) {
                $newsData = $this->gNewsService->fetchTopHeadlines($category, $language);
                
                if ($newsData && isset($newsData['articles'])) {
                    $count += $this->storeArticles($newsData['articles'], $generalCategory->id, $language);
                }
            }
        }

        return $count;
    }

    /**
     * Store articles in database
     */
    private function storeArticles($articles, $categoryId, $language = 'en')
    {
        $count = 0;

        foreach ($articles as $articleData) {
            try {
                // Check if article already exists
                $existingArticle = Article::where('external_id', md5($articleData['url'] ?? ''))
                    ->orWhere('url', $articleData['url'] ?? '')
                    ->first();

                if ($existingArticle) {
                    continue; // Skip duplicate
                }

                // Get or create source
                $source = $this->getOrCreateSource($articleData);

                // Transform and create article
                $transformedArticle = $this->gNewsService->transformArticle(
                    $articleData, 
                    $categoryId, 
                    $source->id
                );
                
                $transformedArticle['language'] = $language;

                $article = Article::create($transformedArticle);
                
                // Auto-categorize if category is missing
                if (empty($article->category_id)) {
                    $this->categorizationService->categorizeArticle($article);
                }
                
                // Calculate ranking score
                $this->rankingService->updateArticleRanking($article);
                
                $count++;

                // Update source stats
                $source->updateFetchStats();

            } catch (\Exception $e) {
                Log::error('Failed to store article', [
                    'article' => $articleData,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $count;
    }

    /**
     * Get or create news source
     */
    private function getOrCreateSource($articleData)
    {
        $sourceName = $articleData['source']['name'] ?? 'Unknown Source';
        $sourceUrl = $articleData['source']['url'] ?? $articleData['url'] ?? '';

        return Source::firstOrCreate(
            ['name' => $sourceName],
            [
                'url' => $sourceUrl,
                'description' => "News source: {$sourceName}",
                'language' => 'en',
                'is_active' => true,
                'reliability_score' => 0.5,
                'fetch_count' => 0
            ]
        );
    }

    /**
     * Ensure default categories exist
     */
    private function ensureDefaultCategories()
    {
        $categories = [
            [
                'name' => 'AI News',
                'slug' => 'ai-news',
                'description' => 'Artificial Intelligence and Machine Learning news',
                'color' => '#3B82F6',
                'icon' => 'fas fa-robot',
                'sort_order' => 1
            ],
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'General technology and innovation news',
                'color' => '#10B981',
                'icon' => 'fas fa-microchip',
                'sort_order' => 2
            ],
            [
                'name' => 'General',
                'slug' => 'general',
                'description' => 'General news and current affairs',
                'color' => '#6B7280',
                'icon' => 'fas fa-newspaper',
                'sort_order' => 3
            ],
            [
                'name' => 'Tutorials',
                'slug' => 'tutorials',
                'description' => 'Learning resources and tutorials',
                'color' => '#8B5CF6',
                'icon' => 'fas fa-graduation-cap',
                'sort_order' => 4
            ]
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                array_merge($categoryData, ['is_active' => true])
            );
        }
    }

    /**
     * Clean up old articles
     */
    public function cleanupOldArticles($daysToKeep = 30)
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        $count = Article::where('published_at', '<', $cutoffDate)
            ->where('is_published', false)
            ->delete();

        Log::info("Cleaned up {$count} old articles older than {$daysToKeep} days");
        
        return $count;
    }

    /**
     * Get news statistics
     */
    public function getNewsStatistics()
    {
        return [
            'total_articles' => Article::count(),
            'published_articles' => Article::published()->count(),
            'ai_articles' => Article::aiRelated()->count(),
            'recent_articles' => Article::recent()->count(),
            'articles_by_language' => Article::select('language', DB::raw('count(*) as count'))
                ->groupBy('language')
                ->pluck('count', 'language')
                ->toArray(),
            'articles_by_category' => Article::with('category')
                ->select('category_id', DB::raw('count(*) as count'))
                ->groupBy('category_id')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->category->name ?? 'Unknown' => $item->count];
                })
                ->toArray(),
        ];
    }
}