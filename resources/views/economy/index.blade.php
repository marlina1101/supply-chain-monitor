@extends('layouts.app')

@section('page-title', 'Ekonomi Dunia')

@section('content')

{{-- Indicator Selector --}}
<div class="section-card mb-4">
    <div class="section-title">📊 Pilih Indikator Ekonomi</div>
    <form method="GET" action="{{ route('economy') }}">
        <div class="row g-2">
            <div class="col-md-6">
                <select name="indicator" class="form-select" onchange="this.form.submit()">
                    @foreach($indicators as $code => $label)
                        <option value="{{ $code }}" {{ $indicator == $code ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-repeat"></i> Tampilkan
                </button>
            </div>
            <div class="col-md-3 text-end text-muted small d-flex align-items-center justify-content-end">
                <i class="bi bi-database me-1"></i> Sumber: World Bank API
            </div>
        </div>
    </form>
</div>

@if(isset($error))
    <div class="alert alert-danger">{{ $error }}</div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Negara Dipantau</div>
                    <div class="fs-4 fw-bold">{{ count($economyData) }}</div>
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
                    <div class="text-muted small">Indikator</div>
                    <div class="fs-6 fw-bold">{{ $indicators[$indicator] }}</div>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Tertinggi</div>
                    <div class="fs-6 fw-bold text-success">
                        {{ $economyData[0]['country'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-trophy"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Sumber Data</div>
                    <div class="fs-6 fw-bold">World Bank</div>
                </div>
                <div class="icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-bank"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart --}}
<div class="section-card mb-4">
    <div class="section-title">📈 Grafik {{ $indicators[$indicator] }}</div>
    <canvas id="economyChart" height="80"></canvas>
</div>

{{-- Table --}}
<div class="section-card">
    <div class="section-title">🏦 Data {{ $indicators[$indicator] }} per Negara</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Peringkat</th>
                    <th>Negara</th>
                    <th>Kode</th>
                    <th>{{ $indicators[$indicator] }}</th>
                    <th>Tahun Data</th>
                    <th>Proporsi</th>
                </tr>
            </thead>
            <tbody>
                @php $maxVal = $economyData[0]['value'] ?? 1; @endphp
                @foreach($economyData as $i => $d)
                <tr>
                    <td>
                        @if($i == 0) 🥇
                        @elseif($i == 1) 🥈
                        @elseif($i == 2) 🥉
                        @else <span class="text-muted">{{ $i + 1 }}</span>
                        @endif
                    </td>
                    <td class="fw-bold">{{ $d['country'] }}</td>
                    <td><span class="badge bg-secondary">{{ $d['code'] }}</span></td>
                    <td class="fw-bold text-success">
                        @if($indicator === 'NY.GDP.MKTP.CD')
                            ${{ number_format($d['value'] / 1e12, 2) }} T
                        @elseif($indicator === 'SP.POP.TOTL')
                            {{ number_format($d['value'] / 1e6, 1) }} Juta
                        @else
                            {{ number_format($d['value'], 2) }}%
                        @endif
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $d['year'] }}</span></td>
                    <td style="width: 200px;">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: {{ ($d['value'] / $maxVal) * 100 }}%"></div>
                        </div>
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
const labels = @json(array_column($economyData, 'country'));
const values = @json(array_column($economyData, 'value'));

new Chart(document.getElementById('economyChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: '{{ $indicators[$indicator] }}',
            data: values,
            backgroundColor: labels.map((_, i) =>
                i === 0 ? '#ffd700' : i === 1 ? '#c0c0c0' : i === 2 ? '#cd7f32' : '#4e73df'
            ),
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