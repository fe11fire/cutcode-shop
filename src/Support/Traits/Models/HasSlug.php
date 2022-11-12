<?php

declare(strict_types=1);

namespace Support\Traits\Models;

use Illuminate\Database\Eloquent\Model;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function (Model $item) {
            $item->slug = $item->slug ?? self::searchSlug($item);
        });
    }

    protected static function searchSlug($item): string
    {
        $i = 0;
        do {
            $slug = self::makeSlug($item, $i);
            $i++;
        } while ($item::where('slug', '=', $slug)->exists());

        return $slug;
    }

    protected static function makeSlug($item, $index = 0): string
    {
        $index = $index == 0 ? '' : ' ' . $index;
        return (string) str($item->{self::slugFrom()})->append($index)->slug();
    }

    public static function slugFrom(): string
    {
        return 'title';
    }
}
