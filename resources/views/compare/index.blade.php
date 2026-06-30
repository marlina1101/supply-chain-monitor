@extends('layouts.app')

@section('page-title', 'Country Comparison')

@section('content')

{{-- Selector --}}
<div class="section-card mb-4">
    <div class="section-title">🔄 Pilih 2 Negara untuk Dibandingkan</div>
    <form method="GET" action="{{ route('compare') }}">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label fw-bold text-primary">🌍 Negara 1</label>
                <select name="country1" class="form-select">
                    @foreach($countries as $code => $c)
                        <option value="{{ $code }}" {{ $code1 == $code ? 'selected' : '' }}>
                            {{ $c['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 text-center pt-4">
                <span class="fs-3 fw-bold text-muted">VS</span>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-danger">🌍 Negara 2</label>
                <select name="country2" class="form-select">
                    @foreach($countries as $code => $c)
                        <option value="{{ $code }}" {{ $code2 == $code ? 'selected' : '' }}>
                            {{ $c['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 pt-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-arrow-left-right"></i> Bandingkan
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Risk Score Comparison --}}
<div class="row g-3 mb-4">
    <div class="col-md-5">
        <div class="section-card text-center h-100">
            <div class="fs-4 fw-bold text-primary mb-1">🌍 {{ $data1['name'] }}</div>
            <div class="display-4 fw-bold" style="color:{{ $data1['riskLevel']['color'] }}">
                {{ $data1['totalRisk'] }}
            </div>
            <div class="text-muted mb-2">Risk Score</div>
            <span class="badge fs-6 bg-{{ $data1['riskLevel']['class'] }} px-4 py-2">
                {{ $data1['riskLevel']['level'] }}
            </span>
        </div>
    </div>
    <div class="col-md-2 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <div class="fs-1 fw-bold text-muted">VS</div>
            @if($data1['totalRisk'] < $data2['totalRisk'])
                <div class="badge bg-success mt-2">{{ $data1['name'] }} lebih aman</div>
            @elseif($data1['totalRisk'] > $data2['totalRisk'])
                <div class="badge bg-success mt-2">{{ $data2['name'] }} lebih aman</div>
            @else
                <div class="badge bg-secondary mt-2">Sama</div>
            @endif
        </div>
    </div>
    <div class="col-md-5">
        <div class="section-card text-center h-100">
            <div class="fs-4 fw-bold text-danger mb-1">🌍 {{ $data2['name'] }}</div>
            <div class="display-4 fw-bold" style="color:{{ $data2['riskLevel']['color'] }}">
                {{ $data2['totalRisk'] }}
            </div>
            <div class="text-muted mb-2">Risk Score</div>
            <span class="badge fs-6 bg-{{ $data2['riskLevel']['class'] }} px-4 py-2">
                {{ $data2['riskLevel']['level'] }}
            </span>
        </div>
    </div>
</div>

{{-- Detail Comparison Table --}}
<div class="section-card mb-4">
    <div class="section-title">📊 Perbandingan Detail</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>Indikator</th>
                    <th class="text-primary">{{ $data1['name'] }}</th>
                    <th class="text-danger">{{ $data2['name'] }}</th>
                    <th>Lebih Baik</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold text-start">🌡️ Temperatur</td>
                    <td>{{ $data1['temp'] !== null ? $data1['temp'].'°C' : 'N/A' }}</td>
                    <td>{{ $data2['temp'] !== null ? $data2['temp'].'°C' : 'N/A' }}</td>
                    <td>—</td>
                </tr>
                <tr>
                    <td class="fw-bold text-start">💨 Kecepatan Angin</td>
                    <td>{{ $data1['windSpeed'] }} km/h</td>
                    <td>{{ $data2['windSpeed'] }} km/h</td>
                    <td>
                        @if($data1['windSpeed'] < $data2['windSpeed'])
                            <span class="badge bg-primary">{{ $data1['name'] }}</span>
                        @elseif($data1['windSpeed'] > $data2['windSpeed'])
                            <span class="badge bg-danger">{{ $data2['name'] }}</span>
                        @else
                            <span class="badge bg-secondary">Sama</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-start">📈 Inflasi</td>
                    <td>{{ $data1['inflation'] !== null ? number_format($data1['inflation'],2).'%' : 'N/A' }}</td>
                    <td>{{ $data2['inflation'] !== null ? number_format($data2['inflation'],2).'%' : 'N/A' }}</td>
                    <td>
                        @if($data1['inflation'] !== null && $data2['inflation'] !== null)
                            @if($data1['inflation'] < $data2['inflation'])
                                <span class="badge bg-primary">{{ $data1['name'] }}</span>
                            @elseif($data1['inflation'] > $data2['inflation'])
                                <span class="badge bg-danger">{{ $data2['name'] }}</span>
                            @else
                                <span class="badge bg-secondary">Sama</span>
                            @endif
                        @else —
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-start">💱 Exchange Rate (vs USD)</td>
                    <td>{{ number_format($data1['exchangeRate'], 4) }}</td>
                    <td>{{ number_format($data2['exchangeRate'], 4) }}</td>
                    <td>—</td>
                </tr>
                <tr>
                    <td class="fw-bold text-start">🌤️ Weather Risk</td>
                    <td>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar bg-{{ $data1['weatherRisk'] > 65 ? 'danger' : ($data1['weatherRisk'] > 35 ? 'warning' : 'success') }}"
                                 style="width:{{ $data1['weatherRisk'] }}%"></div>
                        </div>
                        <small>{{ round($data1['weatherRisk']) }}/100</small>
                    </td>
                    <td>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar bg-{{ $data2['weatherRisk'] > 65 ? 'danger' : ($data2['weatherRisk'] > 35 ? 'warning' : 'success') }}"
                                 style="width:{{ $data2['weatherRisk'] }}%"></div>
                        </div>
                        <small>{{ round($data2['weatherRisk']) }}/100</small>
                    </td>
                    <td>
                        @if($data1['weatherRisk'] < $data2['weatherRisk'])
                            <span class="badge bg-primary">{{ $data1['name'] }}</span>
                        @elseif($data1['weatherRisk'] > $data2['weatherRisk'])
                            <span class="badge bg-danger">{{ $data2['name'] }}</span>
                        @else
                            <span class="badge bg-secondary">Sama</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-start">⚡ Total Risk Score</td>
                    <td class="fw-bold fs-5" style="color:{{ $data1['riskLevel']['color'] }}">
                        {{ $data1['totalRisk'] }}
                    </td>
                    <td class="fw-bold fs-5" style="color:{{ $data2['riskLevel']['color'] }}">
                        {{ $data2['totalRisk'] }}
                    </td>
                    <td>
                        @if($data1['totalRisk'] < $data2['totalRisk'])
                            <span class="badge bg-primary">{{ $data1['name'] }}</span>
                        @elseif($data1['totalRisk'] > $data2['totalRisk'])
                            <span class="badge bg-danger">{{ $data2['name'] }}</span>
                        @else
                            <span class="badge bg-secondary">Sama</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Radar Chart Comparison --}}
<div class="section-card">
    <div class="section-title">🕸️ Radar Chart Perbandingan Risiko</div>
    <div class="row">
        <div class="col-md-8 mx-auto">
            <canvas id="compareRadar" height="300"></canvas>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
new Chart(document.getElementById('compareRadar'), {
    type: 'radar',
    data: {
        labels: ['Weather Risk', 'Inflation Risk', 'Currency Risk', 'News Risk', 'Total Risk'],
        datasets: [
            {
                label: '{{ $data1["name"] }}',
                data: [
                    {{ round($data1['weatherRisk']) }},
                    {{ round($data1['inflationRisk']) }},
                    {{ round($data1['currencyRisk']) }},
                    {{ round($data1['newsRisk']) }},
                    {{ $data1['totalRisk'] }}
                ],
                backgroundColor: 'rgba(33,150,243,0.2)',
                borderColor: '#2196F3',
                pointBackgroundColor: '#2196F3',
                pointRadius: 5,
            },
            {
                label: '{{ $data2["name"] }}',
                data: [
                    {{ round($data2['weatherRisk']) }},
                    {{ round($data2['inflationRisk']) }},
                    {{ round($data2['currencyRisk']) }},
                    {{ round($data2['newsRisk']) }},
                    {{ $data2['totalRisk'] }}
                ],
                backgroundColor: 'rgba(244,67,54,0.2)',
                borderColor: '#F44336',
                pointBackgroundColor: '#F44336',
                pointRadius: 5,
            }
        ]
    },
    options: {
        scales: {
            r: { beginAtZero: true, max: 100, ticks: { stepSize: 25 } }
        },
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
@endpush