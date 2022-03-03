<?php declare(strict_types=1);

namespace Soyhuce\ModelInjection\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => Str::random(),
            'email' => Str::random() . '@email.com',
            'password' => Hash::make('password'),
        ];
    }
}
