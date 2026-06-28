@extends('layouts.app')

@section('page-title', 'Nilai Tukar')

@section('content')

{{-- Base Currency Selector --}}
<div class="section-card mb-4">
    <div class="section-title">💱 Pilih Mata Uang Dasar</div>
    <form method="GET" action="{{ route('currency') }}">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="base" class="form-select" onchange="this.form.submit()">
                    @foreach($mainCurrencies as $code => $info)
                        <option value="{{ $code }}" {{ $base == $code ? 'selected' : '' }}>
                            {{ $info['flag'] }} {{ $code }} - {{ $info['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-repeat"></i> Update Kurs
                </button>
            </div>
            <div class="col-md-5 text-end text-muted small">
                <i class="bi bi-clock"></i> Data diperbarui: {{ now()->format('d M Y, H:i') }} WIB
            </div>
        </div>
    </form>
</div>

@if(isset($error))
    <div class="alert alert-danger">{{ $error }}</div>
@endif

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Mata Uang Dasar</div>
                    <div class="fs-3 fw-bold text-primary">{{ $base }}</div>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-currency-exchange"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Mata Uang Dipantau</div>
                    <div class="fs-3 fw-bold">{{ count($rates) }}</div>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-globe2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Status Data</div>
                    <div class="fs-5 fw-bold text-success">✅ Real-time</div>
                </div>
                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-wifi"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Rate Cards --}}
<div class="section-card mb-4">
    <div class="section-title">💰 Kurs terhadap {{ $base }}</div>
    <div class="row g-3">
        @foreach($rates as $r)
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size:1.8rem;">{{ $r['flag'] }}</span>
                        <span class="badge bg-primary">{{ $r['code'] }}</span>
                    </div>
                    <div class="fw-bold">{{ $r['name'] }}</div>
                    <div class="text-muted small mb-2">{{ $r['country'] }}</div>
                    <div class="fs-5 fw-bold text-success">
                        {{ number_format($r['rate'], 4) }}
                    </div>
                    <div class="text-muted small">1 {{ $base }} = {{ number_format($r['rate'], 2) }} {{ $r['code'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Chart --}}
<div class="section-card">
    <div class="section-title">📊 Grafik Perbandingan Nilai Tukar (terhadap {{ $base }})</div>
    <canvas id="currencyChart" height="100"></canvas>
</div>

@endsection

@push('scripts')
<script>
const labels = @json(array_column($rates, 'code'));
const values = @json(array_column($rates, 'rate'));

new Chart(document.getElementById('currencyChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Nilai Tukar terhadap {{ $base }}',
            data: values,
            backgroundColor: '#4e73df',
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