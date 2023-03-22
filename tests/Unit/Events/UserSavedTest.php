<?php

namespace Tests\Unit\Events;

use App\Events\UserSaved;
use App\Listeners\SaveUserBackgroundInformation;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserSavedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @testdox it calls save user background information listener with correct parameters
     * @return void
     */
    public function testUserSaved()
    {
        // Arrangements
        Event::fake();
        $user = User::factory()->create();
        $listener = new SaveUserBackgroundInformation(app(UserService::class));

        // Actions
        UserSaved::dispatch($user);
        $listener->handle(new UserSaved($user));

        // Assertions
        Event::assertDispatched(UserSaved::class);
        $this->assertDatabaseHas('details', [
            'key' => 'Full name',
            'value' => $user->fullname,
            'user_id' => $user->id
        ]);
    }
}
