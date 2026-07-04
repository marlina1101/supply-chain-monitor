@extends('layouts.app')

@section('page-title', 'Dashboard Utama')

@section('content')

{{-- Global Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Kota Cuaca Dipantau</div>
                    <div class="fs-4 fw-bold">{{ $globalStats['weather'] }} Kota</div>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-cloud-sun"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Negara Dipantau</div>
                    <div class="fs-4 fw-bold">{{ $globalStats['countries'] }}+</div>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-globe2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Pelabuhan Dipantau</div>
                    <div class="fs-4 fw-bold">{{ $globalStats['ports'] }} Port</div>
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
                    <div class="text-muted small">Update Berita</div>
                    <div class="fs-4 fw-bold text-success">{{ $globalStats['news'] }}</div>
                </div>
                <div class="icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-newspaper"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cuaca & Nilai Tukar --}}
<div class="row g-3 mb-4">

    {{-- Cuaca --}}
    <div class="col-md-6">
        <div class="section-card h-100">
            <div class="section-title">
                🌤️ Cuaca Kota Strategis
                <a href="{{ route('weather') }}" class="btn btn-sm btn-outline-primary float-end">
                    Lihat Semua
                </a>
            </div>
            <div class="row g-2">
                @forelse($weatherSummary as $w)
                <div class="col-6">
                    <div class="card border-0 bg-light p-2 text-center">
                        <div style="font-size:1.8rem;">{{ $w['icon'] }}</div>
                        <div class="fw-bold small">{{ $w['city'] }}</div>
                        <div class="fs-5 fw-bold text-primary">{{ $w['temp'] }}°C</div>
                    </div>
                </div>
                @empty
                <div class="text-muted text-center py-3">Data tidak tersedia</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Nilai Tukar --}}
    <div class="col-md-6">
        <div class="section-card h-100">
            <div class="section-title">
                💱 Nilai Tukar (Base: USD)
                <a href="{{ route('currency') }}" class="btn btn-sm btn-outline-primary float-end">
                    Lihat Semua
                </a>
            </div>
            @if(count($currencySummary) > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mata Uang</th>
                            <th class="text-end">Kurs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currencySummary as $code => $rate)
                        <tr>
                            <td class="fw-bold">{{ $code }}</td>
                            <td class="text-end text-success fw-bold">
                                {{ number_format($rate, 4) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div class="text-muted text-center py-3">Data tidak tersedia</div>
            @endif
        </div>
    </div>

</div>

{{-- Peta & Risiko --}}
<div class="row g-3 mb-4">

    {{-- Peta --}}
    <div class="col-md-8">
        <div class="section-card h-100">
            <div class="section-title">
                🗺️ Peta Pelabuhan Global
                <a href="{{ route('port') }}" class="btn btn-sm btn-outline-primary float-end">
                    Lihat Semua
                </a>
            </div>
            <div id="dashboard-map" style="height: 300px; border-radius: 8px;"></div>
        </div>
    </div>

    {{-- Ringkasan Risiko --}}
    <div class="col-md-4">
        <div class="section-card h-100">
            <div class="section-title">⚠️ Status Pelabuhan Utama</div>
            <div class="list-group list-group-flush">
                @foreach($portSummary as $port)
                <div class="list-group-item px-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold small">{{ $port['name'] }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">
                                {{ $port['country'] }} · {{ $port['volume'] }}M TEU
                            </div>
                        </div>
                        @if($port['status'] === 'active')
                            <span class="badge bg-success">Aktif</span>
                        @elseif($port['status'] === 'busy')
                            <span class="badge bg-warning text-dark">Sibuk</span>
                        @else
                            <span class="badge bg-danger">Gangguan</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- Grafik Risiko --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="section-card">
            <div class="section-title">📈 Indikator Risiko Rantai Pasok Global</div>
            <canvas id="riskChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="section-card">
            <div class="section-title">🔴 Distribusi Level Risiko</div>
            <canvas id="riskDonut" height="220"></canvas>
        </div>
    </div>
</div>
{{-- Currency & Risk Trend --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="section-card">
            <div class="section-title">💱 Currency Trend — IDR/USD (6 Bulan)</div>
            <canvas id="currencyTrendChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="section-card">
            <div class="section-title">📊 Risk Trend Global (6 Bulan)</div>
            <canvas id="riskTrendChart" height="120"></canvas>
        </div>
    </div>
</div>

{{-- Berita Terbaru --}}
<div class="section-card">
    <div class="section-title">
        📰 Berita Global Terbaru
        <a href="{{ route('news') }}" class="btn btn-sm btn-outline-primary float-end">
            Lihat Semua
        </a>
    </div>
    @if(count($newsSummary) > 0)
    <div class="row g-3">
        @foreach($newsSummary as $article)
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                @if(!empty($article['image']))
                    <img src="{{ $article['image'] }}" class="card-img-top"
                         style="height:120px; object-fit:cover;"
                         onerror="this.style.display='none'">
                @endif
                <div class="card-body p-2">
                    <span class="badge bg-primary mb-1" style="font-size:0.65rem;">
                        {{ $article['source']['name'] ?? 'News' }}
                    </span>
                    <p class="small fw-bold mb-1" style="font-size:0.8rem; line-height:1.3;">
                        {{ Str::limit($article['title'], 80) }}
                    </p>
                    <a href="{{ $article['url'] }}" target="_blank"
                       class="btn btn-sm btn-outline-secondary w-100" style="font-size:0.75rem;">
                        Baca
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
        <div class="text-muted text-center py-3">
            <i class="bi bi-newspaper"></i> Berita tidak tersedia
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// ===== PETA =====
const map = L.map('dashboard-map').setView([20, 0], 2);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

const ports = @json($portSummary);
const coords = {
    'Port of Shanghai':  [31.2304, 121.4737],
    'Port of Singapore': [1.2644, 103.8229],
    'Port of Rotterdam': [51.9225, 4.4792],
    'Port of Dubai':     [25.0657, 55.1713],
    'Port of Alexandria':[31.2001, 29.9187],
};

ports.forEach(port => {
    const color = port.status === 'active' ? '#2196F3' :
                  port.status === 'busy'   ? '#FF9800' : '#F44336';
    const latlon = coords[port.name];
    if (latlon) {
        const icon = L.divIcon({
            className: '',
            html: `<div style="width:12px;height:12px;background:${color};border:2px solid white;border-radius:50%;box-shadow:0 2px 4px rgba(0,0,0,0.3);"></div>`,
            iconSize: [12, 12], iconAnchor: [6, 6]
        });
        L.marker(latlon, { icon }).addTo(map)
            .bindPopup(`<strong>⚓ ${port.name}</strong><br>${port.country}<br>Volume: ${port.volume}M TEU`);
    }
}); // ← penutup forEach

// ===== CHART RISIKO GARIS =====
new Chart(document.getElementById('riskChart'), {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
        datasets: [
            {
                label: 'Risiko Cuaca',
                data: [30, 45, 35, 60, 40, 55],
                borderColor: '#42a5f5',
                backgroundColor: 'rgba(66,165,245,0.1)',
                tension: 0.4, fill: true
            },
            {
                label: 'Risiko Ekonomi',
                data: [50, 55, 65, 70, 60, 75],
                borderColor: '#ef5350',
                backgroundColor: 'rgba(239,83,80,0.1)',
                tension: 0.4, fill: true
            },
            {
                label: 'Risiko Logistik',
                data: [40, 35, 50, 45, 55, 50],
                borderColor: '#66bb6a',
                backgroundColor: 'rgba(102,187,106,0.1)',
                tension: 0.4, fill: true
            }
        ]
    },
    options: {
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true, max: 100 } }
    }
});

// ===== DONUT CHART =====
new Chart(document.getElementById('riskDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Risiko Rendah', 'Risiko Sedang', 'Risiko Tinggi'],
        datasets: [{
            data: [45, 35, 20],
            backgroundColor: ['#66bb6a', '#ffa726', '#ef5350'],
            borderWidth: 2
        }]
    },
    options: {
        plugins: { legend: { position: 'bottom' } },
        cutout: '65%'
    }
});

