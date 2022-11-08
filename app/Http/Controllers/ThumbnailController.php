<?php

namespace App\Http\Controllers;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ThumbnailController extends Controller
{
    public function __invoke(
        string $dir,
        string $method,
        string $size,
        string $file,
    ) {
        // dd($size);
        abort_if(!in_array($size, config('thumbnail.allowed_sizes', [])), 403, ' size not allowed');

        $storage = Storage::disk('images');

        $realPath = "$dir/$file";
        $newDirPath = "$dir/$method/$size";
        $resultPath = "$newDirPath/$file";
        // dd($newDirPath);
        if (!$storage->exists($newDirPath)) {
            // dd($storage);
            $storage->makeDirectory($newDirPath);
        }
        // dd($storage->path($realPath));
        if (!$storage->exists($resultPath)) {
            $image = Image::make($storage->path($realPath));
            [$w, $h] = explode('x', $size);
            $image->{$method}($w, $h);
            $image->save($storage->path($resultPath));
        }

        return response()->file($storage->path($resultPath));
    }
}
