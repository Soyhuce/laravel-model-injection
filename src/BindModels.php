<?php declare(strict_types=1);

namespace Soyhuce\ModelInjection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use function is_callable;

trait BindModels
{
    /**
     * Bind models of class $class using route parameter $param validated with rules $rules.
     * Model is found using default query or the one provided by $closure.
     */
    protected function bindModel(string $param, string $class, mixed $rules, ?callable $closure = null): void
    {
        $this->bindModelOn($param, $class, (new $class())->getRouteKeyName(), $rules, $closure);
    }

    /**
     * Bind models of class $class using route parameter $param validated with rules $rules.
     * Model is found using default query or the one provided by $closure, searching on field $field.
     */
    protected function bindModelOn(
        string $param,
        string $class,
        string $field,
        mixed $rules,
        ?callable $closure = null,
    ): void {
        Route::bind($param, function ($value) use ($param, $class, $field, $rules, $closure): ?Model {
            if ($value === null) {
                return null;
            }

            if (Validator::make([$param => $value], [$param => $rules])->fails()) {
                throw new ModelNotFoundException('The model key is invalid.');
            }

            if (is_callable($closure)) {
                $query = $closure();
            } else {
                $query = $class::query();
            }

            return $query->where($field, $value)->firstOrFail();
        });
    }
}
