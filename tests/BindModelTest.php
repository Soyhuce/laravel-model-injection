<?php declare(strict_types=1);

use Soyhuce\ModelInjection\Tests\Fixtures\User;

beforeEach(function (): void {
    $this->user = User::factory()->createOne();

    defineRoute('users/{explicitUser}', fn (User $explicitUser) => $explicitUser->only(['id', 'name']));
    defineRoute('usersByName/{userByName}', fn (User $userByName) => $userByName->only(['id', 'name']));
    defineRoute('usersValidated/{userValidated}', fn (User $userValidated) => $userValidated->only(['id', 'name']));
});

it('can retrieve a user', function (): void {
    $this->getJson("users/{$this->user->id}")
        ->assertOk()
        ->assertJson([
            'id' => $this->user->id,
            'name' => $this->user->name,
        ]);
});

it('validates input data', function (): void {
    $this->getJson("users/{$this->user->name}")
        ->assertNotFound()
        ->assertJsonPath('message', 'The model key is invalid.');
});

it('returns not found response if the model does not exists', function (): void {
    $this->user->delete();
    $this->getJson("users/{$this->user->id}")
        ->assertNotFound()
        ->assertJsonPath('message', 'No query results for model [Soyhuce\ModelInjection\Tests\Fixtures\User].');
});

it('can retrieve the model on other field', function (): void {
    $this->getJson("usersByName/{$this->user->name}")
        ->assertOk()
        ->assertJson([
            'id' => $this->user->id,
            'name' => $this->user->name,
        ]);
});

it('uses the closure to retrieve the model', function (): void {
    $this->getJson("usersValidated/{$this->user->id}")
        ->assertNotFound()
        ->assertJsonPath('message', 'No query results for model [Soyhuce\ModelInjection\Tests\Fixtures\User].');

    $this->user->update(['email_verified_at' => now()]);

    $this->getJson("usersValidated/{$this->user->id}")
        ->assertOk()
        ->assertJson([
            'id' => $this->user->id,
            'name' => $this->user->name,
        ]);
});
