@extends('admin.layouts.app')
@section('page-title', 'Manajemen Artikel')
@section('content')

<div class="row g-3">
    <div class="col-md-4">
        <div class="section-card">
            <div class="section-title">➕ Tambah Artikel</div>
            <form method="POST" action="{{ route('admin.articles.store') }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label small fw-bold">Judul</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="economy">Ekonomi</option>
                        <option value="logistics">Logistik</option>
                        <option value="geopolitics">Geopolitik</option>
                        <option value="risk">Analisis Risiko</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Konten</label>
                    <textarea name="content" class="form-control" rows="6" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save"></i> Simpan Artikel
                </button>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="section-card">
            <div class="section-title">📰 Daftar Artikel ({{ count($articles) }})</div>
            @if(count($articles) === 0)
                <div class="text-center text-muted py-4">Belum ada artikel</div>
            @else
            <div class="list-group list-group-flush">
                @foreach($articles as $a)
                <div class="list-group-item px-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1 me-3">
                            <div class="fw-bold">{{ $a->title }}</div>
                            <div class="d-flex gap-2 mt-1">
                                <span class="badge bg-secondary">{{ ucfirst($a->category) }}</span>
                                <small class="text-muted">oleh {{ $a->author }}</small>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($a->created_at)->format('d M Y') }}
                                </small>
                            </div>
                            <p class="small text-muted mt-1 mb-0">
                                {{ Str::limit($a->content, 100) }}
                            </p>
                        </div>
                        <form method="POST"
                              action="{{ route('admin.articles.delete', $a->id) }}"
                              onsubmit="return confirm('Hapus artikel ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

@endsection