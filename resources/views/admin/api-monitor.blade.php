@extends('admin.layouts.app')
@section('page-title', 'Monitor API')
@section('content')

<div class="section-card mb-4">
    <div class="section-title">🔌 Status Layanan API — Real-time</div>
    <div class="row g-3">
        @foreach($results as $api)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size:1.8rem;">{{ $api['icon'] }}</span>
                        @if($api['status'] === 'online')
                            <span class="badge bg-success">✅ Online</span>
                        @elseif($api['status'] === 'error')
                            <span class="badge bg-warning text-dark">⚠️ Error</span>
                        @else
                            <span class="badge bg-danger">❌ Offline</span>
                        @endif
                    </div>
                    <div class="fw-bold">{{ $api['name'] }}</div>
                    <div class="text-muted small mt-1">
                        HTTP Status: <strong>{{ $api['code'] ?: 'N/A' }}</strong>
                    </div>
                    <div class="text-muted small">
                        Response Time:
                        <strong class="{{ $api['time'] > 3000 ? 'text-danger' : ($api['time'] > 1000 ? 'text-warning' : 'text-success') }}">
                            {{ $api['time'] }} ms
                        </strong>
                    </div>
                    {{-- Progress bar response time --}}
                    <div class="progress mt-2" style="height:4px;">
                        <div class="progress-bar {{ $api['status'] === 'online' ? 'bg-success' : 'bg-danger' }}"
                             style="width:{{ $api['status'] === 'online' ? min(($api['time']/5000)*100, 100) : 100 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Summary --}}
<div class="row g-3">
    @php
        $online  = collect($results)->where('status', 'online')->count();
        $offline = collect($results)->where('status', 'offline')->count();
        $error   = collect($results)->where('status', 'error')->count();
        $avgTime = collect($results)->where('status', 'online')->avg('time');
    @endphp
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="fs-1 fw-bold text-success">{{ $online }}</div>
            <div class="text-muted">API Online</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="fs-1 fw-bold text-danger">{{ $offline }}</div>
            <div class="text-muted">API Offline</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="fs-1 fw-bold text-warning">{{ $error }}</div>
            <div class="text-muted">API Error</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="fs-1 fw-bold text-primary">{{ round($avgTime) }}</div>
            <div class="text-muted">Avg Response (ms)</div>
        </div>
    </div>
</div>

@endsection