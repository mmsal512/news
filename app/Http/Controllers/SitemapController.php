<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate main sitemap index
     */
    public function index()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $xml .= '  <sitemap>' . "\n";
        $xml .= '    <loc>' . url('/sitemap-articles.xml') . '</loc>' . "\n";
        $xml .= '    <lastmod>' . now()->toAtomString() . '</lastmod>' . "\n";
        $xml .= '  </sitemap>' . "\n";
        $xml .= '</sitemapindex>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate articles sitemap
     */
    public function articles()
    {
        $articles = Article::published()
            ->orderBy('published_at', 'desc')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml .= ' xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

        foreach ($articles as $article) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . route('articles.show', $article->id) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $article->updated_at->toAtomString() . '</lastmod>' . "\n";
            $xml .= '    <changefreq>weekly</changefreq>' . "\n";
            $xml .= '    <priority>0.8</priority>' . "\n";
            
            if ($article->published_at) {
                $xml .= '    <news:news>' . "\n";
                $xml .= '      <news:publication>' . "\n";
                $xml .= '        <news:name>' . htmlspecialchars(config('app.name')) . '</news:name>' . "\n";
                $xml .= '        <news:language>' . htmlspecialchars($article->language) . '</news:language>' . "\n";
                $xml .= '      </news:publication>' . "\n";
                $xml .= '      <news:publication_date>' . $article->published_at->toAtomString() . '</news:publication_date>' . "\n";
                $xml .= '      <news:title>' . htmlspecialchars($article->title) . '</news:title>' . "\n";
                if ($article->image_url) {
                    $xml .= '      <news:image>' . htmlspecialchars($article->image_url) . '</news:image>' . "\n";
                }
                $xml .= '    </news:news>' . "\n";
            }
            
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}
