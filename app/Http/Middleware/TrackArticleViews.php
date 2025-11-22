<?php

namespace App\Http\Middleware;

use App\Models\Article;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class TrackArticleViews
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track on article detail pages
        if ($request->routeIs('articles.show') && $request->route('article')) {
            $article = $request->route('article');
            
            if ($article instanceof Article) {
                $this->trackView($article, $request);
            }
        }

        return $response;
    }

    /**
     * Track article view
     */
    private function trackView(Article $article, Request $request): void
    {
        $cookieName = 'article_viewed_' . $article->id;
        $isUnique = !$request->cookie($cookieName);

        // Increment views
        $article->incrementViews($isUnique);

        // Set cookie to track unique views (expires in 24 hours)
        if ($isUnique) {
            cookie()->queue($cookieName, true, 60 * 24);
        }
    }
}
