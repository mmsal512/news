<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CopyrightNotice extends Component
{
    public $article;

    /**
     * Create a new component instance.
     */
    public function __construct($article)
    {
        $this->article = $article;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.copyright-notice');
    }
}

