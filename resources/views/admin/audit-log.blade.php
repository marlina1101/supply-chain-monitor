@extends('admin.layouts.app')
@section('page-title', 'Audit Log')
@section('content')

<div class="section-card">
    <div class="section-title">📋 Riwayat Aktivitas Sistem</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Aksi</th>
                    <th>Deskripsi</th>
                    <th>IP Address</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $i => $log)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td><span class="badge bg-primary">{{ $log->action }}</span></td>
                    <td class="small">{{ $log->description }}</td>
                    <td class="small text-muted">{{ $log->ip_address }}</td>
                    <td class="small text-muted">
                        {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i:s') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $logs->links() }}
</div>

@endsection