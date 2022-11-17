<?php

namespace App\Observers;

use Domain\Catalog\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     *
     * @param  \App\Models\src\Domain\Catalog\Models\Category  $category
     * @return void
     */
    public function created(Category $category)
    {
        Cache::forget('category_homepage');
    }

    /**
     * Handle the Category "updated" event.
     *
     * @param  \App\Models\src\Domain\Catalog\Models\Category  $category
     * @return void
     */
    public function updated(Category $category)
    {
        Cache::forget('category_homepage');
    }

    /**
     * Handle the Category "deleted" event.
     *
     * @param  \App\Models\src\Domain\Catalog\Models\Category  $category
     * @return void
     */
    public function deleted(Category $category)
    {
        Cache::forget('category_homepage');
    }

    /**
     * Handle the Category "restored" event.
     *
     * @param  \App\Models\src\Domain\Catalog\Models\Category  $category
     * @return void
     */
    public function restored(Category $category)
    {
        Cache::forget('category_homepage');
    }

    /**
     * Handle the Category "force deleted" event.
     *
     * @param  \App\Models\src\Domain\Catalog\Models\Category  $category
     * @return void
     */
    public function forceDeleted(Category $category)
    {
        Cache::forget('category_homepage');
    }
}
