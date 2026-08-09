@extends('layouts.app')

@section('title', 'Manajemen Paket Soal')

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Manajemen Paket Soal</h4>

            <a href="{{ route('admin.packages.create') }}" class="btn btn-primary btn-sm">
                + Tambah Paket
            </a>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="alert-success">
                {{ session('success') }}

                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>

            <script>
                setTimeout(function() {
                    $('#alert-success').alert('close');
                }, 3000);
            </script>
        @endif


        {{-- Table --}}
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%">
                        <thead class="thead-light align-middle text-center">
                            <tr>
                                <th>#</th>
                                <th>Nama Paket</th>

                                <th>Jumlah Soal</th>
                                <th>Audio Listening</th>
                                <th>Status</th>
                                <th>Aksi</th>
                                <th>Token</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse ($packages as $index => $package)
                                <tr>
                                    <td>{{ $index + 1 }}</td>

                                    <td>
                                        <strong>{{ $package->name }}</strong>
                                        @if ($package->description)
                                            <div class="text-muted small">
                                                {{ $package->description }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        {{ $package->questions_count }}
                                    </td>

                                    <td class="text-center">

                                        @php
                                            $activeAudio = $package->audio_path
                                                ? $package->audios->where('path', $package->audio_path)->first()
                                                : null;
                                        @endphp

                                        @if ($activeAudio)
                                            <div>
                                                {{-- {{ $activeAudio->filename }}</strong> --}}
                                                <audio controls>
                                                    <source src="{{ Storage::url($activeAudio->path) }}" type="audio/mpeg">
                                                    Your browser does not support the audio element.
                                                </audio>
                                                &nbsp;

                                                <a href="{{ route('admin.audios.index', $package) }}"
                                                    class="btn btn-sm btn-info" title="Kelola Audio">
                                                    <i class="fa-solid fa-refresh"></i>
                                                </a>
                                            </div>
                                        @else
                                            <div>
                                                Audio belum tersedia
                                                <a href="{{ route('admin.audios.index', $package) }}"
                                                    class="btn btn-sm btn-info" title="Tambah Audio">
                                                    <i class="fa-solid fa-plus"></i>
                                                </a>
                                            </div>
                                        @endif


                                    </td>

                                    <td class="text-center">
                                        <form action="{{ route('admin.packages.publish', $package) }}" method="POST">
                                            @csrf

                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="publishSwitch{{ $package->id }}" onchange="this.form.submit()"
                                                    {{ $package->status === 'published' ? 'checked' : '' }}>

                                                <label class="custom-control-label" for="publishSwitch{{ $package->id }}">
                                                    {{ $package->status === 'published' ? 'Published' : 'Draft' }}
                                                </label>
                                            </div>
                                        </form>
                                    </td>

                                    <td align="center">
                                        {{-- Soal --}}
                                        <a href="{{ route('admin.questions.index', $package) }}"
                                            class="btn btn-sm btn-info" title="Kelola Soal">
                                            <i class="fa-solid fa-clipboard-question"></i>
                                        </a>
                                        {{-- Instruksi --}}
                                        <a href="{{ route('admin.instructions.index', $package) }}"
                                            class="btn btn-sm btn-secondary" title="Kelola Instruksi">
                                            <i class="fa-solid fa-book"></i></a>
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.packages.edit', $package) }}"
                                            class="btn btn-sm btn-warning" title="Edit Paket Soal">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>

                                        {{-- Hapus --}}
                                        <form action="{{ route('admin.packages.destroy', $package) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus paket soal ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger" title="Hapus Paket Soal">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        @if ($package->token_secret)
                                            <span class="badge badge-success"
                                                style="font-size: 1.2em; letter-spacing: 2px;">
                                                {{ $package->token_secret }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">BELUM ADA</span>
                                        @endif

                                        <form action="{{ route('admin.package.token', $package->id) }}" method="POST"
                                            class="d-inline ml-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary"
                                                title="Generate Token Baru">
                                                <i class="fas fa-sync-alt"></i>
                                                {{ $package->token_secret ? 'Reset' : 'Generate' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Belum ada paket soal
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
