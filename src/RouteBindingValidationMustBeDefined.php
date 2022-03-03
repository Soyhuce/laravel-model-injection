<?php declare(strict_types=1);

namespace Soyhuce\ModelInjection;

use Exception;

class RouteBindingValidationMustBeDefined extends Exception
{
    public function __construct(string $class, string $field)
    {
        parent::__construct("Route binding validation must be defined for field {$field} of {$class}.");
    }
}
