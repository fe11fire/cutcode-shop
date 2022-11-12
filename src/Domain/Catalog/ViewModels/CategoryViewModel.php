<?php

namespace Domain\Catalog\ViewModels;

use Support\Traits\Makeable;
use Domain\Catalog\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryViewModel
{
    use Makeable;

    public function homePage()
    {
        return Cache::rememberForever('category_homepage', fn () => Category::query()
            ->homePage()
            ->get());
    }
}
