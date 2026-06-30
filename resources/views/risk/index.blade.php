@extends('layouts.app')

@section('page-title', 'Risk Scoring Engine')

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

{{-- Country Selector --}}
<div class="section-card mb-4">
    <div class="section-title">🎯 Pilih Negara untuk Analisis Risiko</div>
    <form method="GET" action="{{ route('risk') }}">
        <div class="row g-2">
            <div class="col-md-4">
                <select name="country" class="form-select" onchange="this.form.submit()">
                    @foreach($countries as $code => $c)
                        <option value="{{ $code }}" {{ $selected == $code ? 'selected' : '' }}>
                            {{ $c['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-radar"></i> Analisis
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Risk Score Utama --}}
<div class="row g-3 mb-4">

    {{-- Gauge Chart --}}
    <div class="col-md-4">
        <div class="section-card h-100 text-center">
            <div class="section-title">⚡ Total Risk Score</div>
            <div class="gauge-wrap my-3">
                <canvas id="gaugeChart" width="250" height="150"></canvas>
                <div style="margin-top:-30px;">
                    <div class="fs-1 fw-bold" style="color:{{ $riskLevel['color'] }}">
                        {{ $totalRisk }}
                    </div>
                    <div class="fs-6 fw-bold text-muted">/ 100</div>
                </div>
            </div>
            <div class="mt-2">
                <span class="badge fs-6 px-4 py-2 bg-{{ $riskLevel['class'] }}">
                    {{ $riskLevel['level'] }}
                </span>
            </div>
            <div class="mt-3 text-muted small">
                Negara: <strong>{{ $country['name'] }}</strong>
            </div>

            {{-- Tombol Tambah Watchlist --}}
            <form method="POST" action="{{ route('watchlist.add') }}" class="mt-3">
                @csrf
                <input type="hidden" name="country_code" value="{{ $selected }}">
                <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-star"></i> Tambah ke Watchlist
                </button>
            </form>
        </div>
    </div>

    {{-- Breakdown Risiko --}}
    <div class="col-md-8">
        <div class="section-card h-100">
            <div class="section-title">📊 Breakdown Komponen Risiko</div>

            {{-- Weather Risk --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-bold">🌤️ Weather Risk <small class="text-muted">(bobot 30%)</small></span>
                    <span class="fw-bold" style="color:{{ $weatherRisk > 65 ? '#b71c1c' : ($weatherRisk > 35 ? '#e65100' : '#2e7d32') }}">
                        {{ round($weatherRisk) }}/100
                    </span>
                </div>
                <div class="progress" style="height:12px; border-radius:6px;">
                    <div class="progress-bar {{ $weatherRisk > 65 ? 'bg-danger' : ($weatherRisk > 35 ? 'bg-warning' : 'bg-success') }}"
                         style="width:{{ $weatherRisk }}%; border-radius:6px;"></div>
                </div>
                <small class="text-muted">Temp: {{ $temp }}°C | Angin: {{ $windSpeed }} km/h</small>
            </div>

            {{-- Inflation Risk --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-bold">📈 Inflation Risk <small class="text-muted">(bobot 20%)</small></span>
                    <span class="fw-bold" style="color:{{ $inflationRisk > 65 ? '#b71c1c' : ($inflationRisk > 35 ? '#e65100' : '#2e7d32') }}">
                        {{ round($inflationRisk) }}/100
                    </span>
                </div>
                <div class="progress" style="height:12px; border-radius:6px;">
                    <div class="progress-bar {{ $inflationRisk > 65 ? 'bg-danger' : ($inflationRisk > 35 ? 'bg-warning' : 'bg-success') }}"
                         style="width:{{ $inflationRisk }}%; border-radius:6px;"></div>
                </div>
                <small class="text-muted">
                    Inflasi: {{ $inflationRate !== null ? number_format($inflationRate, 2).'%' : 'N/A' }}
                </small>
            </div>

            {{-- Currency Risk --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-bold">💱 Currency Risk <small class="text-muted">(bobot 10%)</small></span>
                    <span class="fw-bold" style="color:{{ $currencyRisk > 65 ? '#b71c1c' : ($currencyRisk > 35 ? '#e65100' : '#2e7d32') }}">
                        {{ round($currencyRisk) }}/100
                    </span>
                </div>
                <div class="progress" style="height:12px; border-radius:6px;">
                    <div class="progress-bar {{ $currencyRisk > 65 ? 'bg-danger' : ($currencyRisk > 35 ? 'bg-warning' : 'bg-success') }}"
                         style="width:{{ $currencyRisk }}%; border-radius:6px;"></div>
                </div>
                <small class="text-muted">Exchange Rate: {{ number_format($exchangeRate, 4) }}</small>
            </div>

            {{-- News Risk --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-bold">📰 News Sentiment Risk <small class="text-muted">(bobot 40%)</small></span>
                    <span class="fw-bold" style="color:{{ $newsRisk > 65 ? '#b71c1c' : ($newsRisk > 35 ? '#e65100' : '#2e7d32') }}">
                        {{ round($newsRisk) }}/100
                    </span>
                </div>
                <div class="progress" style="height:12px; border-radius:6px;">
                    <div class="progress-bar {{ $newsRisk > 65 ? 'bg-danger' : ($newsRisk > 35 ? 'bg-warning' : 'bg-success') }}"
                         style="width:{{ $newsRisk }}%; border-radius:6px;"></div>
                </div>
                <small class="text-muted">
                    Sentiment:
                    <span class="badge {{ $sentiment['sentiment'] === 'positive' ? 'bg-success' : ($sentiment['sentiment'] === 'negative' ? 'bg-danger' : 'bg-secondary') }}">
                        {{ ucfirst($sentiment['sentiment']) }}
                    </span>
                    | Skor: {{ $sentiment['score'] }}
                    | ✅ {{ $sentiment['positive'] }} kata positif
                    | ❌ {{ $sentiment['negative'] }} kata negatif
                </small>
            </div>

        </div>
    </div>
</div>

{{-- Radar Chart & Formula --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="section-card">
            <div class="section-title">🕸️ Radar Risk Profile</div>
            <canvas id="radarChart" height="250"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="section-card">
            <div class="section-title">🧮 Formula Perhitungan Risiko</div>
            <div class="bg-light p-3 rounded mb-3" style="font-family: monospace; font-size:0.85rem;">
                <div class="text-primary fw-bold mb-2">Risk Score Formula:</div>
                <div>Risk = (Weather × 30%)</div>
                <div>     + (Inflation × 20%)</div>
                <div>     + (Currency × 10%)</div>
                <div>     + (News Sentiment × 40%)</div>
                <hr>
                <div class="fw-bold">= ({{ round($weatherRisk) }} × 0.30)</div>
                <div class="fw-bold">+ ({{ round($inflationRisk) }} × 0.20)</div>
                <div class="fw-bold">+ ({{ round($currencyRisk) }} × 0.10)</div>
                <div class="fw-bold">+ ({{ round($newsRisk) }} × 0.40)</div>
                <hr>
                <div class="text-success fw-bold fs-5">= {{ $totalRisk }} → {{ $riskLevel['level'] }}</div>
            </div>
            <div class="row text-center g-2">
                <div class="col-4">
                    <div class="p-2 rounded" style="background:#e8f5e9;">
                        <div class="fw-bold text-success">< 35</div>
                        <div class="small">Low Risk</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 rounded" style="background:#fff3e0;">
                        <div class="fw-bold text-warning">35 - 65</div>
                        <div class="small">Medium Risk</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 rounded" style="background:#ffebee;">
                        <div class="fw-bold text-danger">> 65</div>
                        <div class="small">High Risk</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ===== GAUGE CHART (Semicircle) =====
const gaugeCanvas = document.getElementById('gaugeChart');
const gCtx = gaugeCanvas.getContext('2d');
const score = {{ $totalRisk }};
const color = score > 65 ? '#b71c1c' : score > 35 ? '#e65100' : '#2e7d32';

// Background arc
gCtx.beginPath();
gCtx.arc(125, 130, 100, Math.PI, 2 * Math.PI);
gCtx.lineWidth = 20;
gCtx.strokeStyle = '#e0e0e0';
gCtx.stroke();

// Score arc
const angle = Math.PI + (score / 100) * Math.PI;
gCtx.beginPath();
gCtx.arc(125, 130, 100, Math.PI, angle);
gCtx.lineWidth = 20;
gCtx.strokeStyle = color;
gCtx.stroke();

// Labels
gCtx.fillStyle = '#666';
gCtx.font = '12px Arial';
gCtx.fillText('0', 15, 140);
gCtx.fillText('50', 118, 20);
gCtx.fillText('100', 220, 140);

// ===== RADAR CHART =====
new Chart(document.getElementById('radarChart'), {
    type: 'radar',
    data: {
        labels: ['Weather Risk', 'Inflation Risk', 'Currency Risk', 'News Risk', 'Overall'],
        datasets: [{
            label: '{{ $country["name"] }}',
            data: [
                {{ round($weatherRisk) }},
                {{ round($inflationRisk) }},
                {{ round($currencyRisk) }},
                {{ round($newsRisk) }},
                {{ $totalRisk }}
            ],
            backgroundColor: 'rgba(33, 150, 243, 0.2)',
            borderColor: '#2196F3',
            pointBackgroundColor: '#2196F3',
            pointRadius: 5,
        }]
    },
    options: {
        scales: {
            r: {
                beginAtZero: true,
                max: 100,
                ticks: { stepSize: 25 }
            }
        },
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
@endpush