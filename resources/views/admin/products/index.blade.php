@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Products</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Add Product</a>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <table class="table">
        <thead><tr><th></th><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach($products as $p)
            <tr>
                <td style="width:80px;">
                    @php $thumb = $p->images->first()?->path ?? $p->image; @endphp
                    @if($thumb)
                        <img src="{{ \Illuminate\Support\Str::startsWith($thumb,'http') ? $thumb : asset('storage/'.$thumb) }}" class="img-fluid" alt="" style="max-width:70px;">
                    @endif
                </td>
                <td>{{ $p->id }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category?->name }}</td>
                <td>{{ $p->price }}</td>
                <td>{{ $p->stock }}</td>
                <td>
                    <a href="{{ route('admin.products.edit',$p) }}" class="btn btn-sm btn-secondary">Edit</a>
                    <form action="{{ route('admin.products.destroy',$p) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{-- use Bootstrap pagination so next/previous arrows are visible --}}
    {{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
@endsection