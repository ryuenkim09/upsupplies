@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Users</h1>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email" class="form-control" />
        </div>
        <div class="col-auto">
            <select name="active" class="form-select">
                <option value="">Any status</option>
                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-auto form-check align-self-center">
            <input type="checkbox" class="form-check-input" id="trashedCheck" name="trashed" value="1" {{ request('trashed') ? 'checked' : '' }}>
            <label class="form-check-label" for="trashedCheck">Show deleted</label>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>

    <table class="table">
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Active</th><th>Last Login</th><th>Registered</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach($users as $u)
            <tr class="{{ $u->trashed() ? 'table-danger' : '' }}">
                <td>{{ $u->id }}</td>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->role }}</td>
                <td>{{ $u->active ? 'Yes' : 'No' }}</td>
                <td>{{ $u->last_login ? $u->last_login->format('Y-m-d H:i') : '-' }}</td>
                <td>{{ $u->created_at->format('Y-m-d') }}</td>
                <td>
                    @if($u->trashed())
                        <form method="POST" action="{{ route('admin.dashboard.users.restore',$u) }}" style="display:inline">
                            @csrf
                            <button class="btn btn-sm btn-success">Restore</button>
                        </form>
                    @else
                        <a href="{{ route('admin.users.edit',$u) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <form method="POST" action="{{ route('admin.users.destroy',$u) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete user?')">Delete</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
@endsection