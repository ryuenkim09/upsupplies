@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Profile</h1>
    <!-- profile update form begins further below; existing images are shown before that -->
    <div class="mb-3 text-center">
            <label>Profile Picture</label>
            <div class="mb-2">
                @if($user->images->isNotEmpty())
                    <img src="{{ Storage::url($user->images->first()->path) }}" width="150" class="rounded-circle" style="border:2px solid #000;">
                @else
                    <img src="https://via.placeholder.com/150?text=No+Image" width="150" class="rounded-circle" style="border:2px solid #000;">
                @endif
            </div>
        </div>
        <div class="mb-3">
            <a href="{{ route('addresses.index') }}" class="btn btn-outline-secondary">
                Manage Saved Addresses
            </a>
        </div>

        <!-- existing additional-images display + delete buttons are rendered *outside* the update form to avoid nested forms -->
        <div class="mb-3">
            <label>Additional Images</label>
            <div class="mb-2">
                @foreach($user->images as $img)
                    <div class="d-inline-block position-relative me-2">
                        <img src="{{ Storage::url($img->path) }}" width="80" class="rounded">
                        <form method="POST" action="{{ route('user.profile.images.destroy',$img) }}" class="position-absolute top-0 end-0">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" style="font-size:.6rem;">&times;</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- now start the main profile-update form -->
        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
            @if(session('files_count') !== null)
                <div class="alert alert-info">Uploaded file count: {{ session('files_count') }}</div>
            @endif
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

            <!-- address and phone are managed via the saved addresses page -->
            <div class="mb-3">
                <em>Your shipping addresses are stored separately. "Manage Saved Addresses" above lets you add/edit/remove them; selections are used during checkout.</em>
            </div>

            <div class="mb-3">
                <label>Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>

            <div class="mb-3">
                <label>Add New Images</label>
                <input type="file" name="images[]" multiple class="form-control" accept="image/png,image/jpeg">
                @error('images')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
                @error('images.*')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <button class="btn btn-primary">Save</button>
        </form>
</div>
@endsection
