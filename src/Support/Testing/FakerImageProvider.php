<?php

namespace Support\Testing;

use Faker\Provider\Base;
use Illuminate\Http\File;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FakerImageProvider extends Base
{

    public function image(string $folder): string
    {
        $storage = Storage::disk('images');

        // dd($storage);

        if (!$storage->exists($folder)) {
            $storage->makeDirectory($folder);
        }
        /** @var Storage $storage */
        $file = $this->generator->file(base_path('/tests/Fixtures/images/') . $folder, $storage->path($folder), false);

        return '/storage/public/images/' . $file;
    }
}
