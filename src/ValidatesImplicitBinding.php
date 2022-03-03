<?php declare(strict_types=1);

namespace Soyhuce\ModelInjection;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function in_array;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait ValidatesImplicitBinding
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @param string|null $field
     * @return \Illuminate\Database\Eloquent\Builder
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        $this->validateRouteBinding($value, $field);

        return parent::resolveRouteBindingQuery($query, $value, $field);
    }

    /**
     * @param string $childType
     * @param mixed $value
     * @param string|null $field
     * @return \Illuminate\Database\Eloquent\Builder
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    protected function resolveChildRouteBindingQuery($childType, $value, $field)
    {
        $this->validateChildRouteBinding($childType, $value, $field);

        return parent::resolveChildRouteBindingQuery($childType, $value, $field);
    }

    public function validateRouteBinding(mixed $value, ?string $field): void
    {
        $field ??= $this->getRouteKeyName();

        if (Validator::make([$field => $value], [$field => $this->routeBindingRule($field)])->fails()) {
            throw new ModelNotFoundException('The model key is invalid.');
        }
    }

    public function validateChildRouteBinding(string $childType, mixed $value, string $field): void
    {
        $related = $this->{Str::plural(Str::camel($childType))}()->getRelated();

        if (in_array(ValidatesImplicitBinding::class, class_uses_recursive($related))) {
            $related->validateRouteBinding($value, $field ?: $related->getRouteKey());
        }
    }

    public function routeBindingRule(string $field): mixed
    {
        $rules = $this->routeBindingRules();

        if (!isset($rules[$field])) {
            throw new RouteBindingValidationMustBeDefined(static::class, $field);
        }

        return $rules[$field];
    }

    /**
     * @return array<string, mixed>
     */
    abstract public function routeBindingRules(): array;
}
