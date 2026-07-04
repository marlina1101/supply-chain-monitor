@extends('layouts.app')
@section('page-title', 'Global Country Dashboard')
@section('content')

{{-- Country Selector --}}
<div class="section-card mb-4">
    <div class="section-title">🌍 Pilih Negara</div>
    <form method="GET" action="{{ route('globalcountry') }}">
        <div class="row g-2">
            <div class="col-md-5">
                <select name="country" class="form-select form-select-lg" onchange="this.form.submit()">
                    @foreach($countries as $code => $c)
                        <option value="{{ $code }}" {{ $selected == $code ? 'selected' : '' }}>
                            {{ $c['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Info Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto mb-2">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="text-muted small">GDP</div>
            <div class="fs-5 fw-bold">
                @if($data['gdp'])
                    ${{ number_format($data['gdp']/1e12, 2) }} T
                @else N/A @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="icon-box bg-danger bg-opacity-10 text-danger mx-auto mb-2">
                <i class="bi bi-percent"></i>
            </div>
            <div class="text-muted small">Inflasi</div>
            <div class="fs-5 fw-bold">
                @if($data['inflation'] !== null)
                    {{ number_format($data['inflation'], 2) }}%
                @else N/A @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="icon-box bg-success bg-opacity-10 text-success mx-auto mb-2">
                <i class="bi bi-people"></i>
            </div>
            <div class="text-muted small">Populasi</div>
            <div class="fs-5 fw-bold">
                @if($data['population'])
                    {{ number_format($data['population']/1e6, 1) }} Juta
                @else N/A @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto mb-2">
                <i class="bi bi-currency-exchange"></i>
            </div>
            <div class="text-muted small">Kurs vs USD</div>
            <div class="fs-5 fw-bold">
                @if($data['exchangeRate'])
                    {{ number_format($data['exchangeRate'], 2) }} {{ $data['currency'] }}
                @else N/A @endif
            </div>
        </div>
    </div>
</div>

{{-- Cuaca & Risk Score --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="section-card text-center h-100">
            <div class="section-title">🌤️ Cuaca Saat Ini</div>
            <div style="font-size: 4rem;">{{ $data['icon'] }}</div>
            <div class="fs-2 fw-bold text-primary">
                {{ $data['temp'] !== null ? $data['temp'].'°C' : 'N/A' }}
            </div>
            <div class="text-muted">{{ $data['condition'] }}</div>
            <div class="mt-2 small text-muted">
                💨 Angin: {{ $data['windSpeed'] }} km/h
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="section-card text-center h-100">
            <div class="section-title">⚡ Risk Score</div>
            <div class="display-4 fw-bold mt-3"
                 style="color:{{ $data['riskLevel']['color'] ?? '#666' }}">
                {{ $data['riskScore'] ?? 'N/A' }}
            </div>
            <div class="text-muted mb-2">/ 100</div>
            @if($data['riskLevel'])
                <span class="badge fs-6 bg-{{ $data['riskLevel']['class'] }} px-4 py-2">
                    {{ $data['riskLevel']['level'] }}
                </span>
            @endif
            <div class="mt-3">
                <a href="{{ route('risk', ['country' => $selected]) }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-radar"></i> Detail Risk Score
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="section-card h-100">
            <div class="section-title">📊 Ringkasan Negara</div>
            <table class="table table-sm table-borderless">
                <tr>
                    <td class="text-muted small">Negara</td>
                    <td class="fw-bold">{{ $country['name'] }}</td>
                </tr>
                <tr>
                    <td class="text-muted small">Mata Uang</td>
                    <td class="fw-bold">{{ $data['currency'] }}</td>
                </tr>
                <tr>
                    <td class="text-muted small">GDP</td>
                    <td class="fw-bold">
                        @if($data['gdp']) ${{ number_format($data['gdp']/1e12,2) }}T
                        @else N/A @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-muted small">Inflasi</td>
                    <td class="fw-bold">
                        @if($data['inflation'] !== null) {{ number_format($data['inflation'],2) }}%
                        @else N/A @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-muted small">Populasi</td>
                    <td class="fw-bold">
                        @if($data['population']) {{ number_format($data['population']/1e6,1) }} Juta
                        @else N/A @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-muted small">Cuaca</td>
                    <td class="fw-bold">{{ $data['icon'] }} {{ $data['temp'] }}°C</td>
                </tr>
                <tr>
                    <td class="text-muted small">Risk Level</td>
                    <td>
                        @if($data['riskLevel'])
                        <span class="badge bg-{{ $data['riskLevel']['class'] }}">
                            {{ $data['riskLevel']['level'] }}
                        </span>
                        @endif
                    </td>
                </tr>
            </table>
            <div class="mt-2 d-flex gap-2">
                <a href="{{ route('compare', ['country1' => $selected]) }}"
                   class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-arrow-left-right"></i> Compare
                </a>
                <a href="{{ route('watchlist.add') }}" class="btn btn-sm btn-outline-warning w-100">
                    <i class="bi bi-star"></i> Watchlist
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Grafik GDP Trend (Data Ilustratif) --}}
<div class="row g-3">
    <div class="col-md-6">
        <div class="section-card">
            <div class="section-title">📈 GDP Trend (Ilustrasi)</div>
            <canvas id="gdpTrendChart" height="200"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="section-card">
            <div class="section-title">📉 Inflation Trend (Ilustrasi)</div>
            <canvas id="inflationTrendChart" height="200"></canvas>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// GDP Trend Chart
new Chart(document.getElementById('gdpTrendChart'), {
    type: 'line',
    data: {
        labels: ['2019', '2020', '2021', '2022', '2023', '2024'],
        datasets: [{
            label: 'GDP Trend {{ $country["name"] }}',
            data: [100, 92, 98, 105, 108, {{ $data['gdp'] ? round($data['gdp']/1e11) : 110 }}],
            borderColor: '#2196F3',
            backgroundColor: 'rgba(33,150,243,0.1)',
            tension: 0.4, fill: true,
        }]
    },
    options: {
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: false } }
    }
});

// Inflation Trend Chart
new Chart(document.getElementById('inflationTrendChart'), {
    type: 'line',
    data: {
        labels: ['2019', '2020', '2021', '2022', '2023', '2024'],
        datasets: [{
            label: 'Inflation Trend {{ $country["name"] }}',
            data: [2.1, 1.5, 3.2, 6.8, 4.5, {{ $data['inflation'] ?? 3.0 }}],
            borderColor: '#ef5350',
            backgroundColor: 'rgba(239,83,80,0.1)',
            tension: 0.4, fill: true,
        }]
    },
    options: {
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endpush