@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Edit Category</h1>
    <form action="{{ route('admin.categories.update',$category) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name',$category->name) }}">
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection