@extends('layouts.app')

@section('page-title', 'Info Negara')

@section('content')

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Negara</div>
                    <div class="fs-4 fw-bold">{{ $stats['total'] }}</div>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-globe2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Wilayah</div>
                    <div class="fs-4 fw-bold">5 Wilayah</div>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-map"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Data Real-time</div>
                    <div class="fs-4 fw-bold">✅ Live</div>
                </div>
                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-wifi"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Sumber Data</div>
                    <div class="fs-4 fw-bold">CountriesNow</div>
                </div>
                <div class="icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-database"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Search --}}
<div class="section-card mb-4">
    <div class="section-title">🔍 Cari Negara</div>
    <form method="GET" action="{{ route('country') }}">
        <div class="row g-2">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control"
                    placeholder="Cari nama negara... (contoh: Indonesia, Japan, Brazil)"
                    value="{{ $search }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('country') }}" class="btn btn-secondary w-100">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Country Table --}}
<div class="section-card">
    <div class="section-title">
        🌍 Daftar Negara
        <span class="badge bg-primary ms-2">{{ count($countries) }} negara</span>
    </div>

    @if(count($countries) === 0)
        <div class="text-center py-5 text-muted">
            <i class="bi bi-search fs-1"></i>
            <p class="mt-2">Negara tidak ditemukan</p>
        </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="countryTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Bendera</th>
                    <th>Nama Negara</th>
                    <th>Ibu Kota</th>
                    <th>Mata Uang</th>
                    <th>Kode Dial</th>
                    <th>ISO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($countries as $i => $c)
                <tr>
                    <td class="text-muted small">{{ $i + 1 }}</td>
                    <td>
                        @if($c['flag'])
                            <img src="{{ $c['flag'] }}" alt="{{ $c['name'] }}"
                                style="width:40px; height:25px; object-fit:cover; border-radius:3px; border:1px solid #eee;">
                        @else
                            <span style="font-size:1.5rem;">{{ $c['emoji'] }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="fw-bold">{{ $c['name'] }}</div>
                        <small class="text-muted">{{ $c['emoji'] }}</small>
                    </td>
                    <td>{{ $c['capital'] }}</td>
                    <td><small>{{ $c['currency'] }}</small></td>
                    <td><span class="badge bg-secondary">{{ $c['dial'] }}</span></td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $c['iso2'] }}</span>
                        <span class="badge bg-light text-dark border">{{ $c['iso3'] }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection