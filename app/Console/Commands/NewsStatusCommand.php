<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use App\Services\News\NewsAggregatorService;
use Illuminate\Console\Command;

class NewsStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show news system statistics and status';

    /**
     * Execute the console command.
     */
    public function handle(NewsAggregatorService $newsAggregator)
    {
        $this->info('📊 News System Status');
        $this->line('');
        
        // Get statistics
        $stats = $newsAggregator->getNewsStatistics();
        
        // Display basic stats
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Articles', $stats['total_articles']],
                ['Published Articles', $stats['published_articles']],
                ['AI Articles', $stats['ai_articles']],
                ['Recent Articles (7 days)', $stats['recent_articles']],
            ]
        );
        
        // Articles by language
        if (!empty($stats['articles_by_language'])) {
            $this->line('');
            $this->info('📝 Articles by Language:');
            foreach ($stats['articles_by_language'] as $language => $count) {
                $this->line("  {$language}: {$count}");
            }
        }
        
        // Articles by category
        if (!empty($stats['articles_by_category'])) {
            $this->line('');
            $this->info('🏷️ Articles by Category:');
            foreach ($stats['articles_by_category'] as $category => $count) {
                $this->line("  {$category}: {$count}");
            }
        }
        
        // Source information
        $this->line('');
        $this->info('📡 News Sources:');
        $sources = Source::select('name', 'fetch_count', 'last_fetch_at', 'reliability_score', 'is_active')
            ->orderBy('fetch_count', 'desc')
            ->get();
            
        if ($sources->count() > 0) {
            $sourceData = $sources->map(function ($source) {
                return [
                    'name' => $source->name,
                    'fetches' => $source->fetch_count,
                    'last_fetch' => $source->last_fetch_at ? $source->last_fetch_at->diffForHumans() : 'Never',
                    'reliability' => $source->reliability_score,
                    'status' => $source->is_active ? '✅ Active' : '❌ Inactive',
                ];
            })->toArray();
            
            $this->table(
                ['Source', 'Fetches', 'Last Fetch', 'Reliability', 'Status'],
                $sourceData
            );
        }
        
        // Recent articles
        $this->line('');
        $this->info('📰 Recent Articles (Last 10):');
        $recentArticles = Article::with(['category', 'source'])
            ->latest('published_at')
            ->limit(10)
            ->get();
            
        if ($recentArticles->count() > 0) {
            $articleData = $recentArticles->map(function ($article) {
                return [
                    'title' => \Str::limit($article->title, 50),
                    'category' => $article->category->name ?? 'N/A',
                    'source' => $article->source->name ?? 'N/A',
                    'published' => $article->published_at->diffForHumans(),
                    'status' => $article->is_published ? '✅ Published' : '⏳ Pending',
                    'ai_score' => number_format($article->ai_score, 2),
                ];
            })->toArray();
            
            $this->table(
                ['Title', 'Category', 'Source', 'Published', 'Status', 'AI Score'],
                $articleData
            );
        } else {
            $this->warn('No articles found. Run "php artisan news:fetch" to get started.');
        }
    }
}