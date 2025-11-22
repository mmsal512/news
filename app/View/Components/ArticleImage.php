<?php

namespace App\View\Components;

use App\Helpers\ImageHelper;
use App\Models\Article;
use Illuminate\View\Component;

class ArticleImage extends Component
{
    public $article;
    public $imageUrl;
    public $alt;
    public $class;

    /**
     * Create a new component instance.
     */
    public function __construct(Article $article, $class = '', $alt = null)
    {
        $this->article = $article;
        $this->imageUrl = ImageHelper::getArticleImage($article);
        $this->alt = $alt ?? $article->title;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.article-image');
    }
}

