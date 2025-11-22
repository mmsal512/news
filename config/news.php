<?php

return [
    /*
    |--------------------------------------------------------------------------
    | News Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for news fetching and management
    |
    */

    // Auto-publish articles when fetched from APIs
    'auto_publish' => env('NEWS_AUTO_PUBLISH', false),

    // Default language for articles
    'default_language' => env('NEWS_DEFAULT_LANGUAGE', 'en'),

    // Maximum articles to fetch per request
    'max_articles_per_fetch' => env('NEWS_MAX_ARTICLES_PER_FETCH', 100),

    // Cleanup old unpublished articles after X days
    'cleanup_unpublished_after_days' => env('NEWS_CLEANUP_UNPUBLISHED_DAYS', 30),
];

