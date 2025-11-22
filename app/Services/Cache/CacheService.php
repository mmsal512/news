<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use App\Models\Article;
use App\Models\Category;

class CacheService
{
    /**
     * Clear all article-related caches
     */
    public function clearArticleCaches(?int $articleId = null): void
    {
        if ($articleId) {
            Cache::forget("article_{$articleId}");
            Cache::forget("related_articles_{$articleId}");
        }
        
        Cache::forget('active_categories');
        Cache::forget('popular_search_terms');
        Cache::tags(['articles'])->flush();
    }

    /**
     * Warm up important caches
     */
    public function warmUpCaches(): void
    {
        // Cache active categories
        Cache::remember('active_categories', 3600, function () {
            return Category::active()->ordered()->get();
        });

        // Cache popular articles
        Cache::remember('popular_articles', 1800, function () {
            return Article::published()
                ->orderBy('views', 'desc')
                ->limit(10)
                ->get();
        });

        // Cache recent articles
        Cache::remember('recent_articles', 900, function () {
            return Article::published()
                ->orderBy('published_at', 'desc')
                ->limit(10)
                ->get();
        });
    }

    /**
     * Get cached data or remember
     */
    public function remember(string $key, int $ttl, callable $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Clear cache by pattern
     */
    public function clearByPattern(string $pattern): void
    {
        // This would require Redis or a custom cache driver
        // For now, we'll use tags if available
        if (method_exists(Cache::getStore(), 'tags')) {
            Cache::tags([$pattern])->flush();
        }
    }
}

