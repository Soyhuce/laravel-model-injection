<?php declare(strict_types=1);

namespace Soyhuce\ModelInjection;

use Illuminate\Support\Facades\Validator;
use function in_array;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait ValidatesImplicitBinding
{
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        $this->validateRouteBinding($value, $field);

        return parent::resolveRouteBindingQuery($query, $value, $field);
    }

    protected function resolveChildRouteBindingQuery($childType, $value, $field)
    {
        $this->validateChildRouteBinding($childType, $value, $field);

        return parent::resolveChildRouteBindingQuery($childType, $value, $field);
    }

    public function validateRouteBinding(mixed $value, ?string $field): void
    {
        $field ??= $this->getRouteKeyName();

        if (Validator::make([$field => $value], [$field => $this->routeBindingRule($field)])->fails()) {
            InvalidRouteBinding::handle(static::class, $field);
        }
    }

    public function validateChildRouteBinding(string $childType, mixed $value, ?string $field): void
    {
        $related = $this->{$this->childRouteBindingRelationshipName($childType)}()->getRelated();

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
