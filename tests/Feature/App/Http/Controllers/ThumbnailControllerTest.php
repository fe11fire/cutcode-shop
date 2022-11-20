<?php

namespace Tests\Feature\App\Http\Controllers;

use Tests\TestCase;
use App\Models\Product;
use Mockery\MockInterface;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Database\Factories\ProductFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThumbnailControllerTest extends TestCase
{
    use RefreshDatabase;

    // private function mockImage($method, $size): MockInterface
    // {
    //     $image = $this->mock(Image::class, function (MockInterface $m) use ($method) {
    //         $m->shouldReceive('make')->once()->andReturnSelf();
    //         $m->shouldReceive($method)->once()->andReturnSelf();
    //         $m->shouldReceive('save')->once()->andReturn('1111');
    //     });

    //     return $image;
    // }

    /**
     * @test
     * @return void
     */
    public function it_generated_success(): void
    {
        $size = '500x500';
        $method = 'resize';

        config()->set('thumbnail', ['allowed_sizes' => [$size]]);

        $storage = Storage::disk('images');

        /** @var Product $product */
        $product = ProductFactory::new()->create();

        $respone = $this->get($product->makeThumbnail($size, $method));

        // dd($this->mockImage($method, $size));

        $respone->assertOk();


        /** @var Storage $storage */
        $storage->assertExists(
            "products/$method/$size/" . File::basename($product->thumbnail)
        );
    }
}
