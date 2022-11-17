<?php

namespace App\Http\Controllers\Catalog;


use App\Models\Product;
use Domain\Catalog\Models\Brand;
use Domain\Catalog\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

class CatalogController extends Controller
{
    public function __invoke(?Category $category)
    {
        $brands = Brand::query()->has('products')->get();
        $categories = Category::query()->has('products')->get();

        $products = Product::query()
            ->select('id', 'title', 'slug', 'price', 'thumbnail')
            ->when($category->exists, function (Builder $query) use ($category) {
                $query->whereRelation('categories', 'categories.id', '=', $category->id);
            })
            ->searched()
            ->filtered()
            ->sorted()
            ->paginate(9);

        // $products->setRelation('brands', $brands);

        return view(
            'catalog.index',
            [
                'categories' => $categories,
                'products' => $products,
                'brands' => $brands,
                'category' => $category,
            ]
        );
    }
}
