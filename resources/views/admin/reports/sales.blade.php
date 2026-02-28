@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Sales Overview</h1>
    <p>Total orders: {{ $count }}</p>
    <p>Total revenue: {{ $total }}</p>
</div>
@endsection