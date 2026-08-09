@extends('layouts.admin')

@section('title', 'Edit Soal')

@section('content')
<div class="container-fluid">
    <h4>Edit Soal — {{ $package->name }}</h4>

    <form action="{{ route('admin.questions.update', [$package, $question]) }}"
          method="POST"
          enctype="multipart/form-data">

        @include('admin.questions._form', ['question' => $question])

    </form>
</div>
@endsection
