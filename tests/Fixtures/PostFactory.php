<?php declare(strict_types=1);

namespace Soyhuce\ModelInjection\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'slug' => Str::random(),
        ];
    }
}
