<?php

namespace App\Helpers;

use App\Models\Category;

class ImageHelper
{
    /**
     * Get default article image
     * Returns a beautiful placeholder image based on category or generic
     */
    public static function getDefaultArticleImage($category = null, $size = '800x600')
    {
        // Use category-specific colors if available
        $colors = [
            'politics' => '4f46e5,6366f1', // Indigo
            'technology' => '06b6d4,3b82f6', // Cyan/Blue
            'sports' => '10b981,059669', // Green
            'entertainment' => 'ec4899,f472b6', // Pink
            'business' => 'f59e0b,f97316', // Orange
        ];

        $colorPair = $colors[strtolower($category?->slug ?? '')] ?? '6366f1,8b5cf6'; // Default: Indigo to Purple

        // Using placeholder.com with gradient-like effect
        return "https://images.unsplash.com/photo-1504711434969-e33886168f5c?w={$size}&q=80&auto=format&fit=crop";
        
        // Alternative: Using a service that generates beautiful placeholder images
        // return "https://via.placeholder.com/{$size}/{$colorPair}/ffffff?text=News";
    }

    /**
     * Get empty state image for categories
     */
    public static function getEmptyStateImage($category = null)
    {
        // Category-specific images from Unsplash
        $categoryImages = [
            'politics' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=800&q=80&auto=format&fit=crop',
            'technology' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&q=80&auto=format&fit=crop',
            'sports' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&q=80&auto=format&fit=crop',
            'entertainment' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800&q=80&auto=format&fit=crop',
            'business' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&q=80&auto=format&fit=crop',
        ];

        if ($category && isset($categoryImages[strtolower($category->slug ?? '')])) {
            return $categoryImages[strtolower($category->slug)];
        }

        // Default empty state image
        return 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&q=80&auto=format&fit=crop';
    }

    /**
     * Get article image URL with fallback
     */
    public static function getArticleImage($article, $size = '800x600')
    {
        if ($article->image_url) {
            return $article->image_url;
        }

        return self::getDefaultArticleImage($article->category, $size);
    }

    /**
     * Generate a beautiful gradient placeholder
     */
    public static function getGradientPlaceholder($category = null, $width = 800, $height = 600)
    {
        $colors = [
            'politics' => ['from-indigo-500', 'to-purple-600'],
            'technology' => ['from-cyan-500', 'to-blue-600'],
            'sports' => ['from-green-500', 'to-emerald-600'],
            'entertainment' => ['from-pink-500', 'to-rose-600'],
            'business' => ['from-orange-500', 'to-amber-600'],
        ];

        $gradient = $colors[strtolower($category?->slug ?? '')] ?? ['from-indigo-500', 'to-purple-600'];
        
        // Return SVG gradient placeholder
        return "data:image/svg+xml," . urlencode("
            <svg width='{$width}' height='{$height}' xmlns='http://www.w3.org/2000/svg'>
                <defs>
                    <linearGradient id='grad' x1='0%' y1='0%' x2='100%' y2='100%'>
                        <stop offset='0%' style='stop-color:#{$gradient[0]};stop-opacity:1' />
                        <stop offset='100%' style='stop-color:#{$gradient[1]};stop-opacity:1' />
                    </linearGradient>
                </defs>
                <rect width='{$width}' height='{$height}' fill='url(#grad)'/>
                <text x='50%' y='50%' font-family='Arial, sans-serif' font-size='24' fill='white' text-anchor='middle' dominant-baseline='middle' opacity='0.3'>News Article</text>
            </svg>
        ");
    }
}
