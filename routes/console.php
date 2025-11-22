<?php

use App\Jobs\FetchNewsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule hourly news fetching
Schedule::job(new FetchNewsJob('all'))
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/news-fetch.log'));

// Schedule cleanup of old unpublished articles (daily at 2 AM)
Schedule::call(function () {
    app(\App\Services\News\NewsAggregatorService::class)->cleanupOldArticles(30);
})->name('cleanup-old-articles')->dailyAt('02:00')->withoutOverlapping();
