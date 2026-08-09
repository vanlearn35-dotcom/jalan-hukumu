@extends('layouts.app')

@section('title', 'Debug Upload Audio')

@section('content')
<div class="container mt-4">
    <h4>Debug Upload Audio — {{ $package->name ?? '' }}</h4>
    <div class="card shadow p-3 mt-3">
        @foreach ($debug as $key => $value)
            <h5>{{ $key }}</h5>
            @if(is_array($value))
                <pre>{{ print_r($value, true) }}</pre>
            @else
                <p>{{ $value }}</p>
            @endif
            <hr>
        @endforeach
    </div>
    <a href="{{ route('admin.audios.index', $package->id) }}" class="btn btn-primary mt-3">Kembali</a>
</div>
@endsection
