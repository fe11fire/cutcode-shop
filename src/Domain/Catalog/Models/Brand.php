<?php

namespace Domain\Catalog\Models;

use Support\Traits\Models\HasSlug;
use Database\Factories\BrandFactory;
use Domain\Catalog\Collections\BrandCollection;
use Illuminate\Database\Eloquent\Model;
use Support\Traits\Models\HasThumbnail;
use Domain\Catalog\QueryBuilders\BrandQueryBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static Brand|BrandQueryBuilder query()
 */
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

    public function newEloquentBuilder($query): BrandQueryBuilder
    {
        return new BrandQueryBuilder($query);
    }

    public function newCollection(array $models = []): BrandCollection
    {
        return new BrandCollection($models);
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
