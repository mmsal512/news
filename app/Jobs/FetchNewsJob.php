<?php

namespace App\Jobs;

use App\Services\News\NewsAggregatorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchNewsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $timeout = 600; // 10 minutes timeout
    public $tries = 3;

    private $newsType;

    /**
     * Create a new job instance.
     */
    public function __construct($newsType = 'all')
    {
        $this->newsType = $newsType;
    }

    /**
     * Execute the job.
     */
    public function handle(NewsAggregatorService $newsAggregator): void
    {
        Log::info('Starting news fetch job', ['type' => $this->newsType]);

        try {
            switch ($this->newsType) {
                case 'ai':
                    $result = $newsAggregator->fetchAndStoreAiNews();
                    Log::info("Fetched {$result} AI news articles");
                    break;
                    
                case 'tech':
                    $result = $newsAggregator->fetchAndStoreTechNews();
                    Log::info("Fetched {$result} tech news articles");
                    break;
                    
                case 'general':
                    $result = $newsAggregator->fetchAndStoreGeneralNews();
                    Log::info("Fetched {$result} general news articles");
                    break;
                    
                case 'all':
                default:
                    $results = $newsAggregator->fetchAllNews();
                    Log::info('Completed full news fetch', $results);
                    
                    // Clean up old articles
                    $cleanedUp = $newsAggregator->cleanupOldArticles();
                    Log::info("Cleaned up {$cleanedUp} old articles");
                    break;
            }
            
        } catch (\Exception $e) {
            Log::error('News fetch job failed', [
                'type' => $this->newsType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('News fetch job failed permanently', [
            'type' => $this->newsType,
            'error' => $exception->getMessage()
        ]);
    }
}
