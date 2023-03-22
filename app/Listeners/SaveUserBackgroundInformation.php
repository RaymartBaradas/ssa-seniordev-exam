<?php

namespace App\Listeners;

use App\Events\UserSaved;
use App\Services\UserService;
use Carbon\Carbon;

class SaveUserBackgroundInformation
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle the event.
     */
    public function handle(UserSaved $event): void
    {
        $user = $event->user;

        $attributes = $this->formatData($user);

        $this->userService->storeDetail($attributes);
    }

    /**
     * @param $user
     *
     * @return array
     */
    private function formatData($user): array
    {
        $date = Carbon::now();

        return  [
            [
                'key' => 'Full name',
                'value' => $user->fullname,
                'user_id' => $user->id,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'key' => 'Middle Initial',
                'value' => $user->middleinitial,
                'user_id' => $user->id,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'key' => 'Avatar',
                'value' => $user->avatar,
                'user_id' => $user->id,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'key' => 'Gender',
                'value' => $user->prefixname,
                'user_id' => $user->id,
                'created_at' => $date,
                'updated_at' => $date,
            ],
        ];
    }
}
