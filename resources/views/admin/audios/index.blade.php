@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table_audio_player.css') }}">
@endpush
@section('title', 'Manajemen Audio Listening')

@section('content')
    <div class="container-fluid">

        <h1 class="h3 mb-4 text-gray-800">🎵 Manajemen Audio Listening - {{ $package->name }}</h1>

        <div class="card shadow mb-4">
            <div class="card-header">
                <strong>Upload Audio (Native)</strong>
            </div>
            <div class="card-body">

                <form id="audio-upload-form" action="{{ route('admin.audios.store', $package) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <input type="file" name="audio" id="audio-input" accept="audio/*" class="form-control mb-2"
                        required>

                    <button type="submit" id="btn-upload-audio" class="btn btn-primary">
                        Upload Audio
                    </button>
                </form>
                <div id="upload-progress-box" class="mt-3 d-none">
                    <div class="progress">
                        <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                            role="progressbar" style="width: 0%">
                            0%
                        </div>
                    </div>
                </div>

            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-music mr-2"></i> Audio Player

                </h6>
            </div>
            <div class="card-body text-center">
                {{-- Judul Audio --}}
                <h5 id="audio-title" class="mb-3">
                    {{ $package->activeAudio ? $package->activeAudio->filename : 'Tidak ada audio aktif' }}
                </h5>
                <span id="player-status" class="ml-2 badge badge-warning d-none">
                    <i class="fas fa-spinner fa-spin mr-1"></i> Buffering...
                </span>
                <div class="px-3">
                    <input type="range" id="audio-progress" class="custom-range w-100" min="0" max="100"
                        step="0.1" value="0">
                </div>

                <div class="text-muted my-2">
                    <span id="current-time" class="font-weight-bold">00:00</span>
                    <span class="mx-1">/</span>
                    <span id="total-time">00:00</span>
                </div>

                <div class="btn-group shadow-sm" role="group">
                    <button type="button" class="btn btn-light border" id="btn-back" title="-5 detik">
                        <i class="fa fa-backward text-secondary"></i>
                    </button>

                    <button type="button" class="btn btn-light border px-4" id="btn-play">
                        <i id="play-icon" class="fa fa-play text-primary"></i>

                    </button>

                    <button type="button" class="btn btn-light border" id="btn-stop" title="Stop">
                        <i class="fa fa-stop text-danger"></i>
                    </button>

                    <button type="button" class="btn btn-light border" id="btn-forward" title="+5 detik">
                        <i class="fa fa-forward text-secondary"></i>
                    </button>
                </div>

                <div id="active-audio-source"
                    data-src="{{ $package->activeAudio ? asset('storage/' . $package->activeAudio->path) : '' }}">
                </div>

            </div>
        </div>

        {{-- =======================
    | DAFTAR AUDIO
    ======================= --}}
        <div class="card shadow">
            <div class="card-body">

                <h5 class="mb-3">📂 Daftar Audio</h5>

                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="70">Versi</th>
                            <th>Nama File</th>
                            <th width="120">Status</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($package->audios as $audio)
                            <tr>
                                <td class="text-center">v{{ $audio->version }}</td>

                                <td>{{ $audio->filename }}</td>

                                <td>
                                    @if ($audio->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>

                                <td class="text-center">

                                    {{-- PLAY VIA GLOBAL PLAYER --}}
                                    <button class="btn btn-primary btn-sm btn-play-table"
                                        data-url="{{ asset('storage/' . $audio->path) }}" data-title="{{ $audio->title }}">
                                        <i class="fas fa-play mr-1"></i>
                                    </button>

                                    {{-- SET AKTIF --}}
                                    @if (!$audio->is_active)
                                        <form method="POST" action="{{ route('admin.audios.select', [$package, $audio]) }}"
                                            class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- DELETE (NONAKTIF SAJA) --}}
                                    @if (!$audio->is_active)
                                        <form method="POST"
                                            action="{{ route('admin.audios.destroy', [$package, $audio]) }}"
                                            class="d-inline" onsubmit="return confirm('Hapus audio ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    Belum ada audio
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

    </div>


@endsection


@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.4/howler.min.js"></script>
    <script src="{{ asset('js/audio_player.js') }}"></script>
    <script src="{{ asset('js/alert/audio.js') }}"></script>
    <script>
        window.AUDIO_UPLOAD_URL = "{{ route('admin.audios.store', $package) }}";
    </script>
    <script src="{{ asset('js/audio_upload.js') }}"></script>
    {{-- <script src="{{ asset('js/lfm_audio_select.js') }}"></script> --}}
@endpush
