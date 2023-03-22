@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <a class="btn btn-info mb-2" href="{{route('users.index')}}">Back</a>

            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        {{ __('Deleted Users') }}
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
                                        <div class='d-flex align-items-center'>
                                            <form action="{{ route('users.restore', $user->id) }}" method="Post">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" style="outline: none; border: none; background: none; display: inline;" class="text-success">Restore</button>
                                            </form>


                                            <form action="{{ route('users.delete', $user->id) }}" method="Post">

                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="outline: none; border: none; background: none; display: inline;" class="text-danger">Delete Permanently</button>
                                            </form>
                                        </div>
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
