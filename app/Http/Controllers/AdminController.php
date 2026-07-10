<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ===== DASHBOARD =====
    public function index()
    {
        $stats = [
            'total_users'    => DB::table('users')->count(),
            'total_articles' => DB::table('articles')->count(),
            'total_ports'    => DB::table('ports')->count(),
            'total_watchlist'=> DB::table('watchlists')->count(),
            'admin_count'    => DB::table('users')->where('role', 'admin')->count(),
            'user_count'     => DB::table('users')->where('role', 'user')->count(),
            'active_users'   => DB::table('users')->where('is_active', true)->count(),
        ];

        $recentUsers    = DB::table('users')->orderBy('created_at', 'desc')->limit(5)->get();
        $recentArticles = DB::table('articles')->orderBy('created_at', 'desc')->limit(5)->get();
        $recentLogs     = DB::table('activity_logs')->orderBy('created_at', 'desc')->limit(10)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentArticles', 'recentLogs'));
    }

    // ===== USERS =====
    public function users()
    {
        $users = DB::table('users')->orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,user',
        ]);

        DB::table('users')->insert([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->log('Tambah User', "Menambahkan user: {$request->email}");
        return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan!');
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email',
            'role'  => 'required|in:admin,user',
        ]);

        $data = [
            'name'       => $request->name,
            'email'      => $request->email,
            'role'       => $request->role,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $id)->update($data);
        $this->log('Edit User', "Mengubah data user ID: {$id}");
        return redirect()->route('admin.users')->with('success', 'User berhasil diperbarui!');
    }

    public function deleteUser($id)
    {
        if ($id == auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'Tidak bisa menghapus akun sendiri!');
        }
        DB::table('users')->where('id', $id)->delete();
        $this->log('Hapus User', "Menghapus user ID: {$id}");
        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus!');
    }

    public function toggleUser($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        $newStatus = !$user->is_active;
        DB::table('users')->where('id', $id)->update(['is_active' => $newStatus]);
        $status = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        $this->log('Toggle User', "User ID {$id} {$status}");
        return redirect()->route('admin.users')->with('success', "User berhasil {$status}!");
    }

    // ===== ARTICLES =====
    public function articles()
    {
        $articles = DB::table('articles')->orderBy('created_at', 'desc')->get();
        return view('admin.articles', compact('articles'));
    }

    public function storeArticle(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'required|string',
        ]);

        DB::table('articles')->insert([
            'title'      => $request->title,
            'content'    => $request->content,
            'category'   => $request->category,
            'author'     => auth()->user()->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->log('Tambah Artikel', "Menambahkan artikel: {$request->title}");
        return redirect()->route('admin.articles')->with('success', 'Artikel berhasil ditambahkan!');
    }

    public function updateArticle(Request $request, $id)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'required|string',
        ]);

        DB::table('articles')->where('id', $id)->update([
            'title'      => $request->title,
            'content'    => $request->content,
            'category'   => $request->category,
            'updated_at' => now(),
        ]);

        $this->log('Edit Artikel', "Mengubah artikel ID: {$id}");
        return redirect()->route('admin.articles')->with('success', 'Artikel berhasil diperbarui!');
    }

    public function deleteArticle($id)
    {
        DB::table('articles')->where('id', $id)->delete();
        $this->log('Hapus Artikel', "Menghapus artikel ID: {$id}");
        return redirect()->route('admin.articles')->with('success', 'Artikel berhasil dihapus!');
    }

    // ===== PORTS =====
    public function ports()
    {
        $ports = DB::table('ports')->orderBy('id', 'desc')->get();
        return view('admin.ports', compact('ports'));
    }

    public function storePort(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'country'   => 'required|string',
            'region'    => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'volume'    => 'required|numeric',
            'status'    => 'required|string',
        ]);

        DB::table('ports')->insert([
            'name'       => $request->name,
            'country'    => $request->country,
            'region'     => $request->region,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'volume'     => $request->volume,
            'status'     => $request->status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->log('Tambah Pelabuhan', "Menambahkan pelabuhan: {$request->name}");
        return redirect()->route('admin.ports')->with('success', 'Pelabuhan berhasil ditambahkan!');
    }

    public function updatePort(Request $request, $id)
    {
        DB::table('ports')->where('id', $id)->update([
            'name'       => $request->name,
            'country'    => $request->country,
            'region'     => $request->region,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'volume'     => $request->volume,
            'status'     => $request->status,
            'updated_at' => now(),
        ]);

        $this->log('Edit Pelabuhan', "Mengubah pelabuhan ID: {$id}");
        return redirect()->route('admin.ports')->with('success', 'Pelabuhan berhasil diperbarui!');
    }

    public function deletePort($id)
    {
        DB::table('ports')->where('id', $id)->delete();
        $this->log('Hapus Pelabuhan', "Menghapus pelabuhan ID: {$id}");
        return redirect()->route('admin.ports')->with('success', 'Pelabuhan berhasil dihapus!');
    }

    // ===== API MONITOR =====
    public function apiMonitor()
    {
        $apis = [
            [
                'name'     => 'Open-Meteo (Cuaca)',
                'url'      => 'https://api.open-meteo.com/v1/forecast?latitude=0&longitude=0&current=temperature_2m',
                'icon'     => '🌤️',
            ],
            [
                'name'     => 'World Bank (Ekonomi)',
                'url'      => 'https://api.worldbank.org/v2/country/ID/indicator/NY.GDP.MKTP.CD?format=json&mrv=1&per_page=1',
                'icon'     => '🏦',
            ],
            [
    'name' => 'CountriesNow (Negara)',
    'url'  => 'https://countriesnow.space/api/v0.1/countries',
    'icon' => '🌍',
],
            [
                'name'     => 'ExchangeRate (Kurs)',
                'url'      => "https://v6.exchangerate-api.com/v6/" . env('EXCHANGE_RATE_API_KEY') . "/latest/USD",
                'icon'     => '💱',
            ],
            [
                'name'     => 'GNews (Berita)',
                'url'      => "https://gnews.io/api/v4/search?q=economy&max=1&apikey=" . env('GNEWS_API_KEY'),
                'icon'     => '📰',
            ],
            [
                'name'     => 'OpenStreetMap (Peta)',
                'url'      => 'https://tile.openstreetmap.org/1/0/0.png',
                'icon'     => '🗺️',
            ],

[
        'name' => 'World Port Index (Pelabuhan)',
        'url'  => 'https://api.worldbank.org/v2/country/ID/indicator/NY.GDP.MKTP.CD?format=json&mrv=1&per_page=1',
        'icon' => '⚓',
    ],

        ];

        $results = [];
        foreach ($apis as $api) {
            try {
                $start    = microtime(true);
                $response = Http::withoutVerifying()->timeout(10)->get($api['url']);
                $time     = round((microtime(true) - $start) * 1000);

                $results[] = [
                    'name'     => $api['name'],
                    'icon'     => $api['icon'],
                    'status'   => $response->successful() ? 'online' : 'error',
                    'code'     => $response->status(),
                    'time'     => $time,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'name'   => $api['name'],
                    'icon'   => $api['icon'],
                    'status' => 'offline',
                    'code'   => 0,
                    'time'   => 0,
                ];
            }
        }

        return view('admin.api-monitor', compact('results'));
    }

    // ===== AUDIT LOG =====
    public function auditLog()
    {
        $logs = DB::table('activity_logs')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.audit-log', compact('logs'));
    }

    // ===== SETTINGS =====
    public function settings()
    {
        $settings = DB::table('system_settings')->get()->keyBy('key');
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->except(['_token', '_method']) as $key => $value) {
            DB::table('system_settings')
                ->where('key', $key)
                ->update(['value' => $value, 'updated_at' => now()]);
        }

        $this->log('Update Settings', 'Mengubah pengaturan sistem');
        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil disimpan!');
    }

    // ===== HELPER LOG =====
    private function log($action, $description = '')
    {
        DB::table('activity_logs')->insert([
            'action'      => $action,
            'description' => $description,
            'ip_address'  => request()->ip(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}