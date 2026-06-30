<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\RiskCalculator;
use App\Services\SentimentAnalyzer;

class RiskController extends Controller
{
    private $countries = [
        'DE' => ['name' => 'Germany',    'lat' => 51.16, 'lon' => 10.45],
        'CN' => ['name' => 'China',      'lat' => 35.86, 'lon' => 104.19],
        'ID' => ['name' => 'Indonesia',  'lat' => -0.78, 'lon' => 113.92],
        'US' => ['name' => 'USA',        'lat' => 37.09, 'lon' => -95.71],
        'JP' => ['name' => 'Japan',      'lat' => 36.20, 'lon' => 138.25],
        'IN' => ['name' => 'India',      'lat' => 20.59, 'lon' => 78.96],
        'GB' => ['name' => 'UK',         'lat' => 55.37, 'lon' => -3.43],
        'AU' => ['name' => 'Australia',  'lat' => -25.27,'lon' => 133.77],
        'BR' => ['name' => 'Brazil',     'lat' => -14.23,'lon' => -51.92],
        'SG' => ['name' => 'Singapore',  'lat' => 1.35,  'lon' => 103.82],
    ];

    public function index(Request $request)
    {
        $selected = $request->get('country', 'ID');
        $countries = $this->countries;
        $country  = $this->countries[$selected] ?? $this->countries['ID'];

        // 1. Weather risk
        $weatherRisk = 30; $weatherCode = 0; $windSpeed = 0; $temp = 0;
        try {
            $res = Http::withoutVerifying()->timeout(15)
                ->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude'  => $country['lat'],
                    'longitude' => $country['lon'],
                    'current'   => 'temperature_2m,wind_speed_10m,weathercode',
                    'timezone'  => 'auto',
                ]);
            if ($res->successful()) {
                $cur = $res->json()['current'];
                $weatherCode = $cur['weathercode'];
                $windSpeed   = $cur['wind_speed_10m'];
                $temp        = $cur['temperature_2m'];
                $weatherRisk = RiskCalculator::weatherRisk($weatherCode, $windSpeed);
            }
        } catch (\Exception $e) {}

        // 2. Inflation risk
        $inflationRisk = 50; $inflationRate = null;
        try {
            $res = Http::withoutVerifying()->timeout(15)
                ->get("https://api.worldbank.org/v2/country/{$selected}/indicator/FP.CPI.TOTL.ZG", [
                    'format' => 'json', 'mrv' => 1, 'per_page' => 1
                ]);
            if ($res->successful() && isset($res->json()[1][0]['value'])) {
                $inflationRate = $res->json()[1][0]['value'];
                $inflationRisk = RiskCalculator::inflationRisk($inflationRate);
            }
        } catch (\Exception $e) {}

        // 3. Currency risk
        $currencyRisk = 20; $exchangeRate = 1;
        try {
            $apiKey = env('EXCHANGE_RATE_API_KEY');
            $res = Http::withoutVerifying()->timeout(15)
                ->get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD");
            if ($res->successful()) {
                $rates = $res->json()['conversion_rates'];
                // Cari mata uang negara
                $currencyMap = ['DE'=>'EUR','CN'=>'CNY','ID'=>'IDR','US'=>'USD',
                                'JP'=>'JPY','IN'=>'INR','GB'=>'GBP','AU'=>'AUD',
                                'BR'=>'BRL','SG'=>'SGD'];
                $code = $currencyMap[$selected] ?? 'USD';
                $exchangeRate = $rates[$code] ?? 1;
                $currencyRisk = RiskCalculator::currencyRisk($exchangeRate, 1.0);
            }
        } catch (\Exception $e) {}

        // 4. News sentiment risk
        $newsRisk = 40; $sentiment = ['sentiment' => 'neutral', 'score' => 0, 'positive' => 0, 'negative' => 0, 'risk_score' => 40];
        try {
            $apiKey = env('GNEWS_API_KEY');
            $res = Http::withoutVerifying()->timeout(15)
                ->get('https://gnews.io/api/v4/search', [
                    'q'      => $country['name'] . ' economy trade',
                    'lang'   => 'en',
                    'max'    => 5,
                    'apikey' => $apiKey,
                ]);
            if ($res->successful()) {
                $articles = $res->json()['articles'] ?? [];
                $allText  = '';
                foreach ($articles as $a) {
                    $allText .= ' ' . ($a['title'] ?? '') . ' ' . ($a['description'] ?? '');
                }
                $sentiment = SentimentAnalyzer::analyze($allText);
                $newsRisk  = $sentiment['risk_score'];
            }
        } catch (\Exception $e) {}

        // 5. Hitung total risk
        $totalRisk  = RiskCalculator::calculate($weatherRisk, $inflationRisk, $currencyRisk, $newsRisk);
        $riskLevel  = RiskCalculator::getLevel($totalRisk);

        return view('risk.index', compact(
            'countries', 'selected', 'country',
            'weatherRisk', 'inflationRisk', 'currencyRisk', 'newsRisk',
            'totalRisk', 'riskLevel', 'sentiment',
            'weatherCode', 'windSpeed', 'temp', 'inflationRate', 'exchangeRate'
        ));
    }

    public function api(Request $request)
{
    $selected = $request->get('country', 'ID');

    // Reuse logic dari index() — panggil ulang dengan request yang sama
    $response = $this->index($request);
    $data = $response->getData();

    return response()->json([
        'success' => true,
        'country' => $data['country']['name'] ?? $selected,
        'risk_score' => $data['totalRisk'] ?? 0,
        'risk_level' => $data['riskLevel']['level'] ?? 'N/A',
        'breakdown' => [
            'weather'   => round($data['weatherRisk'] ?? 0, 1),
            'inflation' => round($data['inflationRisk'] ?? 0, 1),
            'currency'  => round($data['currencyRisk'] ?? 0, 1),
            'news'      => round($data['newsRisk'] ?? 0, 1),
        ],
    ]);
}

}