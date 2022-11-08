<?php

namespace App\Models;

use App\Traits\Models\HasSlug;
use App\Traits\Models\HasThumbnail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;
    use HasSlug;
    use HasThumbnail;

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

    public function scopeHomePage(Builder $query)
    {
        $query->where('on_home_page', true)->orderBy('sorting')->limit(6);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
