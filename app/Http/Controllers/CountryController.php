<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $region = $request->get('region', '');
        $countries = [];

        try {
            $response = Http::withoutVerifying()->timeout(30)
                ->get('https://countriesnow.space/api/v0.1/countries/info?returns=name,capital,currency,flag,dialCode,unicodeFlag,iso2,iso3');

            if ($response->successful()) {
                $data = $response->json()['data'];

                // Filter pencarian
                if (!empty($search)) {
                    $data = array_filter($data, fn($c) =>
                        stripos($c['name'], $search) !== false
                    );
                }

                // Urutkan nama
                usort($data, fn($a, $b) =>
                    strcmp($a['name'], $b['name'])
                );

                foreach ($data as $c) {
                    $countries[] = [
                        'name'     => $c['name'] ?? 'N/A',
                        'capital'  => $c['capital'] ?? 'N/A',
                        'currency' => $c['currency'] ?? 'N/A',
                        'flag'     => $c['flag'] ?? '',
                        'emoji'    => $c['unicodeFlag'] ?? '',
                        'dial'     => $c['dialCode'] ?? 'N/A',
                        'iso2'     => $c['iso2'] ?? '',
                        'iso3'     => $c['iso3'] ?? '',
                    ];
                }
            }
        } catch (\Exception $e) {
            $countries = [];
        }

        $stats = [
            'total'   => count($countries),
            'regions' => ['Africa', 'Americas', 'Asia', 'Europe', 'Oceania'],
        ];

        return view('country.index', compact('countries', 'stats', 'search', 'region'));
    }

    public function api(Request $request)
{
    $search = $request->get('search', '');
    $countries = [];

    try {
        $response = Http::withoutVerifying()->timeout(30)
            ->get('https://countriesnow.space/api/v0.1/countries/info?returns=name,capital,currency,flag,dialCode,unicodeFlag,iso2,iso3');

        if ($response->successful()) {
            $data = $response->json()['data'];
            if (!empty($search)) {
                $data = array_filter($data, fn($c) => stripos($c['name'], $search) !== false);
            }
            $countries = array_values($data);
        }
    } catch (\Exception $e) {}

    return response()->json([
        'success' => true,
        'total'   => count($countries),
        'data'    => $countries,
    ]);
}

}