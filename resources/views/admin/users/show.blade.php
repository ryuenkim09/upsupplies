@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>User: {{ $user->name }}</h2>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0">User Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Role:</strong> <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span></p>
                <p><strong>Member Since:</strong> {{ date('M d, Y', strtotime($user->created_at)) }}</p>
                <p><strong>Last Login:</strong> 
                    @if($user->last_login)
                        {{ date('M d, Y g:i A', strtotime($user->last_login)) }}
                    @else
                        <small class="text-muted">Never</small>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0">Update User Status</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="active" class="form-label">Status</label>
                        <select class="form-control" id="active" name="active" required>
                            <option value="1" {{ $user->active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$user->active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Order History</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>₱{{ number_format($order->total, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'info') }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td>{{ date('M d, Y', strtotime($order->created_at)) }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3">No orders</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
