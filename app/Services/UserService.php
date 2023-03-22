<?php

namespace App\Services;

use App\Models\Detail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\UserServiceInterface;
use Illuminate\Database\Eloquent\Model;

class UserService implements UserServiceInterface
{
    /**
     * The user instance.
     *
     * @var App\User
     */
    protected $user;

    /**
     * The detail instance.
     *
     * @var App\Detail
     */
    protected $detail;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Constructor to bind user to a repository.
     *
     * @param \App\User                $user
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(User $user, Detail $detail, Request $request)
    {
        $this->user = $user;
        $this->detail = $detail;
        $this->request = $request;
    }

    /**
     * Define the validation rules for the user.
     *
     * @param  int $id
     * @return array
     */
    public function rules($id = null): array
    {
        $rules =  [
            'prefixname' => ['nullable', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'suffixname' => ['nullable', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($id)
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($id)
            ],
            'photo' => ['nullable', 'image'],
        ];

        if ($id === null) {
            $rules['password'] = [
                'required',
                'string',
                'min:8',
                'confirmed'
            ];
        } else {
            $rules['password'] = ['nullable'];
        }

        return $rules;
    }

    /**
     * Retrieve all resources and paginate.
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function list(int $perPage = 1)
    {
        return $this->user->paginate($perPage);
    }

    /**
     * Create user resource.
     *
     * @param  array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $attributes)
    {
        if (isset($attributes['photo']) && $attributes['photo']) {
            $attributes['photo'] = $this->upload($attributes['photo']);
        }

        return $this->user->create($attributes);
    }

    /**
     * Retrieve user resource details.
     * Abort to 404 if not found.
     *
     * @param  integer $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find(int $id): ?Model
    {
        return $this->user->findOrFail($id);
    }

    /**
     * Update user resource.
     *
     * @param  integer $id
     * @param  array   $attributes
     * @return boolean
     */
    public function update(int $id, array $attributes): bool
    {
        $user = $this->find($id);

        foreach ($attributes as $key => $value) {
            if (!$value) {
                unset($attributes[$key]);
            }
        }

        if (isset($attributes['photo']) && $attributes['photo']) {
            $attributes['photo'] = $this->upload($attributes['photo']);
        }

        return $user->update($attributes);
    }

    /**
     * Soft delete user resource.
     *
     * @param  integer $id
     * @return boolean
     */
    public function destroy(int $id): bool
    {
        $user = $this->find($id);
        return $user->delete();
    }

    /**
     * Include only soft deleted records in the results.
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function listTrashed(int $perPage = 1)
    {
        return $this->user->onlyTrashed()->paginate($perPage);
    }

    /**
     * Restore user resource.
     *
     * @param  integer|array $id
     * @return boolean
     */
    public function restore($id): bool
    {
        $trashed = $this->user->onlyTrashed()->findOrFail($id);
        return $trashed->restore();
    }

    /**
     * Permanently delete user resource.
     *
     * @param  integer $id
     * @return void
     */
    public function delete(int $id)
    {
        $trashed = $this->user->onlyTrashed()->findOrFail($id);
        return $trashed->forceDelete();
    }

   /**
     * Create detail resource.
     *
     * @param  array $attributes
     * @return void
     */
    public function storeDetail(array $attributes): void
    {
        $this->detail->insert($attributes);
    }

    /**
     * Generate random hash key.
     *
     * @param  string $key
     * @return string
     */
    public function hash(string $key): string
    {
        return Hash::make($key);
    }

    /**
     * Upload the given file.
     *
     * @param  \Illuminate\Http\UploadedFile $file
     * @return string|null
     */
    public function upload(UploadedFile $file): string
    {
        //get filename with extension
        $originalName = $file->getClientOriginalName();
        //get filename without extension
        $filename = pathinfo($originalName, PATHINFO_FILENAME);
        //get file extension
        $extension = $file->getClientOriginalExtension();
        //filename to store
        $fileNameToStore = $filename . '_' . time() . '.' . $extension;

        $fileNameToStore = preg_replace('/\s+/', '', $fileNameToStore);

        $file->move('img/users', $fileNameToStore);

        return '/img/users/' . $fileNameToStore;
    }
}
