<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InQueryRule implements ValidationRule
{


    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if (request()->has($attribute) && !array_key_exists($attribute, request()->query())) {
            $fail("The :attribute must be exists in query param.");

        }

    }
}
