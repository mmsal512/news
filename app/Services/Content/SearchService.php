<?php

namespace App\Services\Content;

use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SearchService
{
    /**
     * Advanced search for articles
     */
    public function search($query, $filters = [])
    {
        $searchQuery = Article::query();

        // Full-text search on title, content, and summary
        if (!empty($query)) {
            $searchQuery->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%")
                  ->orWhere('summary', 'LIKE', "%{$query}%")
                  ->orWhere('tags', 'LIKE', "%{$query}%");
            });
        }

        // Apply filters
        if (isset($filters['category_id']) && $filters['category_id']) {
            $searchQuery->where('category_id', $filters['category_id']);
        }

        if (isset($filters['language']) && $filters['language']) {
            $searchQuery->where('language', $filters['language']);
        }

        if (isset($filters['is_ai_related']) && $filters['is_ai_related'] !== null) {
            $searchQuery->where('is_ai_related', $filters['is_ai_related']);
        }

        if (isset($filters['source_id']) && $filters['source_id']) {
            $searchQuery->where('source_id', $filters['source_id']);
        }

        if (isset($filters['date_from']) && $filters['date_from']) {
            $searchQuery->where('published_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to']) {
            $searchQuery->where('published_at', '<=', $filters['date_to']);
        }

        if (isset($filters['min_views']) && $filters['min_views']) {
            $searchQuery->where('views', '>=', $filters['min_views']);
        }

        // Only show published articles for public search
        if (!isset($filters['include_unpublished']) || !$filters['include_unpublished']) {
            $searchQuery->where('is_published', true);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'published_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        // Relevance scoring for search queries
        if (!empty($query)) {
            $searchQuery->selectRaw('articles.*, 
                (
                    CASE 
                        WHEN title LIKE ? THEN 10
                        WHEN title LIKE ? THEN 5
                        ELSE 0
                    END +
                    CASE 
                        WHEN summary LIKE ? THEN 5
                        WHEN summary LIKE ? THEN 2
                        ELSE 0
                    END +
                    CASE 
                        WHEN content LIKE ? THEN 2
                        WHEN content LIKE ? THEN 1
                        ELSE 0
                    END +
                    (views * 0.001) +
                    (ai_score * 2)
                ) as relevance_score',
                [
                    "%{$query}%", "%" . Str::substr($query, 0, 10) . "%",
                    "%{$query}%", "%" . Str::substr($query, 0, 10) . "%",
                    "%{$query}%", "%" . Str::substr($query, 0, 10) . "%"
                ]
            )->orderBy('relevance_score', 'desc');
        } else {
            $searchQuery->orderBy($sortBy, $sortOrder);
        }

        return $searchQuery;
    }

    /**
     * Get search suggestions based on query
     */
    public function getSuggestions($query, $limit = 5)
    {
        if (strlen($query) < 2) {
            return [];
        }

        $suggestions = Article::where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('tags', 'LIKE', "%{$query}%");
            })
            ->select('title', 'id')
            ->limit($limit)
            ->get()
            ->pluck('title')
            ->toArray();

        return $suggestions;
    }

    /**
     * Get trending searches
     */
    public function getTrendingSearches($limit = 10)
    {
        // This would typically come from a search_logs table
        // For now, return popular tags
        return Article::where('is_published', true)
            ->whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->countBy()
            ->sortDesc()
            ->take($limit)
            ->keys()
            ->toArray();
    }
}

