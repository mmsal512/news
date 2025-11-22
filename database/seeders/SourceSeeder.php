<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            [
                'name' => 'TechCrunch',
                'url' => 'https://techcrunch.com',
                'description' => 'Leading technology news and startup coverage',
                'language' => 'en',
                'country' => 'us',
                'category' => 'technology',
                'reliability_score' => 0.9,
                'is_active' => true,
            ],
            [
                'name' => 'MIT Technology Review',
                'url' => 'https://www.technologyreview.com',
                'description' => 'In-depth technology analysis and research coverage',
                'language' => 'en',
                'country' => 'us',
                'category' => 'technology',
                'reliability_score' => 0.95,
                'is_active' => true,
            ],
            [
                'name' => 'VentureBeat',
                'url' => 'https://venturebeat.com',
                'description' => 'Technology news with focus on AI and enterprise',
                'language' => 'en',
                'country' => 'us',
                'category' => 'technology',
                'reliability_score' => 0.85,
                'is_active' => true,
            ],
            [
                'name' => 'The Verge',
                'url' => 'https://www.theverge.com',
                'description' => 'Technology, science, art, and culture coverage',
                'language' => 'en',
                'country' => 'us',
                'category' => 'technology',
                'reliability_score' => 0.88,
                'is_active' => true,
            ],
            [
                'name' => 'Wired',
                'url' => 'https://www.wired.com',
                'description' => 'Technology and how it affects culture, the economy, and politics',
                'language' => 'en',
                'country' => 'us',
                'category' => 'technology',
                'reliability_score' => 0.92,
                'is_active' => true,
            ],
            [
                'name' => 'BBC Technology',
                'url' => 'https://www.bbc.com/technology',
                'description' => 'BBC technology news coverage',
                'language' => 'en',
                'country' => 'gb',
                'category' => 'technology',
                'reliability_score' => 0.93,
                'is_active' => true,
            ],
            [
                'name' => 'Reuters Technology',
                'url' => 'https://www.reuters.com/technology',
                'description' => 'Reuters global technology news',
                'language' => 'en',
                'country' => null,
                'category' => 'technology',
                'reliability_score' => 0.94,
                'is_active' => true,
            ],
            [
                'name' => 'AI News',
                'url' => 'https://artificialintelligence-news.com',
                'description' => 'Dedicated AI and machine learning news source',
                'language' => 'en',
                'country' => 'gb',
                'category' => 'technology',
                'reliability_score' => 0.82,
                'is_active' => true,
            ],
            [
                'name' => 'Nature Technology',
                'url' => 'https://www.nature.com',
                'description' => 'Scientific and technology research publications',
                'language' => 'en',
                'country' => 'gb',
                'category' => 'science',
                'reliability_score' => 0.98,
                'is_active' => true,
            ],
            [
                'name' => 'ArsTechnica',
                'url' => 'https://arstechnica.com',
                'description' => 'Technology news and analysis for tech enthusiasts',
                'language' => 'en',
                'country' => 'us',
                'category' => 'technology',
                'reliability_score' => 0.89,
                'is_active' => true,
            ]
        ];

        foreach ($sources as $source) {
            Source::firstOrCreate(
                ['name' => $source['name']],
                $source
            );
        }
    }
}