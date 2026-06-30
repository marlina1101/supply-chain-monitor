<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\RiskCalculator;

class CompareController extends Controller
{
    private $countries = [
        'DE' => ['name' => 'Germany',     'lat' => 51.16,  'lon' => 10.45],
        'CN' => ['name' => 'China',       'lat' => 35.86,  'lon' => 104.19],
        'ID' => ['name' => 'Indonesia',   'lat' => -0.78,  'lon' => 113.92],
        'US' => ['name' => 'USA',         'lat' => 37.09,  'lon' => -95.71],
        'JP' => ['name' => 'Japan',       'lat' => 36.20,  'lon' => 138.25],
        'IN' => ['name' => 'India',       'lat' => 20.59,  'lon' => 78.96],
        'GB' => ['name' => 'UK',          'lat' => 55.37,  'lon' => -3.43],
        'AU' => ['name' => 'Australia',   'lat' => -25.27, 'lon' => 133.77],
        'BR' => ['name' => 'Brazil',      'lat' => -14.23, 'lon' => -51.92],
        'SG' => ['name' => 'Singapore',   'lat' => 1.35,   'lon' => 103.82],
        'KR' => ['name' => 'South Korea', 'lat' => 35.90,  'lon' => 127.76],
        'FR' => ['name' => 'France',      'lat' => 46.22,  'lon' => 2.21],
        'SA' => ['name' => 'Saudi Arabia','lat' => 23.88,  'lon' => 45.07],
        'MY' => ['name' => 'Malaysia',    'lat' => 4.21,   'lon' => 101.97],
    ];

    private $currencyMap = [
        'DE'=>'EUR','CN'=>'CNY','ID'=>'IDR','US'=>'USD',
        'JP'=>'JPY','IN'=>'INR','GB'=>'GBP','AU'=>'AUD',
        'BR'=>'BRL','SG'=>'SGD','KR'=>'KRW','FR'=>'EUR',
        'SA'=>'SAR','MY'=>'MYR',
    ];

    public function index(Request $request)
    {
        $code1 = $request->get('country1', 'US');
        $code2 = $request->get('country2', 'CN');

        $data1 = $this->getCountryData($code1);
        $data2 = $this->getCountryData($code2);

        $countries = $this->countries;

        return view('compare.index', compact('countries', 'code1', 'code2', 'data1', 'data2'));
    }

    private function getCountryData($code)
    {
        $country = $this->countries[$code] ?? $this->countries['US'];

        $result = [
            'code'          => $code,
            'name'          => $country['name'],
            'gdp'           => null,
            'inflation'     => null,
            'population'    => null,
            'temp'          => null,
            'weatherCode'   => 0,
            'windSpeed'     => 0,
            'exchangeRate'  => 1,
            'weatherRisk'   => 30,
            'inflationRisk' => 50,
            'currencyRisk'  => 20,
            'newsRisk'      => 40,
            'totalRisk'     => 0,
            'riskLevel'     => ['level' => 'N/A', 'class' => 'secondary', 'color' => '#666'],
            'sentiment'     => 'neutral',
        ];

        // GDP
        try {
            $res = Http::withoutVerifying()->timeout(15)
                ->get("https://api.worldbank.org/v2/country/{$code}/indicator/NY.GDP.MKTP.CD", [
                    'format' => 'json', 'mrv' => 1, 'per_page' => 1
                ]);
            if ($res->successful() && isset($res->json()[1][0]['value'])) {
                $result['gdp'] = $res->json()[1][0]['value'];
            }
        } catch (\Exception $e) {}

        // Inflasi
        try {
            $res = Http::withoutVerifying()->timeout(15)
                ->get("https://api.worldbank.org/v2/country/{$code}/indicator/FP.CPI.TOTL.ZG", [
                    'format' => 'json', 'mrv' => 1, 'per_page' => 1
                ]);
            if ($res->successful() && isset($res->json()[1][0]['value'])) {
                $result['inflation'] = $res->json()[1][0]['value'];
            }
        } catch (\Exception $e) {}

        // Populasi
        try {
            $res = Http::withoutVerifying()->timeout(15)
                ->get("https://api.worldbank.org/v2/country/{$code}/indicator/SP.POP.TOTL", [
                    'format' => 'json', 'mrv' => 1, 'per_page' => 1
                ]);
            if ($res->successful() && isset($res->json()[1][0]['value'])) {
                $result['population'] = $res->json()[1][0]['value'];
            }
        } catch (\Exception $e) {}

        // Cuaca
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
                $result['temp']        = $cur['temperature_2m'];
                $result['weatherCode'] = $cur['weathercode'];
                $result['windSpeed']   = $cur['wind_speed_10m'];
                $result['weatherRisk'] = RiskCalculator::weatherRisk($cur['weathercode'], $cur['wind_speed_10m']);
            }
        } catch (\Exception $e) {}

        // Currency
        try {
            $apiKey = env('EXCHANGE_RATE_API_KEY');
            $res = Http::withoutVerifying()->timeout(15)
                ->get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD");
            if ($res->successful()) {
                $rates = $res->json()['conversion_rates'];
                $currency = $this->currencyMap[$code] ?? 'USD';
                $result['exchangeRate'] = $rates[$currency] ?? 1;
                $result['currencyRisk'] = RiskCalculator::currencyRisk($result['exchangeRate'], 1.0);
            }
        } catch (\Exception $e) {}

        // Inflation risk
        $result['inflationRisk'] = RiskCalculator::inflationRisk($result['inflation']);

        // Total risk
        $result['totalRisk'] = RiskCalculator::calculate(
            $result['weatherRisk'],
            $result['inflationRisk'],
            $result['currencyRisk'],
            $result['newsRisk']
        );
        $result['riskLevel'] = RiskCalculator::getLevel($result['totalRisk']);

        return $result;
    }
}