<?php

namespace Domain\Catalog\ViewModels;

use Domain\Catalog\Models\Brand;
use Illuminate\Support\Facades\Cache;
use Support\Traits\Makeable;

class BrandViewModel
{
    use Makeable;

    public function homePage()
    {
        return Cache::rememberForever('brand_homepage', fn () => Brand::query()->homePage()->get());
    }
}
