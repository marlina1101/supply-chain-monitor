@extends('admin.layouts.app')
@section('page-title', 'Dashboard Admin')
@section('content')

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total User</div>
                    <div class="fs-3 fw-bold text-primary">{{ $stats['total_users'] }}</div>
                    <div class="small text-muted">
                        {{ $stats['admin_count'] }} Admin · {{ $stats['user_count'] }} User
                    </div>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Artikel</div>
                    <div class="fs-3 fw-bold text-success">{{ $stats['total_articles'] }}</div>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-file-text"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Pelabuhan di DB</div>
                    <div class="fs-3 fw-bold text-warning">{{ $stats['total_ports'] }}</div>
                </div>
                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-anchor"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Watchlist</div>
                    <div class="fs-3 fw-bold text-danger">{{ $stats['total_watchlist'] }}</div>
                </div>
                <div class="icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-star"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Users & Logs --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="section-card">
            <div class="section-title">
                👥 User Terbaru
                <a href="{{ route('admin.users') }}"
                   class="btn btn-sm btn-outline-primary float-end">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentUsers as $u)
                        <tr>
                            <td class="fw-bold">{{ $u->name }}</td>
                            <td class="small text-muted">{{ $u->email }}</td>
                            <td>
                                <span class="badge {{ $u->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                                    {{ ucfirst($u->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ ($u->is_active ?? true) ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ($u->is_active ?? true) ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="section-card">
            <div class="section-title">
                📋 Aktivitas Terbaru
                <a href="{{ route('admin.audit.log') }}"
                   class="btn btn-sm btn-outline-primary float-end">Lihat Semua</a>
            </div>
            @if(count($recentLogs) === 0)
                <div class="text-muted text-center py-3">Belum ada aktivitas</div>
            @else
            <div class="list-group list-group-flush">
                @foreach($recentLogs as $log)
                <div class="list-group-item px-0 py-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="badge bg-primary me-1">{{ $log->action }}</span>
                            <small class="text-muted">{{ $log->description }}</small>
                        </div>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                        </small>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Recent Articles --}}
<div class="section-card">
    <div class="section-title">
        📰 Artikel Terbaru
        <a href="{{ route('admin.articles') }}"
           class="btn btn-sm btn-outline-primary float-end">Lihat Semua</a>
    </div>
    @if(count($recentArticles) === 0)
        <div class="text-muted text-center py-3">Belum ada artikel</div>
    @else
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead class="table-light">
                <tr><th>Judul</th><th>Kategori</th><th>Penulis</th><th>Tanggal</th></tr>
            </thead>
            <tbody>
                @foreach($recentArticles as $a)
                <tr>
                    <td class="fw-bold">{{ Str::limit($a->title, 50) }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($a->category) }}</span></td>
                    <td>{{ $a->author }}</td>
                    <td class="small text-muted">
                        {{ \Carbon\Carbon::parse($a->created_at)->format('d M Y') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection