<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'description',
        'language',
        'country',
        'category',
        'is_active',
        'last_fetch_at',
        'fetch_count',
        'reliability_score'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_fetch_at' => 'datetime',
        'reliability_score' => 'decimal:2'
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function publishedArticles(): HasMany
    {
        return $this->hasMany(Article::class)->published();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    public function updateFetchStats()
    {
        $this->increment('fetch_count');
        $this->update(['last_fetch_at' => now()]);
    }

    public function isReliable()
    {
        return $this->reliability_score >= 0.7;
    }
}
