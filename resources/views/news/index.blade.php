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

{{-- Auto-refresh Bar --}}
<div class="section-card mb-4 d-flex justify-content-between align-items-center">
    <div>
        <span class="text-muted small" id="lastRefreshTime">
            Diperbarui: {{ now()->format('d M Y, H:i') }}
        </span>
    </div>
    <div>
        <span class="badge bg-primary" id="refreshCountdown">Auto-refresh dalam 5:00</span>
        <button class="btn btn-sm btn-outline-primary ms-2" onclick="refreshNews()">
            <i class="bi bi-arrow-clockwise"></i> Refresh Sekarang
        </button>
    </div>
</div>

{{-- Hidden category input --}}
<input type="hidden" id="currentCategory" value="{{ $category }}">

{{-- News Grid --}}
@if(count($news) === 0)
    <div class="section-card text-center py-5 text-muted">
        <i class="bi bi-newspaper fs-1"></i>
        <p class="mt-2">Tidak ada berita tersedia saat ini</p>
    </div>
@else
<div class="row g-3" id="newsGrid">
    @foreach($news as $article)
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            @if(!empty($article['image']))
                <img src="{{ $article['image'] }}" class="card-img-top"
                     style="height: 180px; object-fit: cover;"
                     onerror="this.style.display='none'">
            @endif
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-primary">
                        {{ $article['source']['name'] ?? 'Unknown' }}
                    </span>
                    <small class="text-muted">
                        <i class="bi bi-clock"></i>
                        {{ \Carbon\Carbon::parse($article['publishedAt'])->diffForHumans() }}
                    </small>
                </div>
                <h6 class="fw-bold card-title">{{ $article['title'] }}</h6>
                <p class="text-muted small card-text">
                    {{ Str::limit($article['description'] ?? '', 120) }}
                </p>
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

@push('scripts')
<script>
// ===== AJAX: Auto-refresh Berita setiap 5 menit =====
let newsRefreshTimer;
let countdown = 300;

function startCountdown() {
    countdown = 300;
    updateCountdownDisplay();
    newsRefreshTimer = setInterval(function() {
        countdown--;
        updateCountdownDisplay();
        if (countdown <= 0) refreshNews();
    }, 1000);
}

function updateCountdownDisplay() {
    const mins = Math.floor(countdown / 60);
    const secs = countdown % 60;
    const el = document.getElementById('refreshCountdown');
    if (el) el.textContent = `Auto-refresh dalam ${mins}:${secs.toString().padStart(2,'0')}`;
}

function refreshNews() {
    clearInterval(newsRefreshTimer);
    const category = document.getElementById('currentCategory')?.value || 'economy';
    const grid     = document.getElementById('newsGrid');
    if (!grid) return;

    grid.style.opacity = '0.4';
    const countdownEl = document.getElementById('refreshCountdown');
    if (countdownEl) countdownEl.textContent = '🔄 Memperbarui berita...';

    fetch(`/api/news?category=${category}`)
        .then(res => res.json())
        .then(data => {
            grid.style.opacity = '1';
            if (data.success && data.data.length > 0) {
                let html = '';
                data.data.forEach(article => {
                    const timeAgo = article.publishedAt
                        ? new Date(article.publishedAt).toLocaleDateString('id-ID')
                        : 'Baru saja';
                    html += `
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            ${article.image
                                ? `<img src="${article.image}" class="card-img-top"
                                       style="height:180px;object-fit:cover;"
                                       onerror="this.style.display='none'">`
                                : ''}
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary">${article.source?.name || 'News'}</span>
                                    <small class="text-muted"><i class="bi bi-clock"></i> ${timeAgo}</small>
                                </div>
                                <h6 class="fw-bold card-title">${article.title}</h6>
                                <p class="text-muted small card-text">
                                    ${article.description ? article.description.substring(0, 120) + '...' : ''}
                                </p>
                                <a href="${article.url}" target="_blank"
                                   class="btn btn-sm btn-outline-primary mt-auto">
                                    <i class="bi bi-box-arrow-up-right"></i> Baca Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>`;
                });
                grid.innerHTML = html;
                const tsEl = document.getElementById('lastRefreshTime');
                if (tsEl) tsEl.textContent = 'Diperbarui: ' + new Date().toLocaleString('id-ID');
            }
            startCountdown();
        })
        .catch(err => {
            grid.style.opacity = '1';
            console.error('Refresh error:', err);
            startCountdown();
        });
}

startCountdown();
</script>
@endpush