<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\RiskCalculator;

class GlobalCountryController extends Controller
{
    private $countries = [
        'DE' => ['name' => 'Germany',     'lat' => 51.16,  'lon' => 10.45,  'currency' => 'EUR'],
        'CN' => ['name' => 'China',       'lat' => 35.86,  'lon' => 104.19, 'currency' => 'CNY'],
        'ID' => ['name' => 'Indonesia',   'lat' => -0.78,  'lon' => 113.92, 'currency' => 'IDR'],
        'US' => ['name' => 'USA',         'lat' => 37.09,  'lon' => -95.71, 'currency' => 'USD'],
        'JP' => ['name' => 'Japan',       'lat' => 36.20,  'lon' => 138.25, 'currency' => 'JPY'],
        'IN' => ['name' => 'India',       'lat' => 20.59,  'lon' => 78.96,  'currency' => 'INR'],
        'GB' => ['name' => 'UK',          'lat' => 55.37,  'lon' => -3.43,  'currency' => 'GBP'],
        'AU' => ['name' => 'Australia',   'lat' => -25.27, 'lon' => 133.77, 'currency' => 'AUD'],
        'BR' => ['name' => 'Brazil',      'lat' => -14.23, 'lon' => -51.92, 'currency' => 'BRL'],
        'SG' => ['name' => 'Singapore',   'lat' => 1.35,   'lon' => 103.82, 'currency' => 'SGD'],
        'KR' => ['name' => 'South Korea', 'lat' => 35.90,  'lon' => 127.76, 'currency' => 'KRW'],
        'FR' => ['name' => 'France',      'lat' => 46.22,  'lon' => 2.21,   'currency' => 'EUR'],
        'SA' => ['name' => 'Saudi Arabia','lat' => 23.88,  'lon' => 45.07,  'currency' => 'SAR'],
        'MY' => ['name' => 'Malaysia',    'lat' => 4.21,   'lon' => 101.97, 'currency' => 'MYR'],
    ];

    public function index(Request $request)
    {
        $selected = $request->get('country', 'ID');
        $country  = $this->countries[$selected] ?? $this->countries['ID'];
        $countries = $this->countries;

        $data = [
            'gdp'        => null,
            'inflation'  => null,
            'population' => null,
            'temp'       => null,
            'weatherCode'=> 0,
            'windSpeed'  => 0,
            'condition'  => 'N/A',
            'icon'       => '🌤️',
            'currency'   => $country['currency'],
            'exchangeRate'=> null,
            'riskScore'  => null,
            'riskLevel'  => null,
        ];

        // GDP
        try {
            $res = Http::withoutVerifying()->timeout(15)
                ->get("https://api.worldbank.org/v2/country/{$selected}/indicator/NY.GDP.MKTP.CD", [
                    'format' => 'json', 'mrv' => 1, 'per_page' => 1
                ]);
            if ($res->successful() && isset($res->json()[1][0]['value'])) {
                $data['gdp'] = $res->json()[1][0]['value'];
            }
        } catch (\Exception $e) {}

        // Inflasi
        try {
            $res = Http::withoutVerifying()->timeout(15)
                ->get("https://api.worldbank.org/v2/country/{$selected}/indicator/FP.CPI.TOTL.ZG", [
                    'format' => 'json', 'mrv' => 1, 'per_page' => 1
                ]);
            if ($res->successful() && isset($res->json()[1][0]['value'])) {
                $data['inflation'] = $res->json()[1][0]['value'];
            }
        } catch (\Exception $e) {}

        // Populasi
        try {
            $res = Http::withoutVerifying()->timeout(15)
                ->get("https://api.worldbank.org/v2/country/{$selected}/indicator/SP.POP.TOTL", [
                    'format' => 'json', 'mrv' => 1, 'per_page' => 1
                ]);
            if ($res->successful() && isset($res->json()[1][0]['value'])) {
                $data['population'] = $res->json()[1][0]['value'];
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
                $data['temp']        = $cur['temperature_2m'];
                $data['weatherCode'] = $cur['weathercode'];
                $data['windSpeed']   = $cur['wind_speed_10m'];
                $data['icon']        = $this->getIcon($cur['weathercode']);
                $data['condition']   = $this->getCondition($cur['weathercode']);
            }
        } catch (\Exception $e) {}

        // Nilai tukar
        try {
            $apiKey = env('EXCHANGE_RATE_API_KEY');
            $res = Http::withoutVerifying()->timeout(15)
                ->get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD");
            if ($res->successful()) {
                $rates = $res->json()['conversion_rates'];
                $data['exchangeRate'] = $rates[$country['currency']] ?? null;
            }
        } catch (\Exception $e) {}

        // Risk Score
        $weatherRisk   = RiskCalculator::weatherRisk($data['weatherCode'], $data['windSpeed']);
        $inflationRisk = RiskCalculator::inflationRisk($data['inflation']);
        $currencyRisk  = RiskCalculator::currencyRisk($data['exchangeRate'] ?? 1, 1.0);
        $data['riskScore'] = RiskCalculator::calculate($weatherRisk, $inflationRisk, $currencyRisk, 40);
        $data['riskLevel'] = RiskCalculator::getLevel($data['riskScore']);

        return view('globalcountry.index', compact('countries', 'selected', 'country', 'data'));
    }

    private function getIcon($code)
    {
        if ($code == 0) return '☀️';
        if (in_array($code, [1,2,3])) return '⛅';
        if (in_array($code, [45,48])) return '🌫️';
        if (in_array($code, [51,53,55,61,63,65])) return '🌧️';
        if (in_array($code, [80,81,82])) return '🌦️';
        if (in_array($code, [95,96,99])) return '⛈️';
        return '🌤️';
    }

    private function getCondition($code)
    {
        if ($code == 0) return 'Cerah';
        if (in_array($code, [1,2,3])) return 'Berawan';
        if (in_array($code, [45,48])) return 'Berkabut';
        if (in_array($code, [51,53,55])) return 'Gerimis';
        if (in_array($code, [61,63,65])) return 'Hujan';
        if (in_array($code, [80,81,82])) return 'Hujan Lebat';
        if (in_array($code, [95,96,99])) return 'Badai Petir';
        return 'N/A';
    }
}