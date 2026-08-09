<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoMeta extends Model
{
    use HasFactory;

    protected $table = 'seo_meta';

    protected $fillable = [
        'platform_id',
        'page_type',
        'page_id',
        'custom_path',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
    ];

    // Accessors with fallbacks
    public function getOgTitleOrDefaultAttribute(): string
    {
        return $this->og_title ?: $this->meta_title;
    }

    public function getOgDescriptionOrDefaultAttribute(): string
    {
        return $this->og_description ?: ($this->meta_description ?? '');
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return $this->og_image
            ? asset('storage/' . $this->og_image)
            : null;
    }

    // Relationships
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    // Polymorphic-style — resolve the related page
    public function relatedPage()
    {
        return match ($this->page_type) {
            'product' => Product::find($this->page_id),
            'category' => Category::find($this->page_id),
            'brand' => Brand::find($this->page_id),
            default => null,
        };
    }

    // Scopes
    public function scopeForPage($query, string $type, ?int $id = null)
    {
        return $query->where('page_type', $type)
            ->where('page_id', $id);
    }

    // Helper — get SEO meta for a specific page, with fallback
    public static function getForPage(int $platformId, string $pageType, ?int $pageId = null): ?self
    {
        return static::where('platform_id', $platformId)
            ->where('page_type', $pageType)
            ->where('page_id', $pageId)
            ->first();
    }
}