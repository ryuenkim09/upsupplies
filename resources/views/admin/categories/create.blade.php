@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Add Category</h1>
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection