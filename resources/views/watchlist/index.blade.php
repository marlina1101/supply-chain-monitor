@extends('layouts.app')

@section('page-title', 'My Watchlist')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show">
        <i class="bi bi-info-circle"></i> {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Add Country --}}
<div class="section-card mb-4">
    <div class="section-title">⭐ Tambah Negara ke Watchlist</div>
    <form method="POST" action="{{ route('watchlist.add') }}">
        @csrf
        <div class="row g-2">
            <div class="col-md-8">
                <select name="country_code" class="form-select" required>
                    <option value="">-- Pilih Negara --</option>
                    @foreach($countries as $code => $name)
                        <option value="{{ $code }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-plus-circle"></i> Tambah ke Watchlist
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Negara Dipantau</div>
                    <div class="fs-4 fw-bold">{{ count($watchlistWithRisk) }}</div>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-star-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Status</div>
                    <div class="fs-6 fw-bold">Tersimpan per Sesi</div>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-shield-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Update</div>
                    <div class="fs-6 fw-bold text-success">Real-time</div>
                </div>
                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Watchlist Cards --}}
<div class="section-card">
    <div class="section-title">📋 Daftar Negara Favorit</div>

    @if(count($watchlistWithRisk) === 0)
        <div class="text-center py-5 text-muted">
            <i class="bi bi-star fs-1"></i>
            <p class="mt-2">Belum ada negara di watchlist. Tambahkan negara untuk mulai memantau!</p>
        </div>
    @else
    <div class="row g-3">
        @foreach($watchlistWithRisk as $item)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-bold fs-5">{{ $item['country_name'] }}</div>
                            <small class="text-muted">{{ $item['country_code'] }}</small>
                        </div>
                        <form method="POST" action="{{ route('watchlist.remove', $item['id']) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Hapus dari watchlist?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    <div class="text-muted small mb-3">
                        <i class="bi bi-clock"></i> Ditambahkan {{ \Carbon\Carbon::parse($item['added_at'])->diffForHumans() }}
                    </div>
                    <a href="{{ route('risk', ['country' => $item['country_code']]) }}"
                       class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-radar"></i> Lihat Risk Score
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@endsection