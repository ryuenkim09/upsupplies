@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Add Product</h1>
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
        </div>
        <div class="mb-3">
            <label>Category</label>
            <select name="category_id" class="form-control">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Price</label>
            <input type="text" name="price" class="form-control" value="{{ old('price') }}">
        </div>
        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" class="form-control" value="{{ old('stock') }}">
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label>Image</label>
            <input type="file" name="image" class="form-control">
            <small class="form-text text-muted">You can also upload additional images below.</small>
        </div>
        <div class="mb-3">
            <label>Additional Images</label>
            <input type="file" name="images[]" class="form-control" multiple>
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection