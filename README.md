# Extended Model injection for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/soyhuce/laravel-model-injection.svg?style=flat-square)](https://packagist.org/packages/soyhuce/laravel-model-injection)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/soyhuce/laravel-model-injection/run-tests?label=tests)](https://github.com/soyhuce/laravel-model-injection/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/soyhuce/laravel-model-injection/Check%20&%20fix%20styling?label=code%20style)](https://github.com/soyhuce/laravel-model-injection/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/soyhuce/laravel-model-injection/PHPStan?label=phpstan)](https://github.com/soyhuce/laravel-model-injection/actions?query=workflow%3APHPStan+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/soyhuce/laravel-model-injection.svg?style=flat-square)](https://packagist.org/packages/soyhuce/laravel-model-injection)

Want to control have better control of model injection ? Need to validate the data before querying the database ?

Here is a package that allows you to do that.

## Installation

You can install the package via composer:

```bash
composer require soyhuce/laravel-model-injection
```

## Usage

### Implicit binding

To validate the url parameter used to inject the model in the controller, you can use
the `Soyhuce\ModelInjection\ValidatesImplicitBindings` trait in it.

You will have then to implement the method `public function routeBindingRules(): array`
which will define, for each key on which the model will be bound, the rules to validate the url parameter.

```php
use Soyhuce\ModelInjection\ValidatesImplicitBinding;

class Post extends Model 
{
    use ValidatesImplicitBinding;
    
    /**
     * @return array<string, mixed>
     */
    public function routeBindingRules(): array
    {
        return [
            'id' => 'integer',
            'slug' => ['string', 'min:5']
        ];
    }
}
```

This will allow you to validate the parameter to bind the `Post` in the routes using:

```php
Route::get('posts/{post}', function(Post $post) {
    //...
});

Route::get('posts-by-slug/{post:slug}', function(Post $post) {
    //...
});
```

If the parameter is not valid, a 404 error will be returned.

```
GET /posts/foo => 404
GET /posts-by-slug/bar => 404
```

See [https://laravel.com/docs/routing#implicit-binding](https://laravel.com/docs/routing#implicit-binding)

### Bindings explicites

You can explicitly bind your models using `\Soyhuce\ModelInjection\BindModels` trait in a service
provider (`RouteServiceProvider` for exemple).

```php
use Soyhuce\ModelInjection\BindModels;

class RouteServiceProvider extends ServiceProvider {

    use BindModels;

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot() {
        parent::boot();

        $this->bindModel('user', User::class, 'integer'); // Validates that the parameter is an integer
        
        // You can bind a model explicitly on a given column
        $this->bindModelOn('post', Post::class, ['string', 'min:5'], 'slug');
    }
}

```

If the given parameter is not valid, a 404 error will be returned.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Bastien Philippe](https://github.com/bastien-phi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
