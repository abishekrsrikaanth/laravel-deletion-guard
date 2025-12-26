<?php

namespace WorkDoneRight\DeletionGuard\Exceptions;

use Illuminate\Validation\ValidationException;

class DeletionBlockedException extends ValidationException
{
    public static function withBlockers(array $blockers): self
    {
        return self::withMessages([
            'delete' => collect($blockers)->pluck('message')->all(),
        ]);
    }
}
