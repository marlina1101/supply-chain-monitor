<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PortController extends Controller
{
    private $ports = [
        // Asia
        ['name' => 'Port of Shanghai',      'country' => 'China',        'lat' => 31.2304,  'lon' => 121.4737, 'volume' => 47.3,  'status' => 'active',   'region' => 'Asia'],
        ['name' => 'Port of Singapore',     'country' => 'Singapore',    'lat' => 1.2644,   'lon' => 103.8229, 'volume' => 37.5,  'status' => 'active',   'region' => 'Asia'],
        ['name' => 'Port of Ningbo',        'country' => 'China',        'lat' => 29.8683,  'lon' => 121.5440, 'volume' => 33.4,  'status' => 'active',   'region' => 'Asia'],
        ['name' => 'Port of Shenzhen',      'country' => 'China',        'lat' => 22.5431,  'lon' => 114.0579, 'volume' => 29.9,  'status' => 'active',   'region' => 'Asia'],
        ['name' => 'Port of Guangzhou',     'country' => 'China',        'lat' => 23.1291,  'lon' => 113.2644, 'volume' => 24.0,  'status' => 'active',   'region' => 'Asia'],
        ['name' => 'Port of Busan',         'country' => 'South Korea',  'lat' => 35.1796,  'lon' => 129.0756, 'volume' => 22.7,  'status' => 'active',   'region' => 'Asia'],
        ['name' => 'Port of Hong Kong',     'country' => 'Hong Kong',    'lat' => 22.3193,  'lon' => 114.1694, 'volume' => 18.0,  'status' => 'active',   'region' => 'Asia'],
        ['name' => 'Port of Tokyo',         'country' => 'Japan',        'lat' => 35.6762,  'lon' => 139.6910, 'volume' => 4.5,   'status' => 'active',   'region' => 'Asia'],
        ['name' => 'Port of Jakarta',       'country' => 'Indonesia',    'lat' => -6.1077,  'lon' => 106.8317, 'volume' => 7.6,   'status' => 'active',   'region' => 'Asia'],
        ['name' => 'Port of Klang',         'country' => 'Malaysia',     'lat' => 3.0319,   'lon' => 101.3903, 'volume' => 13.2,  'status' => 'active',   'region' => 'Asia'],
        ['name' => 'Port of Laem Chabang',  'country' => 'Thailand',     'lat' => 13.0820,  'lon' => 100.8812, 'volume' => 8.1,   'status' => 'active',   'region' => 'Asia'],
        ['name' => 'Port of Mumbai',        'country' => 'India',        'lat' => 18.9322,  'lon' => 72.8375,  'volume' => 6.2,   'status' => 'active',   'region' => 'Asia'],

        // Europe
        ['name' => 'Port of Rotterdam',     'country' => 'Netherlands',  'lat' => 51.9225,  'lon' => 4.4792,   'volume' => 15.3,  'status' => 'active',   'region' => 'Europe'],
        ['name' => 'Port of Antwerp',       'country' => 'Belgium',      'lat' => 51.2213,  'lon' => 4.3997,   'volume' => 12.0,  'status' => 'active',   'region' => 'Europe'],
        ['name' => 'Port of Hamburg',       'country' => 'Germany',      'lat' => 53.5753,  'lon' => 10.0153,  'volume' => 8.7,   'status' => 'active',   'region' => 'Europe'],
        ['name' => 'Port of Felixstowe',    'country' => 'UK',           'lat' => 51.9605,  'lon' => 1.3509,   'volume' => 4.0,   'status' => 'active',   'region' => 'Europe'],
        ['name' => 'Port of Valencia',      'country' => 'Spain',        'lat' => 39.4561,  'lon' => -0.3266,  'volume' => 5.4,   'status' => 'active',   'region' => 'Europe'],
        ['name' => 'Port of Piraeus',       'country' => 'Greece',       'lat' => 37.9479,  'lon' => 23.6383,  'volume' => 5.6,   'status' => 'active',   'region' => 'Europe'],

        // Americas
        ['name' => 'Port of Los Angeles',   'country' => 'USA',          'lat' => 33.7283,  'lon' => -118.2621,'volume' => 10.7,  'status' => 'active',   'region' => 'Americas'],
        ['name' => 'Port of Long Beach',    'country' => 'USA',          'lat' => 33.7542,  'lon' => -118.2165,'volume' => 9.1,   'status' => 'active',   'region' => 'Americas'],
        ['name' => 'Port of New York',      'country' => 'USA',          'lat' => 40.6840,  'lon' => -74.0440, 'volume' => 9.5,   'status' => 'active',   'region' => 'Americas'],
        ['name' => 'Port of Santos',        'country' => 'Brazil',       'lat' => -23.9619, 'lon' => -46.3042, 'volume' => 4.2,   'status' => 'active',   'region' => 'Americas'],
        ['name' => 'Port of Colón',         'country' => 'Panama',       'lat' => 9.3702,   'lon' => -79.8969, 'volume' => 5.0,   'status' => 'active',   'region' => 'Americas'],

        // Middle East & Africa
        ['name' => 'Port of Dubai (Jebel Ali)', 'country' => 'UAE',     'lat' => 25.0657,  'lon' => 55.1713,  'volume' => 14.4,  'status' => 'active',   'region' => 'Middle East'],
        ['name' => 'Port of Salalah',       'country' => 'Oman',         'lat' => 16.9407,  'lon' => 54.0024,  'volume' => 4.5,   'status' => 'active',   'region' => 'Middle East'],
        ['name' => 'Port of Dammam',        'country' => 'Saudi Arabia', 'lat' => 26.4367,  'lon' => 50.1033,  'volume' => 2.8,   'status' => 'active',   'region' => 'Middle East'],
        ['name' => 'Port of Durban',        'country' => 'South Africa', 'lat' => -29.8587, 'lon' => 31.0218,  'volume' => 2.9,   'status' => 'active',   'region' => 'Africa'],
        ['name' => 'Port of Alexandria',    'country' => 'Egypt',        'lat' => 31.2001,  'lon' => 29.9187,  'volume' => 1.8,   'status' => 'busy',     'region' => 'Africa'],
    ];

    public function index(Request $request)
    {
        $region = $request->get('region', 'all');
        $ports = $this->ports;

        if ($region !== 'all') {
            $ports = array_filter($ports, fn($p) => $p['region'] === $region);
            $ports = array_values($ports);
        }

        $regions = ['all', 'Asia', 'Europe', 'Americas', 'Middle East', 'Africa'];

        $stats = [
            'total'  => count($ports),
            'active' => count(array_filter($ports, fn($p) => $p['status'] === 'active')),
            'busy'   => count(array_filter($ports, fn($p) => $p['status'] === 'busy')),
        ];

        return view('port.index', compact('ports', 'regions', 'region', 'stats'));
    }
}