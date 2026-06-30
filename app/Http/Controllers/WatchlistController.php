<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\RiskCalculator;

class WatchlistController extends Controller
{
    private $countries = [
        'DE' => 'Germany', 'CN' => 'China', 'ID' => 'Indonesia', 'US' => 'USA',
        'JP' => 'Japan', 'IN' => 'India', 'GB' => 'UK', 'AU' => 'Australia',
        'BR' => 'Brazil', 'SG' => 'Singapore', 'KR' => 'South Korea', 'FR' => 'France',
        'SA' => 'Saudi Arabia', 'MY' => 'Malaysia',
    ];

    public function index(Request $request)
    {
        $sessionId = session()->getId();

        $watchlist = DB::table('watchlists')
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->get();

        $countries = $this->countries;

        // Hitung risk score singkat untuk setiap negara di watchlist
        $watchlistWithRisk = [];
        foreach ($watchlist as $item) {
            $watchlistWithRisk[] = [
                'id'           => $item->id,
                'country_code' => $item->country_code,
                'country_name' => $item->country_name,
                'added_at'     => $item->created_at,
            ];
        }

        return view('watchlist.index', compact('watchlistWithRisk', 'countries'));
    }

    public function add(Request $request)
    {
        $sessionId = session()->getId();
        $code      = $request->input('country_code');
        $name      = $this->countries[$code] ?? $code;

        // Cek duplikasi
        $exists = DB::table('watchlists')
            ->where('session_id', $sessionId)
            ->where('country_code', $code)
            ->exists();

        if (!$exists) {
            DB::table('watchlists')->insert([
                'session_id'   => $sessionId,
                'country_code' => $code,
                'country_name' => $name,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
            return redirect()->back()->with('success', "{$name} ditambahkan ke watchlist!");
        }

        return redirect()->back()->with('info', "{$name} sudah ada di watchlist.");
    }

    public function remove($id)
    {
        DB::table('watchlists')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Negara dihapus dari watchlist.');
    }
}