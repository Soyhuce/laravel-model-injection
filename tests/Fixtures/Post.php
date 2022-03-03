<?php declare(strict_types=1);

namespace Soyhuce\ModelInjection\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Soyhuce\ModelInjection\ValidatesImplicitBinding;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ValidatesImplicitBinding;

    protected $casts = [
        'deleted_at' => 'timestamp',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function routeBindingRules(): array
    {
        return [
            'id' => 'integer',
            'slug' => ['string', 'min:3'],
        ];
    }
}
