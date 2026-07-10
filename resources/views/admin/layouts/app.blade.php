<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — RiskRadar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .admin-sidebar {
            width: 260px;
            min-height: 100vh;
            max-height: 100vh;
            overflow-y: auto;
            background: linear-gradient(180deg, #1a237e 0%, #283593 100%);
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            padding-top: 0;
        }
        
        /* Styling scrollbar */
        .admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .admin-sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }

        .admin-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }

        .admin-sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .admin-brand {
            background: rgba(0,0,0,0.2);
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 10px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.2s;
            font-size: 0.9rem;
        }
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
        }
        .admin-sidebar .nav-link i { margin-right: 8px; width: 20px; }
        .admin-sidebar .nav-section {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px 20px 5px;
        }
        .admin-content { margin-left: 260px; padding: 20px; }
        .admin-topbar {
            background: white;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 4px solid #1a237e;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); }
        .section-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1a237e;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8eaf6;
        }
        .icon-box {
            width: 50px; height: 50px;
            border-radius: 10px;
            display: flex; align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ADMIN SIDEBAR --}}
<div class="admin-sidebar">
    <div class="admin-brand">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-shield-lock-fill text-warning fs-4"></i>
            <div>
                <div style="font-size:1rem; font-weight:700; color:white;">Admin Panel</div>
                <div style="font-size:0.65rem; color:rgba(255,255,255,0.5);">RiskRadar System</div>
            </div>
        </div>
    </div>

    <nav class="nav flex-column pt-2">
        <div class="nav-section">Main</div>
        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard Admin
        </a>

        <div class="nav-section">Manajemen</div>
        <a href="{{ route('admin.users') }}"
           class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Manajemen User
        </a>
        <a href="{{ route('admin.articles') }}"
           class="nav-link {{ request()->routeIs('admin.articles') ? 'active' : '' }}">
            <i class="bi bi-file-text"></i> Manajemen Artikel
        </a>
        <a href="{{ route('admin.ports') }}"
           class="nav-link {{ request()->routeIs('admin.ports') ? 'active' : '' }}">
            <i class="bi bi-anchor"></i> Manajemen Pelabuhan
        </a>

        <div class="nav-section">Monitoring</div>
        <a href="{{ route('admin.api.monitor') }}"
           class="nav-link {{ request()->routeIs('admin.api.monitor') ? 'active' : '' }}">
            <i class="bi bi-activity"></i> Monitor API
        </a>
        <a href="{{ route('admin.audit.log') }}"
           class="nav-link {{ request()->routeIs('admin.audit.log') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> Audit Log
        </a>

        <div class="nav-section">Sistem</div>
        <a href="{{ route('admin.settings') }}"
           class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Pengaturan
        </a>

        <hr style="border-color:rgba(255,255,255,0.1); margin: 10px 20px;">

        <a href="{{ route('dashboard') }}" class="nav-link">
            <i class="bi bi-arrow-left-circle"></i> Kembali ke Aplikasi
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mx-2 mt-1">
            @csrf
            <button type="submit" class="btn w-100 text-start nav-link"
                    style="background:none; border:none; color:rgba(255,255,255,0.6);">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </nav>
</div>

{{-- ADMIN CONTENT --}}
<div class="admin-content">
    <div class="admin-topbar">
        <div>
            <h5 class="mb-0 fw-bold">@yield('page-title', 'Admin Dashboard')</h5>
            <small class="text-muted">RiskRadar — Admin Panel</small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-danger">Admin</span>
            <span class="text-muted small">
                <i class="bi bi-person-circle"></i>
                {{ auth()->user()->name }}
            </span>
            <span class="text-muted small" id="admin-time"></span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function updateTime() {
        document.getElementById('admin-time').textContent =
            new Date().toLocaleString('id-ID');
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>
@stack('scripts')
</body>
</html>