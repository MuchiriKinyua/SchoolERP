<!-- resources/views/role-permission/user/index.blade.php -->
@extends('layouts.app-web-layout')

@section('title', 'Roles')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit User</h4>
                    <a href="{{ url('users') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ url('users/'.$user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="">Name</label>
                            <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
                            @error('name') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="">Email</label>
                            <input type="email" name="email" readonly value="{{ $user->email }}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="">Password</label>
                            <input type="password" name="password" class="form-control">
                            @error('password') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="">Roles</label>
                            <select name="roles[]" class="form-control" multiple>
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                    <option 
                                        value="{{ $role }}"
                                        {{in_array($role, $userRoles) ? 'selected':''}}
                                        >
                                        {{ $role }}
                                        </option>
                                @endforeach
                            </select>
                            @error('roles') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
