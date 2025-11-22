<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'summary',
        'url',
        'image_url',
        'published_at',
        'category_id',
        'source_id',
        'language',
        'ai_score',
        'is_ai_related',
        'is_published',
        'author',
        'tags',
        'meta_description',
        'external_id',
        'views',
        'unique_views',
        'copyright_notice',
        'attribution_text',
        'requires_attribution',
        'is_featured',
        'is_breaking',
        'gallery_images',
        'reading_time'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_ai_related' => 'boolean',
        'is_published' => 'boolean',
        'ai_score' => 'decimal:2',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_breaking' => 'boolean',
        'gallery_images' => 'array',
        'reading_time' => 'integer'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeAiRelated($query)
    {
        return $query->where('is_ai_related', true);
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('published_at', '>=', now()->subDays($days));
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBreaking($query)
    {
        return $query->where('is_breaking', true);
    }

    /**
     * Calculate reading time based on content length
     */
    public function calculateReadingTime()
    {
        $wordCount = str_word_count(strip_tags($this->content ?? ''));
        $readingTime = ceil($wordCount / 200); // Average reading speed: 200 words per minute
        $this->update(['reading_time' => $readingTime]);
        return $readingTime;
    }

    /**
     * Get reading time formatted
     */
    public function getReadingTimeFormattedAttribute()
    {
        $time = $this->reading_time ?? $this->calculateReadingTime();
        return $time . ' ' . ($time == 1 ? 'min' : 'mins') . ' read';
    }

    public function getExcerptAttribute($length = 150)
    {
        return Str::limit(strip_tags($this->content ?? ''), $length);
    }

    public function isAiNews()
    {
        return $this->is_ai_related && $this->ai_score > 0.5;
    }

    /**
     * Increment view count
     */
    public function incrementViews($unique = false)
    {
        $this->increment('views');
        if ($unique) {
            $this->increment('unique_views');
        }
    }

    /**
     * Get formatted view count
     */
    public function getFormattedViewsAttribute()
    {
        if ($this->views >= 1000000) {
            return round($this->views / 1000000, 1) . 'M';
        } elseif ($this->views >= 1000) {
            return round($this->views / 1000, 1) . 'K';
        }
        return $this->views;
    }

    /**
     * Generate slug from title
     */
    public function generateSlug()
    {
        if (empty($this->slug) && !empty($this->title)) {
            $this->slug = \Illuminate\Support\Str::slug($this->title);
            
            // Ensure uniqueness
            $originalSlug = $this->slug;
            $count = 1;
            while (static::where('slug', $this->slug)->where('id', '!=', $this->id)->exists()) {
                $this->slug = $originalSlug . '-' . $count;
                $count++;
            }
        }
        return $this->slug;
    }

    /**
     * Get attribution text
     */
    public function getAttributionAttribute()
    {
        if ($this->attribution_text) {
            return $this->attribution_text;
        }
        
        if ($this->source) {
            return "Source: {$this->source->name}";
        }
        
        return "Source: " . ($this->author ?? 'Unknown');
    }

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->generateSlug();
            }
        });
        
        static::updating(function ($article) {
            if ($article->isDirty('title') && empty($article->slug)) {
                $article->generateSlug();
            }
        });
    }
}
