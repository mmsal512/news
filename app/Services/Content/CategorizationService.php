<?php

namespace App\Services\Content;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategorizationService
{
    /**
     * Automatically categorize an article based on content analysis
     */
    public function categorizeArticle(Article $article): ?Category
    {
        $title = strtolower($article->title ?? '');
        $content = strtolower($article->content ?? '');
        $summary = strtolower($article->summary ?? '');
        $combinedText = $title . ' ' . $summary . ' ' . substr($content, 0, 500);

        // Define category keywords and their weights
        $categoryKeywords = [
            'ai-news' => [
                'artificial intelligence' => 3,
                'machine learning' => 3,
                'deep learning' => 3,
                'neural network' => 2,
                'chatgpt' => 2,
                'openai' => 2,
                'ai model' => 2,
                'llm' => 2,
                'generative ai' => 2,
                'ai' => 1,
                'automation' => 1,
                'algorithm' => 1,
            ],
            'technology' => [
                'technology' => 2,
                'tech' => 2,
                'software' => 2,
                'hardware' => 2,
                'innovation' => 1,
                'startup' => 1,
                'digital' => 1,
                'app' => 1,
                'platform' => 1,
            ],
            'tutorials' => [
                'tutorial' => 3,
                'how to' => 3,
                'guide' => 2,
                'learn' => 2,
                'step by step' => 2,
                'course' => 1,
                'training' => 1,
                'lesson' => 1,
            ],
            'general' => [
                'news' => 1,
                'update' => 1,
                'report' => 1,
            ],
        ];

        $scores = [];
        foreach ($categoryKeywords as $categorySlug => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword => $weight) {
                $count = substr_count($combinedText, $keyword);
                $score += $count * $weight;
            }
            $scores[$categorySlug] = $score;
        }

        // Get the category with highest score
        arsort($scores);
        $topCategorySlug = array_key_first($scores);
        $topScore = $scores[$topCategorySlug] ?? 0;

        // Only categorize if score is above threshold
        if ($topScore > 0) {
            $category = Category::where('slug', $topCategorySlug)->first();
            if ($category) {
                $article->update(['category_id' => $category->id]);
                Log::info("Article categorized", [
                    'article_id' => $article->id,
                    'category' => $category->name,
                    'score' => $topScore
                ]);
                return $category;
            }
        }

        return null;
    }

    /**
     * Re-categorize all uncategorized articles
     */
    public function recategorizeUncategorized()
    {
        $articles = Article::whereNull('category_id')
            ->orWhere('category_id', 0)
            ->get();

        $count = 0;
        foreach ($articles as $article) {
            if ($this->categorizeArticle($article)) {
                $count++;
            }
        }

        return $count;
    }
}
