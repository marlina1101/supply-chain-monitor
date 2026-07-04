<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // Halaman utama admin

public function index()
{
    $totalPorts     = DB::table('ports')->count();
    $totalArticles  = DB::table('articles')->count();
    $totalWatchlist = DB::table('watchlists')->count();
    $totalNewsCache = DB::table('news_cache')->count();
    $totalUsers     = DB::table('users')->count();

    $recentArticles = DB::table('articles')->orderBy('created_at', 'desc')->limit(5)->get();
    $ports          = DB::table('ports')->orderBy('id', 'desc')->get();
    $users          = DB::table('users')->orderBy('created_at', 'desc')->get();

    return view('admin.index', compact(
        'totalPorts', 'totalArticles', 'totalWatchlist',
        'totalNewsCache', 'totalUsers', 'recentArticles', 'ports', 'users'
    ));
}

    }

    // ===== ARTIKEL =====
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
            'author'     => $request->author ?? 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin')->with('success', 'Artikel berhasil ditambahkan!');
    }

    public function deleteArticle($id)
    {
        DB::table('articles')->where('id', $id)->delete();
        return redirect()->route('admin')->with('success', 'Artikel dihapus.');
    }

    // ===== PELABUHAN =====
    public function storePort(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'country'   => 'required|string|max:100',
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

        return redirect()->route('admin')->with('success', 'Pelabuhan berhasil ditambahkan!');
    }

    public function storeUser(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ]);

    DB::table('users')->insert([
        'name'       => $request->name,
        'email'      => $request->email,
        'password'   => bcrypt($request->password),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('admin')->with('success', 'User berhasil ditambahkan!');
}

    public function deletePort($id)
    {
        DB::table('ports')->where('id', $id)->delete();
        return redirect()->route('admin')->with('success', 'Pelabuhan dihapus.');
    }
}