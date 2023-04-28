<?php

namespace Soyhuce\ModelInjection;

use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InvalidRouteBinding
{
    /** @var ?Closure(string, string) */
    public static ?Closure $handler = null;

    /**
     * @param ?\Closure(string, string): mixed $handler
     */
    public static function handleUsing(?Closure $handler): void
    {
        static::$handler = $handler;
    }

    public static function handle(string $model, string $field): void
    {
        static::$handler ??= function () {
            throw new ModelNotFoundException("Invalid route binding.");
        };

        (static::$handler)($model, $field);
    }
}
