<?php declare(strict_types=1);

namespace Soyhuce\ModelInjection\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Soyhuce\ModelInjection\BindModels;

class TestServiceProvider extends ServiceProvider
{
    use BindModels;

    public function boot(): void
    {
        Model::unguard();

        $this->bindModel('explicitUser', User::class, 'integer');
        $this->bindModelOn('userByName', User::class, 'name', 'string');
        $this->bindModel(
            'userValidated',
            User::class,
            'integer',
            static fn () => User::query()->whereNotNull('email_verified_at')
        );

        Factory::guessFactoryNamesUsing(fn (string $model) => $model . 'Factory');
    }
}
