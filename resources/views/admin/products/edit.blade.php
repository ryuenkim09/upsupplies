@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Edit Product</h1>
    <form action="{{ route('admin.products.update',$product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name',$product->name) }}">
        </div>
        <div class="mb-3">
            <label>Category</label>
            <select name="category_id" class="form-control">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $cat->id == $product->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Price</label>
            <input type="text" name="price" class="form-control" value="{{ old('price',$product->price) }}">
        </div>
        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" class="form-control" value="{{ old('stock',$product->stock) }}">
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ old('description',$product->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label>Image</label>
            <input type="file" name="image" class="form-control">
        </div>
        <div class="mb-3">
            <label>Existing Images</label>
            <div class="d-flex flex-wrap gap-2 mb-2">
                @foreach($product->images as $img)
                    <div style="width:120px;" class="text-center">
                        <img src="{{ \Illuminate\Support\Str::startsWith($img->path,'http') ? $img->path : asset('storage/'.$img->path) }}" class="img-fluid mb-1" alt="" />
                        <form method="POST" action="{{ route('admin.products.images.destroy', ['product' => $product->id, 'image' => $img->id]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Remove</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="mb-3">
            <label>Add More Images</label>
            <input type="file" name="images[]" class="form-control" multiple>
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection