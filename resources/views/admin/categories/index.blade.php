@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">Add Category</a>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <table class="table">
        <thead><tr><th>ID</th><th>Name</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach($categories as $cat)
            <tr>
                <td>{{ $cat->id }}</td>
                <td>{{ $cat->name }}</td>
                <td>
                    <a href="{{ route('admin.categories.edit',$cat) }}" class="btn btn-sm btn-secondary">Edit</a>
                    <form action="{{ route('admin.categories.destroy',$cat) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $categories->links() }}
</div>
@endsection