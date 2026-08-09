@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah Paket Soal</h3>

    <form method="POST" action="{{ route('admin.packages.store') }}">
        @csrf

        @include('admin.packages.form')

        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
