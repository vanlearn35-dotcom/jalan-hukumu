@extends('layouts.app')

@section('title', 'Tambah Soal')

@section('content')
<div class="container-fluid">
    <h4>Tambah Soal — {{ $package->name }}</h4>

    <form action="{{ route('admin.questions.store', $package) }}"
          method="POST"
          enctype="multipart/form-data">

        @include('admin.questions._form')

    </form>
</div>
@endsection
