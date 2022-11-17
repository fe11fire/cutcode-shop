<?php

namespace App\Observers;

use Domain\Catalog\Models\Brand;
use Illuminate\Support\Facades\Cache;


class BrandObserver
{
    /**
     * Handle the Brand "created" event.
     *
     * @param  \App\Models\src\Domain\Catalog\Models\Brand  $brand
     * @return void
     */
    public function created(Brand $brand)
    {
        Cache::forget('brand_homepage');
    }

    /**
     * Handle the Brand "updated" event.
     *
     * @param  \App\Models\src\Domain\Catalog\Models\Brand  $brand
     * @return void
     */
    public function updated(Brand $brand)
    {
        Cache::forget('brand_homepage');
    }

    /**
     * Handle the Brand "deleted" event.
     *
     * @param  \App\Models\src\Domain\Catalog\Models\Brand  $brand
     * @return void
     */
    public function deleted(Brand $brand)
    {
        Cache::forget('brand_homepage');
    }

    /**
     * Handle the Brand "restored" event.
     *
     * @param  \App\Models\src\Domain\Catalog\Models\Brand  $brand
     * @return void
     */
    public function restored(Brand $brand)
    {
        Cache::forget('brand_homepage');
    }

    /**
     * Handle the Brand "force deleted" event.
     *
     * @param  \App\Models\src\Domain\Catalog\Models\Brand  $brand
     * @return void
     */
    public function forceDeleted(Brand $brand)
    {
        Cache::forget('brand_homepage');
    }
}
