<?php

namespace App\Services\Content;

use App\Models\Article;
use Illuminate\Support\Facades\Log;

class AIRankingService
{
    /**
     * Calculate comprehensive ranking score for an article
     */
    public function calculateRankingScore(Article $article): float
    {
        $scores = [
            'relevance' => $this->calculateRelevanceScore($article),
            'freshness' => $this->calculateFreshnessScore($article),
            'engagement' => $this->calculateEngagementScore($article),
            'quality' => $this->calculateQualityScore($article),
            'source_reliability' => $this->calculateSourceReliabilityScore($article),
        ];

        // Weighted average
        $weights = [
            'relevance' => 0.30,
            'freshness' => 0.25,
            'engagement' => 0.20,
            'quality' => 0.15,
            'source_reliability' => 0.10,
        ];

        $totalScore = 0;
        foreach ($scores as $key => $score) {
            $totalScore += $score * ($weights[$key] ?? 0);
        }

        return min(1.0, max(0.0, $totalScore));
    }

    /**
     * Calculate relevance score based on AI keywords and category
     */
    private function calculateRelevanceScore(Article $article): float
    {
        $score = 0.5; // Base score

        // Boost for AI-related content
        if ($article->is_ai_related) {
            $score += 0.2;
        }

        // Boost based on AI score
        $score += $article->ai_score * 0.2;

        // Boost for proper categorization
        if ($article->category_id) {
            $score += 0.1;
        }

        return min(1.0, $score);
    }

    /**
     * Calculate freshness score based on publication date
     */
    private function calculateFreshnessScore(Article $article): float
    {
        if (!$article->published_at) {
            return 0.3;
        }

        $daysOld = now()->diffInDays($article->published_at);

        if ($daysOld <= 1) {
            return 1.0;
        } elseif ($daysOld <= 3) {
            return 0.8;
        } elseif ($daysOld <= 7) {
            return 0.6;
        } elseif ($daysOld <= 30) {
            return 0.4;
        } else {
            return max(0.1, 1.0 - ($daysOld / 365));
        }
    }

    /**
     * Calculate engagement score based on views and interactions
     */
    private function calculateEngagementScore(Article $article): float
    {
        $views = $article->views ?? 0;
        $uniqueViews = $article->unique_views ?? 0;

        // Normalize views (assuming 1000 views = max score)
        $viewScore = min(1.0, $views / 1000);
        $uniqueViewScore = min(1.0, $uniqueViews / 500);

        // Combine with 60/40 weight
        return ($viewScore * 0.6) + ($uniqueViewScore * 0.4);
    }

    /**
     * Calculate quality score based on content metrics
     */
    private function calculateQualityScore(Article $article): float
    {
        $score = 0.5; // Base score

        // Check content length
        $contentLength = strlen($article->content ?? '');
        if ($contentLength > 500) {
            $score += 0.2;
        } elseif ($contentLength > 200) {
            $score += 0.1;
        }

        // Check for summary
        if (!empty($article->summary)) {
            $score += 0.1;
        }

        // Check for image
        if (!empty($article->image_url)) {
            $score += 0.1;
        }

        // Check for meta description
        if (!empty($article->meta_description)) {
            $score += 0.1;
        }

        return min(1.0, $score);
    }

    /**
     * Calculate source reliability score
     */
    private function calculateSourceReliabilityScore(Article $article): float
    {
        if (!$article->source) {
            return 0.5;
        }

        return $article->source->reliability_score ?? 0.5;
    }

    /**
     * Update ranking score for an article
     */
    public function updateArticleRanking(Article $article): void
    {
        $rankingScore = $this->calculateRankingScore($article);
        
        // Store in a separate column if needed, or use existing ai_score
        // For now, we'll enhance the ai_score with ranking
        $article->update([
            'ai_score' => ($article->ai_score * 0.5) + ($rankingScore * 0.5)
        ]);

        Log::debug("Article ranking updated", [
            'article_id' => $article->id,
            'ranking_score' => $rankingScore
        ]);
    }

    /**
     * Get top ranked articles
     */
    public function getTopRankedArticles($limit = 10, $categoryId = null)
    {
        $query = Article::published()
            ->orderBy('ai_score', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit($limit);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->get();
    }
}

