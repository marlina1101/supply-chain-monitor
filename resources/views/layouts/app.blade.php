<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RiskRadar — Supply Chain Analytics</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <style>
        body { background-color: #f0f2f5; }

        /* Sidebar */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #1b5e20 0%, #2e7d32 100%);
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            padding-top: 0;
            display: flex;
            flex-direction: column;
        }
        .sidebar .brand {
            color: white;
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            margin-bottom: 5px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.80);
            padding: 9px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.2s;
            font-size: 0.88rem;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.18);
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 8px;
            width: 18px;
        }
        .sidebar .nav-section {
            font-size: 0.68rem;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 20px 4px;
        }
        .sidebar hr {
            border-color: rgba(255,255,255,0.15);
            margin: 8px 20px;
        }
        .sidebar-footer {
            margin-top: auto;
            padding: 10px;
            border-top: 1px solid rgba(255,255,255,0.15);
        }

        /* Main content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        /* Topbar */
        .topbar {
            background: white;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 4px solid #2e7d32;
        }

        /* Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: none;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card .icon-box {
            width: 50px; height: 50px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }

        /* Section cards */
        .section-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 20px;
        }
        .section-card .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1b5e20;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8f5e9;
        }

        /* Radar animation */
        @keyframes radarPulse {
            0%   { box-shadow: 0 0 0 0 rgba(165,214,167,0.5); }
            70%  { box-shadow: 0 0 0 10px rgba(165,214,167,0); }
            100% { box-shadow: 0 0 0 0 rgba(165,214,167,0); }
        }
        .radar-icon {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            animation: radarPulse 2s infinite;
        }

        /* Risk colors */
        .risk-low    { background: #1b5e20; color: white; }
        .risk-medium { background: #e65100; color: white; }
        .risk-high   { background: #b71c1c; color: white; }

        /* Gauge */
        .gauge-wrap { position: relative; text-align: center; }

        /* User card di sidebar */
        .user-card {
            background: rgba(255,255,255,0.12);
            border-radius: 10px;
            padding: 10px 12px;
            margin: 4px 10px;
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<div class="sidebar">
    {{-- Brand --}}
    <div class="brand">
        <div class="d-flex align-items-center gap-2">
            <div class="radar-icon">
                <i class="bi bi-radar" style="font-size:1.3rem; color:#a5d6a7;"></i>
            </div>
            <div>
                <div style="font-size:1rem; font-weight:700; color:white;">RiskRadar</div>
                <div style="font-size:0.62rem; color:rgba(255,255,255,0.55); letter-spacing:1px;">
                    SUPPLY CHAIN ANALYTICS
                </div>
            </div>
        </div>
    </div>

    <nav class="nav flex-column flex-grow-1">
        {{-- Menu Utama --}}
        <div class="nav-section">Utama</div>
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('globalcountry') }}"
           class="nav-link {{ request()->routeIs('globalcountry') ? 'active' : '' }}">
            <i class="bi bi-globe"></i> Country Dashboard
        </a>

        {{-- Menu Analitik --}}
        <div class="nav-section">Analitik Risiko</div>
        <a href="{{ route('risk') }}"
           class="nav-link {{ request()->routeIs('risk') ? 'active' : '' }}">
            <i class="bi bi-radar"></i> Risk Scoring
        </a>
        <a href="{{ route('compare') }}"
           class="nav-link {{ request()->routeIs('compare') ? 'active' : '' }}">
            <i class="bi bi-arrow-left-right"></i> Compare
        </a>
        <a href="{{ route('watchlist') }}"
           class="nav-link {{ request()->routeIs('watchlist') ? 'active' : '' }}">
            <i class="bi bi-star"></i> Watchlist
        </a>

        {{-- Menu Data --}}
        <div class="nav-section">Data Global</div>
        <a href="{{ route('weather') }}"
           class="nav-link {{ request()->routeIs('weather') ? 'active' : '' }}">
            <i class="bi bi-cloud-sun"></i> Cuaca Global
        </a>
        <a href="{{ route('economy') }}"
           class="nav-link {{ request()->routeIs('economy') ? 'active' : '' }}">
            <i class="bi bi-graph-up"></i> Ekonomi Dunia
        </a>
        <a href="{{ route('country') }}"
           class="nav-link {{ request()->routeIs('country') ? 'active' : '' }}">
            <i class="bi bi-flag"></i> Info Negara
        </a>
        <a href="{{ route('currency') }}"
           class="nav-link {{ request()->routeIs('currency') ? 'active' : '' }}">
            <i class="bi bi-currency-exchange"></i> Nilai Tukar
        </a>
        <a href="{{ route('port') }}"
           class="nav-link {{ request()->routeIs('port') ? 'active' : '' }}">
            <i class="bi bi-anchor"></i> Peta Pelabuhan
        </a>
        <a href="{{ route('news') }}"
           class="nav-link {{ request()->routeIs('news') ? 'active' : '' }}">
            <i class="bi bi-newspaper"></i> Berita Global
        </a>

        {{-- Admin Panel (hanya tampil jika role admin) --}}
        @if(auth()->check() && auth()->user()->role === 'admin')
        <hr>
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <i class="bi bi-shield-lock"></i> Admin Panel
        </a>
        @endif
    </nav>

    {{-- Sidebar Footer — Info User --}}
    <div class="sidebar-footer">
        {{-- User Card --}}
        @auth
        <div class="user-card mb-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:34px; height:34px; background:rgba(255,255,255,0.25);">
                    <span style="color:white; font-weight:700; font-size:0.9rem;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                </div>
                <div style="overflow:hidden; flex:1;">
                    <div style="font-size:0.8rem; font-weight:600; color:white;
                                white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ auth()->user()->name }}
                    </div>
                    <div style="font-size:0.65rem; color:rgba(255,255,255,0.6);">
                        {{ ucfirst(auth()->user()->role) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Profil & Logout --}}
        <a href="{{ route('profile.edit') }}"
           class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i> Profil Saya
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="nav-link w-100 text-start border-0 mt-1"
                    style="background:none; cursor:pointer;">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
        @endauth
    </div>
</div>

{{-- MAIN CONTENT --}}
<div class="main-content">
    {{-- Topbar --}}
    <div class="topbar">
        <div>
            <h5 class="mb-0 fw-bold">@yield('page-title', 'Dashboard')</h5>
            <small class="text-muted">Global Supply Chain Risk Monitoring Platform</small>
        </div>
        <div class="d-flex align-items-center gap-3">
            @auth
            <span class="badge {{ auth()->user()->role === 'admin' ? 'bg-danger' : 'bg-success' }}">
                {{ ucfirst(auth()->user()->role) }}
            </span>
            @endauth
            <div class="text-muted small">
                <i class="bi bi-clock"></i>
                <span id="current-time"></span>
            </div>
        </div>
    </div>

    @yield('content')
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    function updateTime() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleString('id-ID');
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>

@stack('scripts')
</body>
</html>