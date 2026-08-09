@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Admin Dashboard</h1>
    </div>

    <div class="row">

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Total Pengguna
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $total_users ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Paket Ujian
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $total_packages ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Sesi Ujian
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $total_sessions ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
