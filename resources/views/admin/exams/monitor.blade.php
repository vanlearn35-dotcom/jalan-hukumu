@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Live Monitoring: {{ $package->name }}</h1>
        <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <div class="card mb-4 border-left-danger shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Token Aktif Saat Ini</div>
                    <div class="h2 mb-0 font-weight-bold text-gray-800">{{ $package->current_token }}</div>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary btn-sm" onclick="fetchData()">Refresh Manual</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Status Peserta</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="monitorTable" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nama Peserta</th>
                            <th>Status Koneksi</th>
                            <th>Section</th>
                            <th>No. Soal</th>
                            <th>Sisa Waktu</th>
                            <th>Status Ujian</th>
                        </tr>
                    </thead>
                    <tbody id="live-data">
                        <tr><td colspan="6" class="text-center">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function fetchData() {
        $.ajax({
            url: "{{ url('/admin/exams/' . $package->id . '/live-data') }}",
            type: "GET",
            success: function(response) {
                let html = '';
                if(response.data.length === 0) {
                    html = '<tr><td colspan="6" class="text-center">Belum ada peserta yang memulai ujian.</td></tr>';
                } else {
                    response.data.forEach(user => {
                        let statusBadge = user.is_online 
                            ? '<span class="badge badge-success">Online</span>' 
                            : '<span class="badge badge-secondary">Offline ('+user.last_seen+')</span>';
                        
                        let examStatus = user.status === 'completed' 
                            ? '<span class="badge badge-primary">Selesai</span>'
                            : '<span class="badge badge-warning">Sedang Mengerjakan</span>';

                        html += `
                            <tr>
                                <td>
                                    <strong>${user.user_name}</strong><br>
                                    <small class="text-muted">${user.user_email}</small>
                                </td>
                                <td class="text-center">${statusBadge}</td>
                                <td class="text-capitalize">${user.section}</td>
                                <td class="text-center font-weight-bold">${user.current_q}</td>
                                <td class="text-center text-danger font-weight-bold">${user.remaining_time}</td>
                                <td class="text-center">${examStatus}</td>
                            </tr>
                        `;
                    });
                }
                $('#live-data').html(html);
            }
        });
    }

    // Auto Refresh setiap 5 detik
    setInterval(fetchData, 5000);
    // Load pertama kali
    fetchData();
</script>
@endsection