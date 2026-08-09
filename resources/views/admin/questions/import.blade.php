@extends('layouts.app')

@section('title', 'Import Soal')

@section('content')
<div class="container-fluid">

    <h4 class="mb-3">
        Import Soal — <strong>{{ $package->name }}</strong>
    </h4>

    {{-- Alert --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('admin.questions.import.preview', $package) }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="form-group">
                    <label>Format File</label>
                    <select name="format" class="form-control" required>
                        <option value="excel">Excel</option>
                        {{-- <option value="json">JSON</option>
                        <option value="aiken">AIKEN</option> --}}
                        <option value="excel">Excel</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>File Soal</label>
                    <input type="file"
                           name="file"
                           class="form-control"
                           required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.questions.index', $package) }}"
                       class="btn btn-secondary">
                        Kembali
                    </a>

                    <button class="btn btn-primary">
                        Preview Import
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
