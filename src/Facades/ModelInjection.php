<?php declare(strict_types=1);

namespace Soyhuce\ModelInjection\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Soyhuce\ModelInjection\ModelInjection
 */
class ModelInjection extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-model-injection';
    }
}
