@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Edit User</h1>
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name',$user->name) }}">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email',$user->email) }}">
        </div>
        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-select">
                <option value="user" {{ $user->role=='user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ $user->role=='admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
        <div class="mb-3 form-check">
            <input type="hidden" name="active" value="0">
            <input type="checkbox" name="active" value="1" class="form-check-input" id="activeCheck" {{ $user->active ? 'checked' : '' }}>
            <label for="activeCheck" class="form-check-label">Active</label>
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
