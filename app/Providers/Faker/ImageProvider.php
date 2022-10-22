<?php

namespace App\Providers\Faker;

use Faker\Provider\Base;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageProvider extends Base
{

    public function image(string $folder): string
    {
        $filename = rand(1, 9) . '.jpg';
        $storage_filename = $folder . '/' . Str::random(10) . '.jpg';

        if (Storage::exists($storage_filename)) {
            return '/storage/' . $storage_filename;
        }

        $file = base_path('/tests/Fixtures/' . $folder . '/') . $filename;
        // dd($file);
        Storage::put(
            $storage_filename,
            file_get_contents($file),
        );

        return '/storage/' . $storage_filename;
    }
}
