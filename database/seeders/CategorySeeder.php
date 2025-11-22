<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'AI News',
                'slug' => 'ai-news',
                'description' => 'Latest news about Artificial Intelligence, Machine Learning, and Deep Learning technologies',
                'color' => '#3B82F6',
                'icon' => 'fas fa-robot',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'General technology news, innovation, and tech industry updates',
                'color' => '#10B981',
                'icon' => 'fas fa-microchip',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Tutorials',
                'slug' => 'tutorials',
                'description' => 'Learning resources, how-to guides, and educational content',
                'color' => '#8B5CF6',
                'icon' => 'fas fa-graduation-cap',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'General News',
                'slug' => 'general',
                'description' => 'General news and current affairs from various sources',
                'color' => '#6B7280',
                'icon' => 'fas fa-newspaper',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Science',
                'slug' => 'science',
                'description' => 'Scientific discoveries, research, and innovation news',
                'color' => '#F59E0B',
                'icon' => 'fas fa-flask',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Business news, startups, and economic updates',
                'color' => '#EF4444',
                'icon' => 'fas fa-chart-line',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Politics',
                'slug' => 'politics',
                'description' => 'Political news, government updates, and policy changes',
                'color' => '#DC2626',
                'icon' => 'fas fa-landmark',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Sports',
                'slug' => 'sports',
                'description' => 'Sports news, matches, and athlete updates',
                'color' => '#059669',
                'icon' => 'fas fa-football-ball',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Entertainment',
                'slug' => 'entertainment',
                'description' => 'Entertainment news, celebrity updates, and media',
                'color' => '#7C3AED',
                'icon' => 'fas fa-film',
                'sort_order' => 9,
                'is_active' => true,
            ]
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}