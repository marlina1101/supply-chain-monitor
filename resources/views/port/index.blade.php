@extends('layouts.app')

@section('page-title', 'Peta Pelabuhan')

@section('content')

{{-- Filter Region --}}
<div class="section-card mb-4">
    <div class="section-title">⚓ Filter Wilayah Pelabuhan</div>
    <div class="d-flex gap-2 flex-wrap">
        @foreach($regions as $r)
            <a href="{{ route('port', ['region' => $r]) }}"
               class="btn {{ $region == $r ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ $r === 'all' ? '🌍 Semua Wilayah' : $r }}
            </a>
        @endforeach
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Pelabuhan</div>
                    <div class="fs-4 fw-bold">{{ $stats['total'] }}</div>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-anchor"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Status Aktif</div>
                    <div class="fs-4 fw-bold text-success">{{ $stats['active'] }}</div>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Status Sibuk</div>
                    <div class="fs-4 fw-bold text-warning">{{ $stats['busy'] }}</div>
                </div>
                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Peta --}}
<div class="section-card mb-4">
    <div class="section-title">🗺️ Peta Pelabuhan Internasional</div>
    <div id="port-map" style="height: 450px; border-radius: 10px;"></div>
    <div class="mt-2 d-flex gap-3 small text-muted">
        <span><span style="color:#2196F3">●</span> Aktif</span>
        <span><span style="color:#FF9800">●</span> Sibuk</span>
        <span><span style="color:#F44336">●</span> Terganggu</span>
    </div>
</div>

{{-- Table --}}
<div class="section-card">
    <div class="section-title">📋 Daftar Pelabuhan</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Pelabuhan</th>
                    <th>Negara</th>
                    <th>Wilayah</th>
                    <th>Volume (Juta TEU)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ports as $i => $port)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td class="fw-bold">{{ $port['name'] }}</td>
                    <td>{{ $port['country'] }}</td>
                    <td><span class="badge bg-secondary">{{ $port['region'] }}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:6px;">
                                <div class="progress-bar bg-primary"
                                     style="width:{{ min(($port['volume']/50)*100, 100) }}%"></div>
                            </div>
                            <span class="fw-bold">{{ $port['volume'] }}</span>
                        </div>
                    </td>
                    <td>
                        @if($port['status'] === 'active')
                            <span class="badge bg-success">✅ Aktif</span>
                        @elseif($port['status'] === 'busy')
                            <span class="badge bg-warning text-dark">⚡ Sibuk</span>
                        @else
                            <span class="badge bg-danger">⚠️ Terganggu</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Inisialisasi peta
const map = L.map('port-map').setView([20, 0], 2);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Data pelabuhan dari PHP
const ports = @json($ports);

// Warna marker berdasarkan status
function getColor(status) {
    if (status === 'active')    return '#2196F3';
    if (status === 'busy')      return '#FF9800';
    return '#F44336';
}

// Tambahkan marker untuk setiap pelabuhan
ports.forEach(port => {
    const color = getColor(port.status);

    const icon = L.divIcon({
        className: '',
        html: `<div style="
            width: 14px; height: 14px;
            background: ${color};
            border: 2px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        "></div>`,
        iconSize: [14, 14],
        iconAnchor: [7, 7],
    });

    L.marker([port.lat, port.lon], { icon })
        .addTo(map)
        .bindPopup(`
            <div style="min-width:180px;">
                <strong>⚓ ${port.name}</strong><br>
                🌍 ${port.country} — ${port.region}<br>
                📦 Volume: <strong>${port.volume} Juta TEU</strong><br>
                Status: <strong style="color:${color}">${port.status.toUpperCase()}</strong>
            </div>
        `);
});
</script>
@endpush