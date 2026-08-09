@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Paket Soal</h3>

    <form method="POST" action="{{ route('admin.packages.update', $package) }}">
        @csrf
        @method('PUT')

        @include('admin.packages.form', ['package' => $package])

        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
