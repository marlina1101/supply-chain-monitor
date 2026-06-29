<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== 1. CUACA — ambil 4 kota =====
        $weatherSummary = [];
        $cities = [
            ['name' => 'Shanghai',   'lat' => 31.2304,  'lon' => 121.4737],
            ['name' => 'Singapore',  'lat' => 1.3521,   'lon' => 103.8198],
            ['name' => 'Rotterdam',  'lat' => 51.9225,  'lon' => 4.4792],
            ['name' => 'Dubai',      'lat' => 25.2048,  'lon' => 55.2708],
        ];

        foreach ($cities as $city) {
            try {
                $res = Http::withoutVerifying()->timeout(15)
                    ->get('https://api.open-meteo.com/v1/forecast', [
                        'latitude'  => $city['lat'],
                        'longitude' => $city['lon'],
                        'current'   => 'temperature_2m,weathercode',
                        'timezone'  => 'auto',
                    ]);
                if ($res->successful()) {
                    $current = $res->json()['current'];
                    $weatherSummary[] = [
                        'city' => $city['name'],
                        'temp' => $current['temperature_2m'],
                        'code' => $current['weathercode'],
                        'icon' => $this->getWeatherIcon($current['weathercode']),
                    ];
                }
            } catch (\Exception $e) {}
        }

        // ===== 2. NILAI TUKAR — USD ke mata uang utama =====
        $currencySummary = [];
        try {
            $apiKey = env('EXCHANGE_RATE_API_KEY');
            $res = Http::withoutVerifying()->timeout(15)
                ->get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD");
            if ($res->successful()) {
                $rates = $res->json()['conversion_rates'];
                $selected = ['EUR', 'CNY', 'JPY', 'IDR', 'SGD', 'GBP'];
                foreach ($selected as $code) {
                    if (isset($rates[$code])) {
                        $currencySummary[$code] = $rates[$code];
                    }
                }
            }
        } catch (\Exception $e) {}

        // ===== 3. BERITA — ambil 4 berita terbaru =====
        $newsSummary = [];
        try {
            $apiKey = env('GNEWS_API_KEY');
            $res = Http::withoutVerifying()->timeout(15)
                ->get('https://gnews.io/api/v4/search', [
                    'q'      => 'global supply chain trade',
                    'lang'   => 'en',
                    'max'    => 4,
                    'apikey' => $apiKey,
                ]);
            if ($res->successful()) {
                $newsSummary = $res->json()['articles'] ?? [];
            }
        } catch (\Exception $e) {}

        // ===== 4. PELABUHAN — data statis =====
        $portSummary = [
            ['name' => 'Port of Shanghai',   'country' => 'China',       'volume' => 47.3, 'status' => 'active'],
            ['name' => 'Port of Singapore',  'country' => 'Singapore',   'volume' => 37.5, 'status' => 'active'],
            ['name' => 'Port of Rotterdam',  'country' => 'Netherlands', 'volume' => 15.3, 'status' => 'active'],
            ['name' => 'Port of Dubai',      'country' => 'UAE',         'volume' => 14.4, 'status' => 'active'],
            ['name' => 'Port of Alexandria', 'country' => 'Egypt',       'volume' => 1.8,  'status' => 'busy'],
        ];

        // ===== 5. STATISTIK GLOBAL =====
        $globalStats = [
            'ports'     => 28,
            'countries' => 250,
            'weather'   => 8,
            'news'      => 'Real-time',
        ];

        return view('dashboard.index', compact(
            'weatherSummary',
            'currencySummary',
            'newsSummary',
            'portSummary',
            'globalStats'
        ));
    }

    private function getWeatherIcon($code)
    {
        if ($code == 0)                        return '☀️';
        if (in_array($code, [1, 2, 3]))        return '⛅';
        if (in_array($code, [45, 48]))         return '🌫️';
        if (in_array($code, [51,53,55,61,63,65])) return '🌧️';
        if (in_array($code, [71, 73, 75]))     return '❄️';
        if (in_array($code, [80, 81, 82]))     return '🌦️';
        if (in_array($code, [95, 96, 99]))     return '⛈️';
        return '🌤️';
    }
}