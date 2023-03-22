<?php

namespace App\Http\Controllers;

use App\Events\UserSaved;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
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
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = $this->userService->list(5);

        return view('users.index', compact(['users']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $attributes = $request->validated();

        $user = $this->userService->store($attributes);

        UserSaved::dispatch($user);

        return redirect()
            ->route('users.index')
            ->with('success', 'User has been created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $user = $this->userService->find($id);

        return view('users.show', compact(['user']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, int $id)
    {
        $attributes = $request->only(User::FILLABLE);

        $this->userService->update($id, $attributes);

        return redirect()
            ->route('users.index')
            ->with('success', 'User has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->userService->destroy($id);

        return redirect()
            ->route('users.index')
            ->with('success', 'User has been deleted.');
    }

    /**
     * Display a listing of the trashed resource.
     */
    public function trashed()
    {
        $users = $this->userService->listTrashed(5);

        return view('users.deleted.index', compact(['users']));
    }

    /**
     * Restore the specified trashed resource from storage.
     */
    public function restore(int $id)
    {
        $this->userService->restore($id);

        return redirect()
            ->route('users.trashed')
            ->with('success', 'User has been restored.');
    }

    /**
     * Delete permanently the specified trashed resource from storage.
     */
    public function delete(int $id)
    {
        $this->userService->delete($id);

        return redirect()
            ->route('users.trashed')
            ->with('success', 'User has been deleted permanently.');
    }
}
