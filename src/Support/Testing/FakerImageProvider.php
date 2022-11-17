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
        if (!Storage::exists($folder)) {
            Storage::makeDirectory($folder);
        }
        $files = FacadesFile::files(base_path('/tests/Fixtures/' . $folder));

        // dd($files[rand(0, (count($files) - 1))]->getFilenameWithoutExtension());
        $filename = $files[rand(0, (count($files) - 1))]->getRelativePathname();
        $storage_filename = '' . $folder . '/' . Str::random(10) . '.jpg';


        if (Storage::exists($storage_filename)) {
            return '/storage/' . $storage_filename;
        }

        $file = base_path('/tests/Fixtures/' . $folder . '/') . $filename;
        // dd($file);
        Storage::put(
            $storage_filename,
            file_get_contents($file),
        );

        return '/storage/public/' . $storage_filename;
    }
}
