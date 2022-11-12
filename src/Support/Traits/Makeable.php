<?php

namespace Support\Traits;


trait Makeable
{
    public static function make(mixed ...$arguments): static
    {
        // dd(new static(...$arguments));
        return new static(...$arguments);
    }
}
