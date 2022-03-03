<?php declare(strict_types=1);

namespace Soyhuce\ModelInjection\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Soyhuce\ModelInjection\Tests\Fixtures\TestServiceProvider;

/**
 * @coversNothing
 */
class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database');
    }

    protected function getPackageProviders($app): array
    {
        return [TestServiceProvider::class];
    }
}
