@extends('layouts.app')

@section('title', 'Preview Import Soal')

@section('content')
<div class="container-fluid">

    <h4 class="mb-3">
        Preview Import — <strong>{{ $package->name }}</strong>
    </h4>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Tipe</th>
                        <th>Soal</th>
                        <th>Jawaban</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($questions as $q)
                    <tr>
                        <td>{{ $q['number'] }}</td>
                        <td>{{ ucfirst($q['type'] ?? '-') }}</td>
                        <td>
                            <div style="max-width:500px">
                                {!! $q['content_html'] ?? '-' !!}
                            </div>
                        </td>
                        <td>{{ $q['answer_key'] ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>

        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('admin.questions.import', $package) }}"
               class="btn btn-secondary">
                Kembali
            </a>

            <form action="{{ route('admin.questions.import.confirm', $package) }}"
                  method="POST">
                @csrf
                <button class="btn btn-success">
                    Simpan Soal
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
