<?php

namespace Domain\Auth\DTOs;

use Illuminate\Http\Request;
use Support\Traits\Makeable;

class NewUserDTO
{
    use Makeable;


    public function __construct(
        public string $name,
        public string $email,
        public string $password,

    ) {
    }

    public static function fromRequest(Request $request)
    {
        // return new self(
        //     $request->get('name'),
        //     $request->get('email'),
        //     $request->get('password')
        // );

        return self::make(...$request->only(['name', 'email', 'password']));
    }
}
