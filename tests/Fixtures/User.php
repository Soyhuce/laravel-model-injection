<?php declare(strict_types=1);

namespace Soyhuce\ModelInjection\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Soyhuce\ModelInjection\ValidatesImplicitBinding;

class User extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ValidatesImplicitBinding;

    protected $casts = [
        'deleted_at' => 'timestamp',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function routeBindingRules(): array
    {
        return [
            'id' => 'integer',
            'name' => ['string', 'min:3'],
        ];
    }
}
