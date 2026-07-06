@extends('admin.layouts.app')
@section('page-title', 'Pengaturan Sistem')
@section('content')

<div class="row g-3">
    <div class="col-md-8">
        <div class="section-card">
            <div class="section-title">⚙️ Pengaturan Sistem</div>
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @foreach($settings as $key => $setting)
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        {{ ucwords(str_replace('_', ' ', $key)) }}
                    </label>
                    @if($key === 'app_name')
                    <input type="text" name="{{ $key }}"
                           class="form-control" value="{{ $setting->value }}">
                    @elseif(str_contains($key, 'interval') || str_contains($key, 'max') || str_contains($key, 'cities'))
                    <input type="number" name="{{ $key }}"
                           class="form-control" value="{{ $setting->value }}">
                    @else
                    <input type="text" name="{{ $key }}"
                           class="form-control" value="{{ $setting->value }}">
                    @endif
                    <div class="form-text text-muted">{{ $setting->description }}</div>
                </div>
                @endforeach
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Pengaturan
                </button>
            </form>
        </div>
    </div>
    <div class="col-md-4">
        <div class="section-card">
            <div class="section-title">ℹ️ Info Sistem</div>
            <table class="table table-sm table-borderless">
                <tr>
                    <td class="text-muted small">Laravel</td>
                    <td class="fw-bold">{{ app()->version() }}</td>
                </tr>
                <tr>
                    <td class="text-muted small">PHP</td>
                    <td class="fw-bold">{{ PHP_VERSION }}</td>
                </tr>
                <tr>
                    <td class="text-muted small">Environment</td>
                    <td class="fw-bold">{{ app()->environment() }}</td>
                </tr>
                <tr>
                    <td class="text-muted small">Database</td>
                    <td class="fw-bold">MySQL</td>
                </tr>
                <tr>
                    <td class="text-muted small">Timezone</td>
                    <td class="fw-bold">{{ config('app.timezone') }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

@endsection