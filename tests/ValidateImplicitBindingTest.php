<?php declare(strict_types=1);

use Illuminate\Support\Facades\Log;
use Soyhuce\ModelInjection\InvalidRouteBinding;
use Soyhuce\ModelInjection\RouteBindingValidationMustBeDefined;
use Soyhuce\ModelInjection\Tests\Fixtures\Post;
use Soyhuce\ModelInjection\Tests\Fixtures\User;

beforeEach(function (): void {
    $this->user = User::factory()->createOne();
    $this->post = Post::factory()->for($this->user)->createOne();

    defineRoute('users/{user}', fn (User $user) => $user->only(['id', 'name']));
    defineRoute('users-with-trashed/{user}', fn (User $user) => $user->only(['id', 'name']))
        ->withTrashed();

    defineRoute('users-on-name/{user:name}', fn (User $user) => $user->only(['id', 'name']));
    defineRoute('users-on-name-with-trashed/{user:name}', fn (User $user) => $user->only(['id', 'name']))
        ->withTrashed();

    defineRoute('users-on-email/{user:email}', fn (User $user) => $user->only(['id', 'name']));

    defineRoute('users/{user}/posts/{post:}', fn (User $user, Post $post) => $post->only(['id', 'slug']));
    defineRoute('users/{user}/posts-with-trashed/{post:}', fn (User $user, Post $post) => $post->only(['id', 'slug']))
        ->withTrashed();

    defineRoute('users/{user}/posts-on-slug/{post:slug}', fn (User $user, Post $post) => $post->only(['id', 'slug']));
    defineRoute(
        'users/{user}/posts-on-slug-with-trashed/{post:slug}',
        fn (User $user, Post $post) => $post->only(['id', 'slug'])
    )
        ->withTrashed();

    defineRoute(
        'users/{user}/posts-on-created-at/{post:created_at}',
        fn (User $user, Post $post) => $post->only(['id', 'slug'])
    );
});

afterEach(fn () => InvalidRouteBinding::handleUsing(null));

it('binds the model', function (): void {
    $this->getJson("users/{$this->user->id}")
        ->assertOk()
        ->assertJson([
            'id' => $this->user->id,
            'name' => $this->user->name,
        ]);
});

it('validates the parameters', function (): void {
    $this->getJson('users/foo')
        ->assertNotFound()
        ->assertJsonPath('message', 'Invalid route binding.');
});

it('return not found if the model does not exist', function (): void {
    $this->user->delete();

    $this->getJson("users/{$this->user->id}")
        ->assertNotFound()
        ->assertJsonPath(
            'message',
            "No query results for model [Soyhuce\\ModelInjection\\Tests\\Fixtures\\User] {$this->user->id}"
        );
});

it('can find soft deleted model', function (): void {
    $this->user->delete();

    $this->getJson("users-with-trashed/{$this->user->id}")
        ->assertOk()
        ->assertJson([
            'id' => $this->user->id,
            'name' => $this->user->name,
        ]);
});

it('can bind the model on custom key', function (): void {
    $this->getJson("users-on-name/{$this->user->name}")
        ->assertOk()
        ->assertJson([
            'id' => $this->user->id,
            'name' => $this->user->name,
        ]);
});

it('validates the custom key', function (): void {
    $this->getJson('users-on-name/1')
        ->assertNotFound()
        ->assertJsonPath('message', 'Invalid route binding.');
});

it('does not find the model on custom key', function (): void {
    $this->getJson('users-on-name/foo')
        ->assertNotFound()
        ->assertJsonPath('message', 'No query results for model [Soyhuce\ModelInjection\Tests\Fixtures\User] foo');
});

it('validates the custom key with trashed', function (): void {
    $this->getJson('users-on-name-with-trashed/1')
        ->assertNotFound()
        ->assertJsonPath('message', 'Invalid route binding.');
});

it('fails when custom key does not have validation rule', function (): void {
    $this->expectException(RouteBindingValidationMustBeDefined::class);
    $this->expectExceptionMessage('Route binding validation must be defined for field email of Soyhuce\\ModelInjection\\Tests\\Fixtures\\User.');

    $this->withoutExceptionHandling()
        ->getJson('users-on-email/1');
});

