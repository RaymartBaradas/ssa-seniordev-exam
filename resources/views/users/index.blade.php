@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        {{ __('Users') }}
                        <div>
                            <a class="btn btn-danger" href="{{route('users.trashed')}}">View Trashed</a>
                            <a class="btn btn-success" href="{{route('users.create')}}">Add New</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success" role="alert">
                            {{ $message }}
                        </div>
                    @endif

                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">Photo</th>
                            <th scope="col">Fullname</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td><img src="{{$user->avatar}}" alt="User" class="img-thumbnail" style="height: 50px;"></td>
                                    <td>{{$user->prefixname}} {{$user->fullname}}</td>
                                    <td>{{$user->username}}</td>
                                    <td>{{$user->email}}</td>
                                    <td>

                                        <form action="{{ route('users.destroy', $user->id) }}" method="Post">
                                            <a href="{{route('users.show', $user->id)}}"><i class="fas fa-list text-info me-2"></i></a>
                                            <a href="{{ route('users.edit', $user->id) }}"><i class="fas fa-edit text-success"></i></a>

                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="outline: none; border: none; background: none; display: inline;"><i class="fas fa-trash text-danger"></i></button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if (!count($users))
                        <div class="text-center">No data available</div>
                    @endif
                    {!! $users->links() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
