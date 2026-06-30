<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GNEWS_API_KEY');
    }

    public function index(Request $request)
    {
        $category = $request->get('category', 'economy');
        $news = [];
        $error = null;

        $categories = [
            'economy'     => '💰 Ekonomi Global',
            'logistics'   => '🚢 Logistik & Rantai Pasok',
            'geopolitics' => '🌍 Geopolitik',
            'trade'       => '📦 Perdagangan Internasional',
        ];

        $queries = [
            'economy'     => 'global economy trade',
            'logistics'   => 'supply chain logistics shipping',
            'geopolitics' => 'geopolitics conflict international',
            'trade'       => 'international trade export import',
        ];

        try {
            $query = $queries[$category] ?? 'global economy';

            $response = Http::withoutVerifying()->timeout(30)
                ->get('https://gnews.io/api/v4/search', [
                    'q'        => $query,
                    'lang'     => 'en',
                    'country'  => 'any',
                    'max'      => 10,
                    'apikey'   => $this->apiKey,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $news = $data['articles'] ?? [];
            } else {
                $error = 'Gagal mengambil berita. Status: ' . $response->status();
            }
        } catch (\Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }

        return view('news.index', compact('news', 'categories', 'category', 'error'));
    }

    public function api(Request $request)
{
    $category = $request->get('category', 'economy');

    $queries = [
        'economy'     => 'global economy trade',
        'logistics'   => 'supply chain logistics shipping',
        'geopolitics' => 'geopolitics conflict international',
        'trade'       => 'international trade export import',
    ];

    $news = [];
    try {
        $query = $queries[$category] ?? 'global economy';
        $response = Http::withoutVerifying()->timeout(30)
            ->get('https://gnews.io/api/v4/search', [
                'q' => $query, 'lang' => 'en', 'max' => 10,
                'apikey' => $this->apiKey,
            ]);
        if ($response->successful()) {
            $news = $response->json()['articles'] ?? [];
        }
    } catch (\Exception $e) {}

    return response()->json([
        'success'  => true,
        'category' => $category,
        'total'    => count($news),
        'data'     => $news,
    ]);
}

}