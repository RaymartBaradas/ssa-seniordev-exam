<?php

namespace Tests\Feature;

use App\Events\UserSaved;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;
    use WithFaker;

    /**
     * @test
     * @testdox It can return users.index view with users data
     * @return void
     */
    public function testIndex(): void
    {
        // Arrangements
        User::factory()->count(5)->create();
        $user = User::factory()->create();

        // Actions
        $response = $this->actingAs($user)->get(route('users.index'));

        // Assertions
        $response->assertOk();
        $response->assertViewIs('users.index');
        $response->assertViewHas('users');
    }

    /**
     * @test
     * @testdox It can return users.create view
     * @return void
     */
    public function testCreate(): void
    {
        // Arrangements
        $user = User::factory()->create();

        // Actions
        $response = $this->actingAs($user)->get(route('users.create'));

        // Assertions
        $response->assertOk();
        $response->assertViewIs('users.create');
    }

    /**
     * @test
     * @testdox It can store a user to database and redirect to users.index view with success
     * @return void
     */
    public function testStore(): void
    {
        // Arrangements
        Event::fake();
        $user = User::factory()->create();
        Storage::fake('public');
        $payload = [
            'prefixname' => 'Mr',
            'firstname' => fake()->firstName(),
            'middlename' => 'A',
            'lastname' => fake()->lastName(),
            'suffixname' => 'Jr',
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'photo' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        // Actions
        $response = $this->actingAs($user)->post(route('users.store'), $payload);

        // Assertions
        Event::assertDispatched(UserSaved::class);
        $this->assertDatabaseHas('users', [
            'username' => $payload['username'],
            'email' => $payload['email'],
        ]);
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'User has been created.');
    }

    /**
     * @test
     * @testdox It can return users.show view with user data
     * @return void
     */
    public function testShow(): void
    {
        // Arrangements
        $user = User::factory()->create();

        // Actions
        $response = $this->actingAs($user)->get(route('users.show', $user->id));

        // Assertions
        $response->assertOk();
        $response->assertViewIs('users.show');
        $response->assertViewHas('user');
    }

    /**
     * @test
     * @testdox It can return users.edit view with user data
     * @return void
     */
    public function testEdit(): void
    {
        // Arrangements
        $user = User::factory()->create();

        // Actions
        $response = $this->actingAs($user)->get(route('users.edit', $user->id));

        // Assertions
        $response->assertOk();
        $response->assertViewIs('users.edit');
        $response->assertViewHas('user');
    }

    /**
     * @test
     * @testdox It can update a user in database and redirect to users.index view with success
     * @return void
     */
    public function testUpdate(): void
    {
        // Arrangements
        $user = User::factory()->create();
        $payload = [
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
        ];

        // Actions
        $response = $this->actingAs($user)->put(route('users.update', $user->id), $payload);

        // Assertions
        $this->assertDatabaseHas('users', [
            'username' => $payload['username'],
            'email' => $payload['email'],
        ]);
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'User has been updated.');
    }

    /**
     * @test
     * @testdox It can soft delete the user and redirect to users.index view with success
     * @return void
     */
    public function testDestroy(): void
    {
        // Arrangements
        $user = User::factory()->create();

        // Actions
        $response = $this->actingAs($user)->delete(route('users.destroy', $user->id));

        // Assertions
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'User has been deleted.');
    }

    /**
     * @test
     * @testdox It can return users.deleted.index view with users data
     * @return void
     */
    public function testTrashed(): void
    {
        // Arrangements
        User::factory()->count(5)->create();
        $user = User::factory()->create();
        $trash = User::factory()->create();
        $trash->delete();

        // Actions
        $response = $this->actingAs($user)->get(route('users.trashed'));

        // Assertions
        $response->assertOk();
        $response->assertViewIs('users.deleted.index');
        $response->assertViewHas('users');
    }

    /**
     * @test
     * @testdox It can restore the user and redirect to users.trashed route with success
     * @return void
     */
    public function testRestore(): void
    {
        // Arrangements
        User::factory()->count(5)->create();
        $user = User::factory()->create();
        $trash = User::factory()->create();
        $trash->delete();

        // Actions
        $response = $this->actingAs($user)->patch(route('users.restore', $trash->id));

        // Assertions
        $response->assertRedirect(route('users.trashed'));
        $response->assertSessionHas('success', 'User has been restored.');
    }

    /**
     * @test
     * @testdox It can permanently delete the user and redirect to users.trashed route with success
     * @return void
     */
    public function testDelete(): void
    {
        // Arrangements
        User::factory()->count(5)->create();
        $user = User::factory()->create();
        $trash = User::factory()->create();
        $trash->delete();

        // Actions
        $response = $this->actingAs($user)->delete(route('users.delete', $trash->id));

        // Assertions
        $response->assertRedirect(route('users.trashed'));
        $response->assertSessionHas('success', 'User has been deleted permanently.');
    }
}
