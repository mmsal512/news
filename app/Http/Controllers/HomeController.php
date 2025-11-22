<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Display the premium homepage
     */
    public function index()
    {
        // Featured articles for hero section
        $featuredArticles = Cache::remember('featured_articles', 3600, function () {
            return Article::published()
                ->featured()
                ->with(['category', 'source'])
                ->orderBy('published_at', 'desc')
                ->limit(3)
                ->get();
        });

        // If no featured articles, get latest
        if ($featuredArticles->isEmpty()) {
            $featuredArticles = Article::published()
                ->with(['category', 'source'])
                ->orderBy('published_at', 'desc')
                ->limit(3)
                ->get();
        }

        // Breaking news
        $breakingNews = Article::published()
            ->breaking()
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        // Latest articles by category
        $categories = Category::active()->ordered()->get();
        $articlesByCategory = [];
        
        foreach ($categories as $category) {
            $articlesByCategory[$category->slug] = Article::published()
                ->where('category_id', $category->id)
                ->with(['category', 'source'])
                ->orderBy('published_at', 'desc')
                ->limit(6)
                ->get();
        }

        // Popular articles
        $popularArticles = Article::published()
            ->with(['category', 'source'])
            ->orderBy('views', 'desc')
            ->limit(6)
            ->get();

        return view('home', compact(
            'featuredArticles',
            'breakingNews',
            'articlesByCategory',
            'categories',
            'popularArticles'
        ));
    }
}
