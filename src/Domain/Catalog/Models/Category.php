<?php

namespace Domain\Catalog\Models;

use App\Models\Product;
use Support\Traits\Models\HasSlug;
use Database\Factories\CategoryFactory;
use Domain\Catalog\Collections\CategoryCollection;
use Illuminate\Database\Eloquent\Model;
use Support\Traits\Models\HasThumbnail;
use Domain\Catalog\QueryBuilders\CategoryQueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static Category|CategoryQueryBuilder query()
 */
class Category extends Model
{
    use HasFactory;
    use HasSlug;
    use HasThumbnail;

    protected $table = 'categories';
    protected $factory = CategoryFactory::class;

    protected $fillable = [
        'slug',
        'title',
        'on_home_page',
        'sorting',
    ];

    protected function thumbnailDir(): string
    {
        return 'categories';
    }

    public function newCollection(array $models = []): CategoryCollection
    {
        return new CategoryCollection($models);
    }

    public function newEloquentBuilder($query): CategoryQueryBuilder
    {
        return new CategoryQueryBuilder($query);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    protected static function newFactory()
    {
        return CategoryFactory::new();
    }
}
