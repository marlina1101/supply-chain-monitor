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

{{-- Live Currency Converter --}}
<div class="section-card mt-4">
    <div class="section-title">⚡ Live Currency Converter (AJAX)</div>
    <div class="row g-3 align-items-center">
        <div class="col-md-3">
            <label class="form-label small fw-bold">Nominal</label>
            <input type="number" id="convertAmount" class="form-control form-control-lg"
                   value="1000" min="0" step="any" placeholder="Masukkan nominal...">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Dari Mata Uang</label>
            <select id="fromCurrency" class="form-select form-select-lg">
                @foreach($mainCurrencies as $code => $info)
                    <option value="{{ $code }}" {{ $code == 'USD' ? 'selected' : '' }}>
                        {{ $info['flag'] }} {{ $code }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1 text-center pt-4">
            <button id="swapBtn" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left-right"></i>
            </button>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Ke Mata Uang</label>
            <select id="toCurrency" class="form-select form-select-lg">
                @foreach($mainCurrencies as $code => $info)
                    <option value="{{ $code }}" {{ $code == 'IDR' ? 'selected' : '' }}>
                        {{ $info['flag'] }} {{ $code }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 pt-4">
            <button id="convertBtn" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-calculator"></i> Konversi
            </button>
        </div>
    </div>

    {{-- Hasil Konversi --}}
    <div id="convertResult" class="mt-4" style="display:none;">
        <div class="p-4 rounded-3" style="background: linear-gradient(135deg, #1a237e, #283593);">
            <div class="text-white text-center">
                <div class="fs-6 opacity-75 mb-1">Hasil Konversi</div>
                <div class="fs-1 fw-bold" id="resultAmount">—</div>
                <div class="fs-6 opacity-75 mt-1" id="resultDetail">—</div>
                <div class="mt-2 small opacity-50" id="resultTime">—</div>
            </div>
        </div>
    </div>

    {{-- Loading --}}
    <div id="convertLoading" class="text-center py-3" style="display:none;">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="text-muted small mt-2">Mengambil kurs terbaru...</div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ===== Chart Nilai Tukar =====
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

// ===== AJAX: Live Currency Converter =====
function doConvert() {
    const amount = parseFloat(document.getElementById('convertAmount').value) || 0;
    const from   = document.getElementById('fromCurrency').value;
    const to     = document.getElementById('toCurrency').value;

    if (amount <= 0) {
        alert('Masukkan nominal yang valid!');
        return;
    }

    document.getElementById('convertLoading').style.display = 'block';
    document.getElementById('convertResult').style.display  = 'none';

    fetch(`/api/currency?base=${from}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('convertLoading').style.display = 'none';

            if (data.success && data.rates[to]) {
                const rate   = data.rates[to];
                const result = amount * rate;

                document.getElementById('convertResult').style.display = 'block';
                document.getElementById('resultAmount').textContent =
                    result.toLocaleString('id-ID', { maximumFractionDigits: 2 }) + ' ' + to;
                document.getElementById('resultDetail').textContent =
                    `${amount.toLocaleString()} ${from} × ${rate.toFixed(6)} = ${result.toLocaleString('id-ID', { maximumFractionDigits: 2 })} ${to}`;
                document.getElementById('resultTime').textContent =
                    'Data diperbarui: ' + new Date().toLocaleString('id-ID');
            } else {
                alert('Gagal mengambil data kurs!');
            }
        })
        .catch(err => {
            document.getElementById('convertLoading').style.display = 'none';
            alert('Error: ' + err.message);
        });
}

document.getElementById('convertBtn').addEventListener('click', doConvert);

let convertTimer;
document.getElementById('convertAmount').addEventListener('input', function() {
    clearTimeout(convertTimer);
    convertTimer = setTimeout(doConvert, 500);
});

document.getElementById('fromCurrency').addEventListener('change', doConvert);
document.getElementById('toCurrency').addEventListener('change', doConvert);

document.getElementById('swapBtn').addEventListener('click', function() {
    const from = document.getElementById('fromCurrency').value;
    const to   = document.getElementById('toCurrency').value;
    document.getElementById('fromCurrency').value = to;
    document.getElementById('toCurrency').value   = from;
    doConvert();
});

doConvert();
</script>
@endpush