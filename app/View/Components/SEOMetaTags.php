<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SEOMetaTags extends Component
{
    public $title;
    public $description;
    public $image;
    public $url;
    public $type;
    public $article;

    /**
     * Create a new component instance.
     */
    public function __construct($title = null, $description = null, $image = null, $url = null, $type = 'website', $article = null)
    {
        $this->title = $title ?? config('app.name');
        $this->description = $description ?? config('app.description', 'Latest news and articles');
        $this->image = $image ?? asset('images/og-default.jpg');
        $this->url = $url ?? url()->current();
        $this->type = $type;
        $this->article = $article;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.seo-meta-tags');
    }
}

