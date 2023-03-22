<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class UserServiceTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    /** @var UserService */
    protected $userService;

    /** @var User */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = app(UserService::class);
    }

    /**
     * @test
     * @testdox It can return a paginated list of users
     * @return void
     */
    public function testList()
    {
        // Arrangements
        User::factory()->count(10)->create();

        // Actions
        $result = $this->userService->list(5);

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(10, $result->total());
        $this->assertEquals(5, $result->perPage());
        $this->assertEquals(2, $result->lastPage());
    }

    /**
     * @test
     * @testdox It can store a user to database
     * @return void
     */
    public function testStore()
    {
        // Arrangements
        $attributes = [
            'prefixname' => 'Mr',
            'firstname' => fake()->firstName(),
            'middlename' => 'A',
            'lastname' => fake()->lastName(),
            'suffixname' => 'Jr',
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'photo' => null,
            'type' => 'user',
            'remember_token' => Str::random(10),
        ];

        // Actions
        $result = $this->userService->store($attributes);

        // Assertions
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($attributes['email'], $result->email);
    }

    /**
     * @test
     * @testdox It can find and return an existing user
     * @return void
     */
    public function testFind()
    {
        // Arrangements
        $user = User::factory()->create();

        // Actions
        $result = $this->userService->find($user->id);

        // Assertions
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    /**
     * @test
     * @testdox It can update an existing user
     * @return void
     */
    public function testUpdate()
    {
        // Arrangements
        $user = User::factory()->create();
        $attributes = [
            'email' => 'jane.doe@example.com',
        ];

        // Actions
        $result = $this->userService->update($user->id, $attributes);

        // Assertions
        $this->assertTrue($result);
        $this->assertEquals($attributes['email'], $user->fresh()->email);
    }

    /**
     * @test
     * @testdox It can soft delete an existing user
     * @return void
     */
    public function testDestroy()
    {
        // Arrangements
        $user = User::factory()->create();

        // Actions
        $result = $this->userService->destroy($user->id);

        // Assertions
        $this->assertTrue($result);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /**
     * @test
     * @testdox It can return a paginated list of trashed users
     * @return void
     */
    public function testListTrashed()
    {
        // Arrangements
        $user = User::factory()->create();
        $user->delete();

        // Actions
        $result = $this->userService->listTrashed(5);

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(1, $result->total());
        $this->assertEquals(5, $result->perPage());
        $this->assertEquals(1, $result->lastPage());
    }

    /**
     * @test
     * @testdox It can restore a soft deleted user
     * @return void
     */
    public function testRestore()
    {
        // Arrangements
        $user = User::factory()->create();
        $user->delete();

        // Actions
        $result = $this->userService->restore($user->id);

        // Assertions
        $this->assertTrue($result);
        $this->assertInstanceOf(User::class, $this->userService->find($user->id));
    }

    /**
     * @test
     * @testdox it can permanently delete a soft deleted user
     * @return void
     */
    public function testDelete()
    {
        // Arrangements
        $user = User::factory()->create();
        $user->delete();

        // Actions
        $result = $this->userService->delete($user->id);

        // Assertions
        $this->assertTrue($result);
        $this->assertNull(User::withTrashed()->find($user->id));
    }

    /**
     * @test
     * @testdox It can upload photo
     * @return void
     */
    public function testUpload()
    {
        // Arrangements
        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.jpg');

        // Actions
        $path = $this->userService->upload($file);

        // Assertions
        $this->assertNotEmpty($path);
        $this->assertStringContainsString('/img/users/', $path);
    }
}
