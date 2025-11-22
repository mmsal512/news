<?php

namespace App\Console\Commands;

use App\Jobs\FetchNewsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch {--type=all : Type of news to fetch (all, ai, tech, general)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news articles from external APIs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        
        $this->info("Starting news fetch for type: {$type}");
        
        try {
            // Dispatch the job
            FetchNewsJob::dispatch($type);
            
            $this->info("News fetch job dispatched successfully!");
            $this->info("Check the queue with: php artisan queue:work");
            
            Log::info("News fetch command executed", ['type' => $type]);
            
        } catch (\Exception $e) {
            $this->error("Failed to dispatch news fetch job: " . $e->getMessage());
            Log::error("News fetch command failed", [
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            return 1;
        }
        
        return 0;
    }
}