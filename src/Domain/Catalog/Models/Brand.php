<?php

namespace Domain\Catalog\Models;

use Database\Factories\BrandFactory;
use Support\Traits\Models\HasSlug;
use Support\Traits\Models\HasThumbnail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;
    use HasSlug;
    use HasThumbnail;

    protected $table = 'brands';
    protected $factory = BrandFactory::class;

    protected $fillable = [
        'slug',
        'title',
        'thumbnail',
        'on_home_page',
        'sorting',
    ];

    protected function thumbnailDir(): string
    {
        return 'brands';
    }

    public function scopeHomePage(Builder $query)
    {
        $query->where('on_home_page', true)->orderBy('sorting')->limit(6);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected static function newFactory()
    {
        return BrandFactory::new();
    }
}
