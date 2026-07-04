@extends('layouts.app')

@section('page-title', 'Admin Dashboard')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Pelabuhan (DB)</div>
                    <div class="fs-4 fw-bold">{{ $totalPorts }}</div>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-anchor"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Artikel</div>
                    <div class="fs-4 fw-bold">{{ $totalArticles }}</div>
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
                    <div class="text-muted small">Total Watchlist Aktif</div>
                    <div class="fs-4 fw-bold">{{ $totalWatchlist }}</div>
                </div>
                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-star"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">News Cache</div>
                    <div class="fs-4 fw-bold">{{ $totalNewsCache }}</div>
                </div>
                <div class="icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-newspaper"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" id="adminTabs">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-articles">
            📝 Kelola Artikel
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-ports">
            ⚓ Kelola Pelabuhan
        </button>
    </li>

    <ul class="nav nav-tabs mb-4" id="adminTabs">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-articles">
            📝 Kelola Artikel
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-ports">
            ⚓ Kelola Pelabuhan
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-users">
            👥 Kelola User
        </button>
    </li>
</ul>

</ul>

<div class="tab-content">

    {{-- TAB ARTIKEL --}}
    <div class="tab-pane fade show active" id="tab-articles">
        <div class="row g-3">
            <div class="col-md-5">
                <div class="section-card">
                    <div class="section-title">➕ Tambah Artikel Baru</div>
                    <form method="POST" action="{{ route('admin.article.store') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Judul Artikel</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Kategori</label>
                            <select name="category" class="form-select">
                                <option value="economy">Ekonomi</option>
                                <option value="logistics">Logistik</option>
                                <option value="geopolitics">Geopolitik</option>
                                <option value="risk">Analisis Risiko</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Penulis</label>
                            <input type="text" name="author" class="form-control" value="Admin">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Konten</label>
                            <textarea name="content" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save"></i> Simpan Artikel
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-md-7">
                <div class="section-card">
                    <div class="section-title">📋 Artikel Terbaru</div>
                    @if(count($recentArticles) === 0)
                        <div class="text-center py-4 text-muted">Belum ada artikel</div>
                    @else
                    <div class="list-group list-group-flush">
                        @foreach($recentArticles as $article)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold">{{ $article->title }}</div>
                                    <span class="badge bg-secondary">{{ ucfirst($article->category) }}</span>
                                    <small class="text-muted ms-2">oleh {{ $article->author }}</small>
                                    <p class="small text-muted mt-1 mb-0">{{ Str::limit($article->content, 100) }}</p>
                                </div>
                                <form method="POST" action="{{ route('admin.article.delete', $article->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Hapus artikel ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- TAB USER --}}
<div class="tab-pane fade" id="tab-users">
    <div class="section-card">
        <div class="section-title">👥 Daftar User Terdaftar</div>
        @if(count($users) === 0)
            <div class="text-center py-4 text-muted">
                <i class="bi bi-people fs-1"></i>
                <p class="mt-2">Belum ada user terdaftar</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Terdaftar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $i => $user)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="fw-bold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}
                            </small>
                        </td>
                        <td><span class="badge bg-success">Aktif</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Form tambah user --}}
    <div class="section-card mt-3">
        <div class="section-title">➕ Tambah User Baru</div>
        <form method="POST" action="{{ route('admin.user.store') }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control"
                        placeholder="Nama lengkap" required>
                </div>
                <div class="col-md-4">
                    <input type="email" name="email" class="form-control"
                        placeholder="Email" required>
                </div>
                <div class="col-md-3">
                    <input type="password" name="password" class="form-control"
                        placeholder="Password" required>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

    {{-- TAB PELABUHAN --}}
    <div class="tab-pane fade" id="tab-ports">
        <div class="row g-3">
            <div class="col-md-5">
                <div class="section-card">
                    <div class="section-title">➕ Tambah Pelabuhan Baru</div>
                    <form method="POST" action="{{ route('admin.port.store') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Nama Pelabuhan</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Negara</label>
                            <input type="text" name="country" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Wilayah</label>
                            <select name="region" class="form-select">
                                <option value="Asia">Asia</option>
                                <option value="Europe">Europe</option>
                                <option value="Americas">Americas</option>
                                <option value="Middle East">Middle East</option>
                                <option value="Africa">Africa</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Latitude</label>
                                <input type="number" step="0.0001" name="latitude" class="form-control" required>
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Longitude</label>
                                <input type="number" step="0.0001" name="longitude" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Volume (Juta TEU)</label>
                            <input type="number" step="0.1" name="volume" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Aktif</option>
                                <option value="busy">Sibuk</option>
                                <option value="disrupted">Terganggu</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save"></i> Simpan Pelabuhan
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-md-7">
                <div class="section-card">
                    <div class="section-title">📋 Pelabuhan di Database</div>
                    @if(count($ports) === 0)
                        <div class="text-center py-4 text-muted">Belum ada data pelabuhan di database (data utama masih statis di PortController)</div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Negara</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ports as $port)
                                <tr>
                                    <td>{{ $port->name }}</td>
                                    <td>{{ $port->country }}</td>
                                    <td><span class="badge bg-secondary">{{ $port->status }}</span></td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.port.delete', $port->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Hapus?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

@endsection