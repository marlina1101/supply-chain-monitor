@extends('layouts.app')

@section('page-title', 'Cuaca Global')

@section('content')

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Kota Dipantau</div>
                    <div class="fs-4 fw-bold">{{ count($weatherData) }} Kota</div>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-geo-alt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Risiko Tinggi</div>
                    <div class="fs-4 fw-bold text-danger">
                        {{ count(array_filter($weatherData, fn($w) => $w['risk'] === 'high')) }} Kota
                    </div>
                </div>
                <div class="icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Kondisi Normal</div>
                    <div class="fs-4 fw-bold text-success">
                        {{ count(array_filter($weatherData, fn($w) => $w['risk'] === 'low')) }} Kota
                    </div>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Weather Cards --}}
<div class="section-card mb-4">
    <div class="section-title">🌤️ Kondisi Cuaca Kota-Kota Strategis</div>
    <div class="row g-3">
        @foreach($weatherData as $w)
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100
                {{ $w['risk'] === 'high' ? 'border-danger border' :
                   ($w['risk'] === 'medium' ? 'border-warning border' : '') }}">
                <div class="card-body text-center">
                    <div style="font-size: 2.5rem;">{{ $w['icon'] }}</div>
                    <h6 class="fw-bold mb-0">{{ $w['city'] }}</h6>
                    <small class="text-muted">{{ $w['country'] }}</small>
                    <div class="fs-3 fw-bold my-2">
                        {{ $w['temp'] !== 'N/A' ? $w['temp'].'°C' : 'N/A' }}
                    </div>
                    <div class="text-muted small mb-2">{{ $w['condition'] }}</div>
                    <div class="row text-center g-1 small">
                        <div class="col-4">
                            <div class="text-muted">💧 Humid</div>
                            <div class="fw-bold">{{ $w['humidity'] }}%</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted">💨 Angin</div>
                            <div class="fw-bold">{{ $w['wind'] }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted">🌧️ Hujan</div>
                            <div class="fw-bold">{{ $w['rain'] }}</div>
                        </div>
                    </div>
                    <div class="mt-2">
                        @if($w['risk'] === 'high')
                            <span class="badge bg-danger">⚠️ Risiko Tinggi</span>
                        @elseif($w['risk'] === 'medium')
                            <span class="badge bg-warning text-dark">⚡ Risiko Sedang</span>
                        @else
                            <span class="badge bg-success">✅ Normal</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Chart Temperatur --}}
<div class="row g-3">
    <div class="col-md-6">
        <div class="section-card">
            <div class="section-title">🌡️ Perbandingan Temperatur</div>
            <canvas id="tempChart" height="200"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="section-card">
            <div class="section-title">💨 Kecepatan Angin (km/h)</div>
            <canvas id="windChart" height="200"></canvas>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const cities = @json(array_column($weatherData, 'city'));
const temps  = @json(array_column($weatherData, 'temp'));
const winds  = @json(array_column($weatherData, 'wind'));

// Chart Temperatur
new Chart(document.getElementById('tempChart'), {
    type: 'bar',
    data: {
        labels: cities,
        datasets: [{
            label: 'Temperatur (°C)',
            data: temps,
            backgroundColor: temps.map(t => t > 35 ? '#ef5350' : t > 25 ? '#ffa726' : '#42a5f5'),
            borderRadius: 6,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: false } }
    }
});

// Chart Angin
new Chart(document.getElementById('windChart'), {
    type: 'bar',
    data: {
        labels: cities,
        datasets: [{
            label: 'Kecepatan Angin (km/h)',
            data: winds,
            backgroundColor: winds.map(w => w > 50 ? '#ef5350' : w > 30 ? '#ffa726' : '#66bb6a'),
            borderRadius: 6,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endpush