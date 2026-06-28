@extends('layouts.app')

@section('page-title', 'Dashboard Utama')

@section('content')

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Cuaca Dipantau</div>
                    <div class="fs-4 fw-bold">12 Kota</div>
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
                    <div class="fs-4 fw-bold">50+ Negara</div>
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
                    <div class="text-muted small">Pelabuhan Aktif</div>
                    <div class="fs-4 fw-bold">20 Port</div>
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
                    <div class="text-muted small">Berita Terbaru</div>
                    <div class="fs-4 fw-bold">Real-time</div>
                </div>
                <div class="icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-newspaper"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="section-card">
            <div class="section-title">📈 Indikator Risiko Rantai Pasok</div>
            <canvas id="riskChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="section-card">
            <div class="section-title">🔴 Level Risiko Saat Ini</div>
            <canvas id="riskDonut" height="200"></canvas>
        </div>
    </div>
</div>

{{-- Map & News Row --}}
<div class="row g-3">
    <div class="col-md-8">
        <div class="section-card">
            <div class="section-title">🗺️ Peta Monitoring Global</div>
            <div id="world-map" style="height: 300px; border-radius: 8px;"></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="section-card">
            <div class="section-title">⚠️ Ringkasan Risiko</div>
            <div class="list-group list-group-flush">
                <div class="list-group-item px-0 d-flex justify-content-between">
                    <span><i class="bi bi-cloud-lightning text-warning"></i> Cuaca Ekstrem</span>
                    <span class="badge bg-warning">Sedang</span>
                </div>
                <div class="list-group-item px-0 d-flex justify-content-between">
                    <span><i class="bi bi-graph-down text-danger"></i> Inflasi Global</span>
                    <span class="badge bg-danger">Tinggi</span>
                </div>
                <div class="list-group-item px-0 d-flex justify-content-between">
                    <span><i class="bi bi-currency-exchange text-success"></i> Nilai Tukar</span>
                    <span class="badge bg-success">Rendah</span>
                </div>
                <div class="list-group-item px-0 d-flex justify-content-between">
                    <span><i class="bi bi-anchor text-primary"></i> Pelabuhan</span>
                    <span class="badge bg-primary">Normal</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Chart risiko garis
new Chart(document.getElementById('riskChart'), {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
        datasets: [
            {
                label: 'Risiko Cuaca',
                data: [30, 45, 35, 60, 40, 55],
                borderColor: '#42a5f5',
                tension: 0.4, fill: false
            },
            {
                label: 'Risiko Ekonomi',
                data: [50, 55, 65, 70, 60, 75],
                borderColor: '#ef5350',
                tension: 0.4, fill: false
            },
            {
                label: 'Risiko Logistik',
                data: [40, 35, 50, 45, 55, 50],
                borderColor: '#66bb6a',
                tension: 0.4, fill: false
            }
        ]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

// Donut chart
new Chart(document.getElementById('riskDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Rendah', 'Sedang', 'Tinggi'],
        datasets: [{
            data: [40, 35, 25],
            backgroundColor: ['#66bb6a', '#ffa726', '#ef5350']
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

// Peta Leaflet
const map = L.map('world-map').setView([20, 0], 2);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

// Contoh marker pelabuhan
const ports = [
    { name: "Port of Shanghai", lat: 31.2, lng: 121.5 },
    { name: "Port of Singapore", lat: 1.26, lng: 103.8 },
    { name: "Port of Rotterdam", lat: 51.9, lng: 4.4 },
    { name: "Port of Los Angeles", lat: 33.7, lng: -118.2 },
    { name: "Port of Dubai", lat: 25.2, lng: 55.3 },
];
ports.forEach(p => {
    L.marker([p.lat, p.lng]).addTo(map).bindPopup(p.name);
});
</script>
@endpush