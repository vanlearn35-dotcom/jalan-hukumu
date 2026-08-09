@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/editor.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}">
    <style>
        /* Warna Badge Custom */
        .badge-listening {
            background-color: #f6c23e;
            color: white;
        }

        .badge-structure {
            background-color: #1cc88a;
            color: white;
        }

        .badge-reading {
            background-color: #36b9cc;
            color: white;
        }

        .badge-passage {
            background-color: rgb(134, 214, 214);
            color: white;
        }

        .ck-editor__editable {
            min-height: 200px;
        }

        .passage-content {
            max-height: 300px;
            overflow-y: auto;
            padding: 15px;
            background: #fdfdfd;
            border: 1px inset #eee;
            font-family: 'Georgia', serif;
        }

        /* Mengatur agar isi Swal bisa di-scroll dan terlihat seperti aplikasi profesional */
        .my-swal-preview .swal2-html-container {
            margin: 1em 0 0 !important;
            padding: 0 !important;
            overflow-x: hidden !important;
        }

        /* Memperhalus scrollbar di dalam preview */
        .col-lg-7::-webkit-scrollbar,
        .col-lg-5::-webkit-scrollbar {
            width: 6px;
        }

        .col-lg-7::-webkit-scrollbar-thumb,
        .col-lg-5::-webkit-scrollbar-thumb {
            background-color: #d1d3e2;
            border-radius: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Soal TOEFL: {{ $package->name }}</h1>
            <div>
                <button class="btn btn-sm btn-success shadow-sm" onclick="$('#importModal').modal('show')">
                    <i class="fas fa-file-excel fa-sm text-white-50"></i> Import Excel
                </button>
                <a href="{{ route('admin.packages') }}" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary" id="form-title">Tambah Soal Baru</h6>
            </div>
            <div class="card-body">
                <div id="fake-input" class="form-control text-muted" style="cursor: pointer; background: #f8f9fc;">
                    <i class="fas fa-plus-circle mr-2"></i> Klik untuk input soal secara manual...
                </div>

                <form action="{{ route('admin.questions.store', $package->id) }}" method="POST" id="main-form"
                    class="d-none mt-2">
                    @csrf
                    <input type="hidden" name="id" id="field-id">

                    <div class="row bg-light p-3 rounded mb-4 border">
                        <div class="col-md-12 mb-2">
                            <label class="text-uppercase font-weight-bold text-muted form-section-title">Informasi
                                Dasar</label>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Section</label>
                            <select name="section" id="field-section" class="form-control border-primary" required
                                onchange="toggleSectionFields()">
                                <option value="listening">Listening</option>
                                <option value="structure">Structure</option>
                                <option value="reading">Reading</option>
                            </select>
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="font-weight-bold">Nomor</label>
                            <input type="number" name="number" id="field-number" class="form-control" required>
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="font-weight-bold">Part (A/B/C)</label>
                            <select name="part" id="field-part" class="form-control">
                                <option value="">- Tanpa Part -</option>
                                <option value="A">Part A</option>
                                <option value="B">Part B</option>
                                <option value="C">Part C</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Tipe Soal</label>
                            <select name="type" id="field-type" class="form-control">
                                <option value="mc">Multiple Choice</option>
                                <option value="error">Error Recognition (Structure Only)</option>
                                <option value="instruction">Instructions</option>
                                <option value="error">Talk Placeholder</option>
                            </select>
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="font-weight-bold">Kunci</label>
                            <select name="answer_key" id="field-answer" class="form-control bg-primary text-white" required>
                                @foreach (['A', 'B', 'C', 'D','Instruction'] as $k)
                                    <option value="{{ $k }}">{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="listening-fields" class="d-none border-left-listening bg-light p-3 rounded mb-4 shadow-sm">
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label class="text-warning font-weight-bold text-uppercase form-section-title"><i
                                        class="fas fa-headphones mr-1"></i> Listening Audio Cues</label>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Cue Start (Detik)</label>
                                <input type="number" name="cue_start" id="field-cue-start" class="form-control"
                                    placeholder="Contoh: 120">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Cue End (Detik)</label>
                                <input type="number" name="cue_end" id="field-cue-end" class="form-control"
                                    placeholder="Contoh: 145">
                            </div>
                        </div>
                    </div>

                    <div id="reading-fields" class="d-none border-left-reading bg-light p-3 rounded mb-4 shadow-sm">
                        <div class="row">
                            <div class="col-md-12 mb-2 d-flex justify-content-between">
                                <label class="text-info font-weight-bold text-uppercase form-section-title"><i
                                        class="fas fa-book-open mr-1"></i> Reading Passage Content</label>
                                <small class="text-muted">Kosongkan jika ingin menggunakan passage dari nomor
                                    sebelumnya</small>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Passage Group / Title</label>
                                <input type="text" name="passage_group" id="field-passage-group" class="form-control"
                                    placeholder="Contoh: 1">
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Teks Bacaan (HTML)</label>
                                <textarea class="ck-editor-5" data-name="passage" name="passage_html"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4 p-3 border rounded shadow-sm">
                        <label class="font-weight-bold text-uppercase text-muted form-section-title"><i
                                class="fas fa-pen mr-1"></i> Pertanyaan / Instruksi Soal</label>
                        <textarea class="ck-editor-5" data-name="question" name="content_html"></textarea>
                    </div>

                    <div class="row bg-light p-3 rounded mb-4 border shadow-sm">
                        <div class="col-md-12 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted form-section-title">Pilihan Jawaban
                                (Options)</label>
                        </div>
                        @foreach (['A', 'B', 'C', 'D'] as $opt)
                            <div class="col-md-6 form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span
                                            class="input-group-text bg-dark text-white font-weight-bold">{{ $opt }}</span>
                                    </div>
                                    <input type="text" name="options[{{ $opt }}]"
                                        id="opt-{{ $opt }}" class="form-control" required
                                        placeholder="Jawaban pilihan {{ $opt }}">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end border-top pt-3">
                        <button type="button" class="btn btn-secondary mr-2" onclick="resetForm()">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 shadow">
                            <i class="fas fa-save mr-1"></i> Simpan Data Soal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Section</th>
                                <th>Detail</th>
                                <th>Kunci</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($questions as $q)
                                <tr>
                                    <td>{{ $q->number }}</td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $q->section }}">{{ strtoupper($q->section) }}</span>
                                        &nbsp;
                                        @if ($q->passage_group)
                                            <span class="badge badge-passage">{{ $q->passage_group }}</span>
                                        @endif
                                    </td>
                                    <td>{!! Str::limit(strip_tags($q->content_html), 50) !!}</td>
                                    <td>{{ $q->answer_key }}</td>
                                    <td>
                                        <button type="button"
                                            class="btn btn-info btn-circle btn-sm shadow-sm btn-preview-swal"
                                            data-id="{{ $q->id }}" data-package="{{ $package->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <button class="btn btn-sm btn-warning"
                                            onclick='editQuestion(@json($q))'>
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button type="button" class="btn btn-danger btn-circle btn-sm btn-delete-trigger"
                                            data-id="{{ $q->id }}" data-package="{{ $package->id }}"
                                            data-number="{{ $q->number }}">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        <form id="delete-form-{{ $q->id }}"
                                            action="{{ route('admin.questions.destroy', [$package->id, $q->id]) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/questions/question.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/super-build/ckeditor.js"></script>
    <script src="{{ asset('js/editor.js') }}"></script>
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                // Mengurutkan berdasarkan kolom ke-2 (index 1 = Section)
                // 'asc' untuk urutan A-Z (Listening -> Reading -> Structure)
                "order": [
                    [1, "asc"]
                ],

                // Opsional: Jika ingin mengurutkan Section dulu baru kemudian Nomor Soal
                // "order": [[ 1, "asc" ], [ 0, "asc" ]], 

                "columnDefs": [{
                        "orderable": false,
                        "targets": 4
                    } // Mematikan fitur sortir pada kolom 'Aksi'
                ]
            });
        });
    </script>
@endpush