it('binds scoped model', function (): void {
    $this->getJson("users/{$this->user->id}/posts/{$this->post->id}")
        ->assertOk()
        ->assertJson([
            'id' => $this->post->id,
            'slug' => $this->post->slug,
        ]);
});

it('scopes scoped model', function (): void {
    $post = Post::factory()->createOne();

    $this->getJson("users/{$this->user->id}/posts/{$post->id}")
        ->assertNotFound()
        ->assertJsonPath(
            'message',
            "No query results for model [Soyhuce\\ModelInjection\\Tests\\Fixtures\\Post] {$post->id}"
        );
});

it('validates scoped bindings', function (): void {
    $this->getJson("users/{$this->user->id}/posts/foo")
        ->assertNotFound()
        ->assertJsonPath('message', 'Invalid route binding.');
});

it('return not found if scoped model is not found', function (): void {
    $this->post->delete();

    $this->getJson("users/{$this->user->id}/posts/{$this->post->id}")
        ->assertNotFound()
        ->assertJsonPath(
            'message',
            "No query results for model [Soyhuce\\ModelInjection\\Tests\\Fixtures\\Post] {$this->post->id}"
        );
});

it('can find soft deleted scoped model', function (): void {
    $this->user->delete();
    $this->post->delete();

    $this->getJson("users/{$this->user->id}/posts-with-trashed/{$this->post->id}")
        ->assertOk()
        ->assertJson([
            'id' => $this->post->id,
            'slug' => $this->post->slug,
        ]);
});

it('binds scoped model on custom key', function (): void {
    $this->getJson("users/{$this->user->id}/posts-on-slug/{$this->post->slug}")
        ->assertOk()
        ->assertJson([
            'id' => $this->post->id,
            'slug' => $this->post->slug,
        ]);
});

it('scopes scoped model on custom key', function (): void {
    $post = Post::factory()->createOne();

    $this->getJson("users/{$this->user->id}/posts-on-slug/{$post->slug}")
        ->assertNotFound()
        ->assertJsonPath(
            'message',
            "No query results for model [Soyhuce\\ModelInjection\\Tests\\Fixtures\\Post] {$post->slug}"
        );
});

it('validates scoped bindings on custom key', function (): void {
    $this->getJson("users/{$this->user->id}/posts-on-slug/3")
        ->assertNotFound()
        ->assertJsonPath('message', 'Invalid route binding.');
});

it('returns not found if scoped model is not found on custom key', function (): void {
    $this->post->delete();

    $this->getJson("users/{$this->user->id}/posts-on-slug/{$this->post->slug}")
        ->assertNotFound()
        ->assertJsonPath(
            'message',
            "No query results for model [Soyhuce\\ModelInjection\\Tests\\Fixtures\\Post] {$this->post->slug}"
        );
});

it('can find soft deleted scoped model on custom key', function (): void {
    $this->user->delete();
    $this->post->delete();

    $this->getJson("users/{$this->user->id}/posts-on-slug-with-trashed/{$this->post->slug}")
        ->assertOk()
        ->assertJson([
            'id' => $this->post->id,
            'slug' => $this->post->slug,
        ]);
});

it('fails when custom key does not have validation rule for scoped model', function (): void {
    $this->expectException(RouteBindingValidationMustBeDefined::class);
    $this->expectExceptionMessage('Route binding validation must be defined for field created_at of Soyhuce\\ModelInjection\\Tests\\Fixtures\\Post.');

    $this->withoutExceptionHandling()
        ->getJson("users/{$this->user->id}/posts-on-created-at/{$this->post->created_at}");
});

it('allows to customize invalid binding handling', function (): void {
    InvalidRouteBinding::handleUsing(function (string $class, string $field): never {
        Log::error("Invalid binding for {$class} on {$field}.");

        abort(422);
    });

    Log::spy()
        ->shouldReceive('error')
        ->with('Invalid binding for Soyhuce\\ModelInjection\\Tests\\Fixtures\\User on id.')
        ->once();

    $this->getJson('users/foo')
        ->assertStatus(422);
});
