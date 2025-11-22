<?php

namespace App\View\Components;

use App\Models\Article;
use Illuminate\View\Component;

class BreakingNewsBanner extends Component
{
    public $breakingArticles;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->breakingArticles = Article::published()
            ->breaking()
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.breaking-news-banner');
    }
}

