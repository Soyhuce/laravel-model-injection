<?php declare(strict_types=1);

namespace Soyhuce\ModelInjection\Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDeprecationHandling;
use Orchestra\Testbench\TestCase as Orchestra;
use PHPUnit\Framework\Attributes\CoversNothing;
use Soyhuce\ModelInjection\Tests\Fixtures\TestServiceProvider;

#[CoversNothing]
class TestCase extends Orchestra
{
    use InteractsWithDeprecationHandling;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutDeprecationHandling();

        $this->loadMigrationsFrom(__DIR__ . '/database');
    }

    protected function getPackageProviders($app): array
    {
        return [TestServiceProvider::class];
    }
}
