@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/editor.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}">
    <style>
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

        .ck-editor__editable {
            min-height: 250px;
        }

        .instruction-preview-text {
            max-height: 60px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Instruksi: {{ $package->name }}</h1>
            <div>
                <button class="btn btn-sm btn-success shadow-sm" onclick="$('#importModal').modal('show')">
                    <i class="fas fa-file-excel fa-sm text-white-50"></i> Import Excel
                </button>
                <a href="{{ route('admin.packages') }}" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
                </a>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary" id="form-title">Tambah Instruksi Baru</h6>
            </div>
            <div class="card-body">
                <div id="fake-input" class="form-control text-muted" style="cursor: pointer; background: #f8f9fc;">
                    <i class="fas fa-plus-circle mr-2"></i> Klik untuk input instruksi secara manual...
                </div>

                <form action="{{ route('admin.instructions.store', $package->id) }}" method="POST" id="main-form"
                    class="d-none mt-2">
                    @csrf
                    <input type="hidden" name="id" id="field-id">

                    <div class="row bg-light p-3 rounded mb-4 border">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Section</label>
                            <select name="section" id="field-section" class="form-control border-primary" required>
                                <option value="listening">Listening</option>
                                <option value="structure">Structure</option>
                                <option value="reading">Reading</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Part (A/B/C - Opsional)</label>
                            <select name="part" id="field-part" class="form-control">
                                <option value="">- Tanpa Part -</option>
                                <option value="A">Part A</option>
                                <option value="B">Part B</option>
                                <option value="C">Part C</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-4 p-3 border rounded shadow-sm">
                        <label class="font-weight-bold text-uppercase text-muted small">Konten Instruksi (HTML)</label>
                        <textarea class="ck-editor-5" data-name="instruction" name="content_html" id="field-content"></textarea>
                    </div>

                    <div class="d-flex justify-content-end border-top pt-3">
                        <button type="button" class="btn btn-secondary mr-2" onclick="resetForm()">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 shadow">
                            <i class="fas fa-save mr-1"></i> Simpan Instruksi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Instruksi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>Section</th>
                                <th>Part</th>
                                <th>Konten</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($instructions as $ins)
                                <tr>
                                    <td><span class="badge badge-{{ $ins->section }}">{{ strtoupper($ins->section) }}</span>
                                    </td>
                                    <td class="text-center font-weight-bold">{{ $ins->part ?? '-' }}</td>
                                    <td>
                                        {{-- Tampilan di tabel: Bersih dari tag HTML agar enak dibaca --}}
                                        <div class="instruction-preview-text text-muted small">
                                            {{ Str::limit(strip_tags($ins->content_html), 100) }}
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        {{-- Tombol Edit --}}
                                        <button type="button" class="btn btn-warning btn-circle btn-sm btn-edit-trigger"
                                            data-id="{{ $ins->id }}" data-section="{{ $ins->section }}"
                                            data-part="{{ $ins->part }}" data-content="{{ e($ins->content_html) }}">
                                            {{-- Gunakan e() di sini --}}
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{-- Tombol Preview --}}
                                        <button type="button" class="btn btn-info btn-circle btn-sm btn-preview-trigger"
                                            data-content="{{ e($ins->content_html) }}" {{-- Gunakan e() di sini --}}
                                            data-title="{{ strtoupper($ins->section) }} {{ $ins->part }}">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        {{-- Form Delete tetap seperti sebelumnya --}}


                                        <button type="button" class="btn btn-danger btn-circle btn-sm btn-delete-trigger"
                                            data-id="{{ $ins->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Preview --}}
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Preview Instruksi: <span id="preview-title"></span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body bg-light">
                    <div id="preview-body" class="p-3 border bg-white rounded shadow-sm" style="min-height: 200px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Modal (Sama seperti sebelumnya) --}}
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/super-build/ckeditor.js"></script>
    <script src="{{ asset('js/editor.js') }}"></script>
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/instructions/instruction.js') }}"></script>
@endpush
