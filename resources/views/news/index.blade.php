@extends('layouts.app')

@section('page-title', 'Berita Global')

@section('content')

{{-- Category Selector --}}
<div class="section-card mb-4">
    <div class="section-title">📰 Pilih Kategori Berita</div>
    <div class="d-flex gap-2 flex-wrap">
        @foreach($categories as $key => $label)
            <a href="{{ route('news', ['category' => $key]) }}"
               class="btn {{ $category == $key ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

@if(isset($error))
    <div class="alert alert-danger">{{ $error }}</div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Berita</div>
                    <div class="fs-4 fw-bold">{{ count($news) }}</div>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-newspaper"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Kategori</div>
                    <div class="fs-6 fw-bold">{{ $categories[$category] }}</div>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-tag"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Sumber</div>
                    <div class="fs-6 fw-bold">GNews API</div>
                </div>
                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-rss"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- News Grid --}}
@if(count($news) === 0)
    <div class="section-card text-center py-5 text-muted">
        <i class="bi bi-newspaper fs-1"></i>
        <p class="mt-2">Tidak ada berita tersedia saat ini</p>
    </div>
@else
<div class="row g-3">
    @foreach($news as $i => $article)
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            @if(!empty($article['image']))
                <img src="{{ $article['image'] }}" class="card-img-top"
                     style="height: 180px; object-fit: cover;"
                     onerror="this.style.display='none'">
            @endif
            <div class="card-body">
                {{-- Source & Date --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-primary">
                        {{ $article['source']['name'] ?? 'Unknown' }}
                    </span>
                    <small class="text-muted">
                        <i class="bi bi-clock"></i>
                        {{ \Carbon\Carbon::parse($article['publishedAt'])->diffForHumans() }}
                    </small>
                </div>

                {{-- Title --}}
                <h6 class="fw-bold card-title">{{ $article['title'] }}</h6>

                {{-- Description --}}
                <p class="text-muted small card-text">
                    {{ Str::limit($article['description'] ?? '', 120) }}
                </p>

                {{-- Read More --}}
                <a href="{{ $article['url'] }}" target="_blank"
                   class="btn btn-sm btn-outline-primary mt-auto">
                    <i class="bi bi-box-arrow-up-right"></i> Baca Selengkapnya
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection