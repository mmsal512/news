<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use App\Services\Content\CategorizationService;
use App\Services\Content\AIRankingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    protected $categorizationService;
    protected $rankingService;

    public function __construct(
        CategorizationService $categorizationService,
        AIRankingService $rankingService
    ) {
        $this->categorizationService = $categorizationService;
        $this->rankingService = $rankingService;
    }

    /**
     * Display a listing of articles for admin
     */
    public function index(Request $request)
    {
        $query = Article::with(['category', 'source']);

        // Filters
        if ($request->has('status')) {
            if ($request->status === 'published') {
                $query->published();
            } elseif ($request->status === 'pending') {
                $query->where('is_published', false);
            }
        }

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('language')) {
            $query->where('language', $request->language);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = Category::active()->get();
        
        return view('admin.articles.index', compact('articles', 'categories'));
    }

    /**
     * Show the form for creating a new article
     */
    public function create()
    {
        $categories = Category::active()->get();
        $sources = Source::active()->get();
        
        return view('admin.articles.create', compact('categories', 'sources'));
    }

    /**
     * Store a newly created article
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'summary' => 'nullable|string|max:500',
            'url' => 'nullable|url',
            'image_url' => 'nullable|url',
            'category_id' => 'required|exists:categories,id',
            'source_id' => 'nullable|exists:sources,id',
            'language' => 'required|string|max:10',
            'tags' => 'nullable|array',
            'meta_description' => 'nullable|string|max:160',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'is_breaking' => 'boolean',
            'gallery_images' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);

        // Process gallery images from newline-separated string to array
        if (!empty($validated['gallery_images'])) {
            $validated['gallery_images'] = array_filter(
                array_map('trim', explode("\n", $validated['gallery_images']))
            );
        } else {
            $validated['gallery_images'] = null;
        }

        $article = Article::create($validated);
        
        // Calculate reading time
        $article->calculateReadingTime();

        // Auto-categorize if needed
        if (empty($article->category_id)) {
            $this->categorizationService->categorizeArticle($article);
        }

        // Calculate ranking
        $this->rankingService->updateArticleRanking($article);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article created successfully.');
    }

    /**
     * Display the specified article
     */
    public function show(Article $article)
    {
        $article->load(['category', 'source']);
        
        return view('admin.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the article
     */
    public function edit(Article $article)
    {
        $categories = Category::active()->get();
        $sources = Source::active()->get();
        
        return view('admin.articles.edit', compact('article', 'categories', 'sources'));
    }

    /**
     * Update the specified article
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'summary' => 'nullable|string|max:500',
            'url' => 'nullable|url',
            'image_url' => 'nullable|url',
            'category_id' => 'required|exists:categories,id',
            'source_id' => 'nullable|exists:sources,id',
            'language' => 'required|string|max:10',
            'tags' => 'nullable|array',
            'meta_description' => 'nullable|string|max:160',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'is_breaking' => 'boolean',
            'gallery_images' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);

        // Process gallery images from newline-separated string to array
        if (!empty($validated['gallery_images'])) {
            $validated['gallery_images'] = array_filter(
                array_map('trim', explode("\n", $validated['gallery_images']))
            );
        } else {
            $validated['gallery_images'] = null;
        }

        $article->update($validated);
        
        // Recalculate reading time
        $article->calculateReadingTime();

        // Re-categorize if category changed
        if ($request->has('category_id') && $request->category_id != $article->category_id) {
            $this->categorizationService->categorizeArticle($article);
        }

        // Recalculate ranking
        $this->rankingService->updateArticleRanking($article);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article updated successfully.');
    }

    /**
     * Remove the specified article
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article deleted successfully.');
    }

    /**
     * Publish an article
     */
    public function publish(Article $article)
    {
        $article->update([
            'is_published' => true,
            'published_at' => $article->published_at ?? now()
        ]);

        return back()->with('success', 'Article published successfully.');
    }

    /**
     * Unpublish an article
     */
    public function unpublish(Article $article)
    {
        $article->update(['is_published' => false]);

        return back()->with('success', 'Article unpublished successfully.');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $articleIds = $request->input('article_ids', []);

        if (empty($articleIds)) {
            return back()->with('error', 'No articles selected.');
        }

        switch ($action) {
            case 'publish':
                Article::whereIn('id', $articleIds)->update(['is_published' => true]);
                return back()->with('success', count($articleIds) . ' articles published.');
                
            case 'unpublish':
                Article::whereIn('id', $articleIds)->update(['is_published' => false]);
                return back()->with('success', count($articleIds) . ' articles unpublished.');
                
            case 'delete':
                Article::whereIn('id', $articleIds)->delete();
                return back()->with('success', count($articleIds) . ' articles deleted.');
                
            case 'categorize':
                $articles = Article::whereIn('id', $articleIds)->get();
                $count = 0;
                foreach ($articles as $article) {
                    if ($this->categorizationService->categorizeArticle($article)) {
                        $count++;
                    }
                }
                return back()->with('success', "{$count} articles categorized.");
                
            default:
                return back()->with('error', 'Invalid action.');
        }
    }
}