// ===== CURRENCY TREND =====
new Chart(document.getElementById('currencyTrendChart'), {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
        datasets: [
            {
                label: 'IDR/USD',
                data: [15800, 15950, 16200, 16500, 16800, 17885],
                borderColor: '#4caf50',
                backgroundColor: 'rgba(76,175,80,0.1)',
                tension: 0.4, fill: true, yAxisID: 'y1'
            },
            {
                label: 'JPY/USD',
                data: [130, 135, 140, 148, 152, 161],
                borderColor: '#ff5722',
                backgroundColor: 'rgba(255,87,34,0.1)',
                tension: 0.4, fill: false, yAxisID: 'y2'
            }
        ]
    },
    options: {
        plugins: { legend: { position: 'bottom' } },
        scales: {
            y1: { type: 'linear', position: 'left', beginAtZero: false },
            y2: { type: 'linear', position: 'right', beginAtZero: false, grid: { drawOnChartArea: false } }
        }
    }
});

// ===== RISK TREND =====
new Chart(document.getElementById('riskTrendChart'), {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
        datasets: [
            {
                label: 'Global Risk Index',
                data: [42, 45, 38, 52, 48, 43],
                borderColor: '#9c27b0',
                backgroundColor: 'rgba(156,39,176,0.1)',
                tension: 0.4, fill: true,
            },
            {
                label: 'Geopolitical Risk',
                data: [55, 60, 48, 65, 58, 52],
                borderColor: '#f44336',
                tension: 0.4, fill: false,
            }
        ]
    },
    options: {
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true, max: 100 } }
    }
});
</script>
@endpush