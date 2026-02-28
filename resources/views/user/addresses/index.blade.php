@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-3">
        <a href="{{ route('user.profile.edit') }}" class="btn btn-outline-secondary">&larr; Back to Profile</a>
    </div>
    <h1>Saved Addresses</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <h5>Add New Address</h5>
        <form method="POST" action="{{ route('addresses.store') }}">
            @csrf
            <div class="mb-2">
                <label for="label" class="form-label">Label (optional)</label>
                <input type="text" name="label" id="label" class="form-control" value="{{ old('label') }}">
            </div>
            <div class="mb-2">
                <label for="address" class="form-label">Address</label>
                <textarea name="address" id="address" rows="3" class="form-control" required>{{ old('address') }}</textarea>
                @error('address')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="mb-2">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <button class="btn btn-primary">Save Address</button>
        </form>
    </div>

    <h5>Your Addresses</h5>
    @if($addresses->isEmpty())
        <p>You have not saved any addresses yet.</p>
    @else
        <ul class="list-group">
            @foreach($addresses as $addr)
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div>
                        <strong>{{ $addr->label ?: 'Address '.$addr->id }}</strong><br>
                        {{ $addr->address }}@if($addr->phone)<br>Phone: {{ $addr->phone }}@endif
                    </div>
                    <form method="POST" action="{{ route('addresses.destroy', $addr) }}" onsubmit="return confirm('Delete this address?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
