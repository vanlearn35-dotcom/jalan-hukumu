@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <h1 class="h3 mb-4">Paket Ujian Tersedia</h1>

        <div class="row">
            @forelse($packages as $package)
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <h5 class="card-title">{{ $package->title }}</h5>

                            <p class="text-muted">
                                {{ Str::limit($package->description, 100) }}
                            </p>

                            <p>
                                <strong>Total Soal:</strong>
                                {{ $package->total_questions }}
                            </p>

                            {{-- <a href="{{ route('exam.packages.show', $package) }}" class="btn btn-primary btn-sm">
                                Lihat Detail
                            </a> --}}

                            <a href="{{ route('admin.exam.preview', $package) }}" class="btn btn-primary btn-sm">
                                Preview Exam
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        Belum ada paket ujian tersedia.
                    </div>
                </div>
            @endforelse
        </div>

    </div>
@endsection
