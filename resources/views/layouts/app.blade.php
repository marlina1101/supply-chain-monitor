<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Chain Monitor</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Leaflet CSS (untuk peta) --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <style>
        body { background-color: #f0f2f5; }

        /* Sidebar */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #1a237e 0%, #283593 100%);
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            padding-top: 20px;
        }
        .sidebar .brand {
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            padding: 10px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 10px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 10px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
        }
        .sidebar .nav-link i { margin-right: 8px; width: 20px; }

        /* Main content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        /* Topbar */
        .topbar {
            background: white;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            color: #1a237e;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8eaf6;
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<div class="sidebar">
    <div class="brand">
        <i class="bi bi-globe2"></i> Supply Chain<br>
        <small style="font-weight:400; font-size:0.75rem; opacity:0.7">Risk Monitor</small>
    </div>
    <nav class="nav flex-column">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('weather') }}" class="nav-link {{ request()->routeIs('weather') ? 'active' : '' }}">
            <i class="bi bi-cloud-sun"></i> Cuaca Global
        </a>
        <a href="{{ route('economy') }}" class="nav-link {{ request()->routeIs('economy') ? 'active' : '' }}">
            <i class="bi bi-graph-up"></i> Ekonomi Dunia
        </a>
        <a href="{{ route('country') }}" class="nav-link {{ request()->routeIs('country') ? 'active' : '' }}">
            <i class="bi bi-flag"></i> Info Negara
        </a>
        <a href="{{ route('currency') }}" class="nav-link {{ request()->routeIs('currency') ? 'active' : '' }}">
            <i class="bi bi-currency-exchange"></i> Nilai Tukar
        </a>
        <a href="{{ route('port') }}" class="nav-link {{ request()->routeIs('port') ? 'active' : '' }}">
            <i class="bi bi-anchor"></i> Peta Pelabuhan
        </a>
        <a href="{{ route('news') }}" class="nav-link {{ request()->routeIs('news') ? 'active' : '' }}">
            <i class="bi bi-newspaper"></i> Berita Global
        </a>
    </nav>
</div>

{{-- MAIN CONTENT --}}
<div class="main-content">
    {{-- Topbar --}}
    <div class="topbar">
        <div>
            <h5 class="mb-0 fw-bold">@yield('page-title', 'Dashboard')</h5>
            <small class="text-muted">Global Supply Chain Risk Monitoring Platform</small>
        </div>
        <div class="text-muted small">
            <i class="bi bi-clock"></i>
            <span id="current-time"></span>
        </div>
    </div>

    {{-- Konten halaman --}}
    @yield('content')
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Jam real-time di topbar
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