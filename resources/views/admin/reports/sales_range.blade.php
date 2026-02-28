@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Sales by {{ ucfirst($range) }}</h1>
    <ul>
    @foreach($data as $row)
        <li>{{ implode(' / ', (array)$row->toArray()) }}</li>
    @endforeach
    </ul>
</div>
@endsection