<?php declare(strict_types=1);

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Soyhuce\ModelInjection\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function defineRoute(string $url, Closure $callback): Route
{
    return RouteFacade::get($url, $callback)
        ->middleware(SubstituteBindings::class);
}
