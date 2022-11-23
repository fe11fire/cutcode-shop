<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Domain\Catalog\ViewModels\BrandViewModel;
use Domain\Catalog\ViewModels\CategoryViewModel;

class ProductController extends Controller
{
    public function __invoke(Product $product)
    {
        return view('product.show', [
            'product' => $product
        ]);
    }
}
