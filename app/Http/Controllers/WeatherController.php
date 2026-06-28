<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    // Daftar kota penting untuk rantai pasok global
    private $cities = [
        ['name' => 'Shanghai',     'lat' => 31.2304, 'lon' => 121.4737, 'country' => 'China'],
        ['name' => 'Singapore',    'lat' => 1.3521,  'lon' => 103.8198, 'country' => 'Singapore'],
        ['name' => 'Rotterdam',    'lat' => 51.9225, 'lon' => 4.4792,   'country' => 'Netherlands'],
        ['name' => 'Los Angeles',  'lat' => 34.0522, 'lon' => -118.2437,'country' => 'USA'],
        ['name' => 'Dubai',        'lat' => 25.2048, 'lon' => 55.2708,  'country' => 'UAE'],
        ['name' => 'Tokyo',        'lat' => 35.6762, 'lon' => 139.6503, 'country' => 'Japan'],
        ['name' => 'Hamburg',      'lat' => 53.5753, 'lon' => 10.0153,  'country' => 'Germany'],
        ['name' => 'Jakarta',      'lat' => -6.2088, 'lon' => 106.8456, 'country' => 'Indonesia'],
    ];

    public function index()
    {
        $weatherData = [];

        foreach ($this->cities as $city) {
            try {
                $response = Http::withoutVerifying()->timeout(30)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude'       => $city['lat'],
                    'longitude'      => $city['lon'],
                    'current'        => 'temperature_2m,relative_humidity_2m,wind_speed_10m,precipitation,weathercode',
                    'timezone'       => 'auto',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $current = $data['current'];

                    $weatherData[] = [
                        'city'        => $city['name'],
                        'country'     => $city['country'],
                        'temp'        => $current['temperature_2m'],
                        'humidity'    => $current['relative_humidity_2m'],
                        'wind'        => $current['wind_speed_10m'],
                        'rain'        => $current['precipitation'],
                        'code'        => $current['weathercode'],
                        'icon'        => $this->getWeatherIcon($current['weathercode']),
                        'condition'   => $this->getWeatherCondition($current['weathercode']),
                        'risk'        => $this->getRiskLevel($current['weathercode'], $current['wind_speed_10m'], $current['precipitation']),
                    ];
                }
            } catch (\Exception $e) {
                $weatherData[] = [
                    'city'      => $city['name'],
                    'country'   => $city['country'],
                    'temp'      => 'N/A',
                    'humidity'  => 'N/A',
                    'wind'      => 'N/A',
                    'rain'      => 'N/A',
                    'code'      => 0,
                    'icon'      => '❓',
                    'condition' => 'Data tidak tersedia',
                    'risk'      => 'unknown',
                ];
            }
        }

        return view('weather.index', compact('weatherData'));
    }

    // Konversi kode cuaca ke emoji
    private function getWeatherIcon($code)
    {
        if ($code == 0)               return '☀️';
        if (in_array($code, [1,2,3])) return '⛅';
        if (in_array($code, [45,48])) return '🌫️';
        if (in_array($code, [51,53,55,61,63,65])) return '🌧️';
        if (in_array($code, [71,73,75])) return '❄️';
        if (in_array($code, [80,81,82])) return '🌦️';
        if (in_array($code, [95,96,99])) return '⛈️';
        return '🌤️';
    }

    // Konversi kode cuaca ke deskripsi
    private function getWeatherCondition($code)
    {
        if ($code == 0)               return 'Cerah';
        if (in_array($code, [1,2,3])) return 'Berawan';
        if (in_array($code, [45,48])) return 'Berkabut';
        if (in_array($code, [51,53,55])) return 'Gerimis';
        if (in_array($code, [61,63,65])) return 'Hujan';
        if (in_array($code, [71,73,75])) return 'Salju';
        if (in_array($code, [80,81,82])) return 'Hujan Lebat';
        if (in_array($code, [95,96,99])) return 'Badai Petir';
        return 'Tidak Diketahui';
    }

    // Hitung level risiko berdasarkan cuaca
    private function getRiskLevel($code, $wind, $rain)
    {
        if (in_array($code, [95,96,99]) || $wind > 50 || $rain > 10) return 'high';
        if (in_array($code, [61,63,65,80,81,82]) || $wind > 30)      return 'medium';
        return 'low';
    }
}