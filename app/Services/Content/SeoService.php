<?php

namespace App\Services\Content;

use App\Models\Article;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SeoService
{
    /**
     * Generate meta description for article
     */
    public function generateMetaDescription($article)
    {
        if ($article->meta_description) {
            return $article->meta_description;
        }

        $description = $article->summary ?: strip_tags($article->content);
        $description = preg_replace('/\s+/', ' ', $description);
        
        // Limit to 160 characters for SEO
        return Str::limit($description, 160);
    }

    /**
     * Generate Open Graph tags
     */
    public function generateOpenGraphTags($article)
    {
        return [
            'og:title' => $article->title,
            'og:description' => $this->generateMetaDescription($article),
            'og:image' => $article->image_url ?: asset('images/default-news.jpg'),
            'og:url' => route('articles.show', $article->id),
            'og:type' => 'article',
            'og:site_name' => config('app.name'),
            'article:published_time' => $article->published_at->toISOString(),
            'article:author' => $article->author ?? config('app.name'),
            'article:section' => $article->category->name ?? 'News',
        ];
    }

    /**
     * Generate Twitter Card tags
     */
    public function generateTwitterCardTags($article)
    {
        return [
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $article->title,
            'twitter:description' => $this->generateMetaDescription($article),
            'twitter:image' => $article->image_url ?: asset('images/default-news.jpg'),
        ];
    }

    /**
     * Generate structured data (JSON-LD)
     */
    public function generateStructuredData($article)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $article->title,
            'description' => $this->generateMetaDescription($article),
            'image' => $article->image_url ?: asset('images/default-news.jpg'),
            'datePublished' => $article->published_at->toISOString(),
            'dateModified' => $article->updated_at->toISOString(),
            'author' => [
                '@type' => 'Person',
                'name' => $article->author ?? config('app.name'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png'),
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('articles.show', $article->id),
            ],
        ];
    }

    /**
     * Generate sitemap entries for articles
     */
    public function generateSitemapEntries()
    {
        return Article::where('is_published', true)
            ->select('id', 'updated_at')
            ->get()
            ->map(function ($article) {
                return [
                    'url' => route('articles.show', $article->id),
                    'lastmod' => $article->updated_at->toISOString(),
                    'changefreq' => 'daily',
                    'priority' => 0.8,
                ];
            });
    }
}

