<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Services\Search\SearchService;
use App\Services\Content\AIRankingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    protected $searchService;
    protected $rankingService;

    public function __construct(SearchService $searchService, AIRankingService $rankingService)
    {
        $this->searchService = $searchService;
        $this->rankingService = $rankingService;
    }

    /**
     * Display listing of articles
     */
    public function index(Request $request)
    {
        try {
            $categoryParam = $request->get('category');
            // Clean up category parameter - remove empty strings
            if ($categoryParam === '' || $categoryParam === null) {
                $categoryParam = null;
            }

            $articles = $this->searchService->search([
                'q' => $request->get('q'),
                'category' => $categoryParam,
                'language' => $request->get('language'),
                'ai_related' => $request->get('ai_related'),
                'sort_by' => $request->get('sort', 'published_at'),
                'per_page' => 15
            ]);

            $categories = Cache::remember('active_categories', 3600, function () {
                return Category::active()->ordered()->get();
            });

            return view('articles.index', compact('articles', 'categories'));
        } catch (\Exception $e) {
            \Log::error('Error in ArticleController@index: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return empty results on error
            $categories = Cache::remember('active_categories', 3600, function () {
                return Category::active()->ordered()->get();
            });
            $articles = Article::published()->paginate(15);
            
            return view('articles.index', compact('articles', 'categories'))
                ->with('error', 'An error occurred while loading articles. Please try again.');
        }
    }

    /**
     * Display a single article
     */
    public function show($id)
    {
        $article = Article::with(['category', 'source'])
            ->published()
            ->findOrFail($id);

        // Get related articles
        $relatedArticles = Cache::remember("related_articles_{$id}", 3600, function () use ($article) {
            return Article::published()
                ->where('id', '!=', $article->id)
                ->where(function ($query) use ($article) {
                    $query->where('category_id', $article->category_id)
                          ->orWhereJsonContains('tags', $article->tags ?? []);
                })
                ->limit(4)
                ->get();
        });

        return view('articles.show', compact('article', 'relatedArticles'));
    }

    /**
     * Search API endpoint
     */
    public function search(Request $request)
    {
        $results = $this->searchService->search($request->all());
        
        return response()->json($results);
    }

    /**
     * Get search suggestions
     */
    public function suggestions(Request $request)
    {
        $term = $request->get('q', '');
        
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $suggestions = $this->searchService->getSuggestions($term);
        
        return response()->json($suggestions);
    }
}

