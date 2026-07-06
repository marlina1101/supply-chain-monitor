@extends('admin.layouts.app')
@section('page-title', 'Manajemen Pelabuhan')
@section('content')

<div class="row g-3">
    <div class="col-md-4">
        <div class="section-card">
            <div class="section-title">➕ Tambah Pelabuhan</div>
            <form method="POST" action="{{ route('admin.ports.store') }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label small fw-bold">Nama Pelabuhan</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Negara</label>
                    <input type="text" name="country" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Wilayah</label>
                    <select name="region" class="form-select">
                        <option value="Asia">Asia</option>
                        <option value="Europe">Europe</option>
                        <option value="Americas">Americas</option>
                        <option value="Middle East">Middle East</option>
                        <option value="Africa">Africa</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-6 mb-2">
                        <label class="form-label small fw-bold">Latitude</label>
                        <input type="number" step="0.0001" name="latitude"
                               class="form-control" required>
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label small fw-bold">Longitude</label>
                        <input type="number" step="0.0001" name="longitude"
                               class="form-control" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Volume (Juta TEU)</label>
                    <input type="number" step="0.1" name="volume"
                           class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">Aktif</option>
                        <option value="busy">Sibuk</option>
                        <option value="disrupted">Terganggu</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save"></i> Simpan Pelabuhan
                </button>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="section-card">
            <div class="section-title">⚓ Pelabuhan di Database ({{ count($ports) }})</div>
            @if(count($ports) === 0)
                <div class="text-center text-muted py-4">Belum ada data pelabuhan</div>
            @else
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Negara</th>
                            <th>Wilayah</th>
                            <th>Volume</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ports as $port)
                        <tr>
                            <td class="fw-bold small">{{ $port->name }}</td>
                            <td class="small">{{ $port->country }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $port->region }}</span>
                            </td>
                            <td class="small">{{ $port->volume }}M TEU</td>
                            <td>
                                @if($port->status === 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @elseif($port->status === 'busy')
                                    <span class="badge bg-warning text-dark">Sibuk</span>
                                @else
                                    <span class="badge bg-danger">Terganggu</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST"
                                      action="{{ route('admin.ports.delete', $port->id) }}"
                                      onsubmit="return confirm('Hapus pelabuhan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection