<?php

namespace App\Services\Search;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class SearchService
{
    /**
     * Advanced search for articles
     */
    public function search(array $params = [])
    {
        $query = Article::query();

        // Search by keyword
        if (!empty($params['q'])) {
            $query->where(function ($q) use ($params) {
                $searchTerm = $params['q'];
                // Use COLLATE for case-insensitive and accent-insensitive search
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%")
                  ->orWhere('summary', 'like', "%{$searchTerm}%")
                  ->orWhere('meta_description', 'like', "%{$searchTerm}%")
                  ->orWhereHas('category', function ($catQuery) use ($searchTerm) {
                      $catQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Filter by category
        if (!empty($params['category']) && $params['category'] !== '') {
            $categoryParam = trim($params['category']);
            $query->whereHas('category', function ($q) use ($categoryParam) {
                if (is_numeric($categoryParam)) {
                    $q->where('id', $categoryParam);
                } else {
                    $q->where('slug', $categoryParam);
                }
            });
        }

        // Filter by language
        if (!empty($params['language'])) {
            $query->where('language', $params['language']);
        }

        // Filter by AI-related
        if (isset($params['ai_related'])) {
            $query->where('is_ai_related', (bool)$params['ai_related']);
        }

        // Filter by published status
        if (!isset($params['include_unpublished']) || !$params['include_unpublished']) {
            $query->published();
        }

        // Filter by date range
        if (!empty($params['from'])) {
            $query->where('published_at', '>=', $params['from']);
        }
        if (!empty($params['to'])) {
            $query->where('published_at', '<=', $params['to']);
        }

        // Filter by tags
        if (!empty($params['tags'])) {
            $tags = is_array($params['tags']) ? $params['tags'] : [$params['tags']];
            $query->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        // Sorting
        $sortBy = $params['sort_by'] ?? 'published_at';
        $sortOrder = $params['sort_order'] ?? 'desc';

        switch ($sortBy) {
            case 'relevance':
                $query->orderBy('ai_score', 'desc')
                      ->orderBy('views', 'desc');
                break;
            case 'views':
                $query->orderBy('views', $sortOrder);
                break;
            case 'score':
                $query->orderBy('ai_score', $sortOrder);
                break;
            default:
                $query->orderBy('published_at', $sortOrder);
        }

        // Pagination
        $perPage = $params['per_page'] ?? 15;
        
        return $query->with(['category', 'source'])->paginate($perPage);
    }

    /**
     * Get search suggestions
     */
    public function getSuggestions(string $term, int $limit = 5)
    {
        $cacheKey = 'search_suggestions_' . md5($term);
        
        return Cache::remember($cacheKey, 3600, function () use ($term, $limit) {
            return Article::published()
                ->where('title', 'like', "%{$term}%")
                ->orWhere('summary', 'like', "%{$term}%")
                ->select('title', 'id', 'slug')
                ->limit($limit)
                ->get()
                ->map(function ($article) {
                    return [
                        'title' => $article->title,
                        'url' => route('articles.show', $article->id)
                    ];
                });
        });
    }

    /**
     * Get popular search terms
     */
    public function getPopularTerms(int $limit = 10)
    {
        // This could be enhanced with a search_logs table
        return Cache::remember('popular_search_terms', 3600, function () use ($limit) {
            // For now, return popular tags
            return Article::published()
                ->whereNotNull('tags')
                ->get()
                ->pluck('tags')
                ->flatten()
                ->countBy()
                ->sortDesc()
                ->take($limit)
                ->keys()
                ->toArray();
        });
    }
}

