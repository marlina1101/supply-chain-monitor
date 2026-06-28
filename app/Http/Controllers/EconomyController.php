<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EconomyController extends Controller
{
    // Negara-negara strategis untuk rantai pasok global
    private $countries = [
        'US' => 'Amerika Serikat',
        'CN' => 'China',
        'DE' => 'Jerman',
        'JP' => 'Jepang',
        'GB' => 'Inggris',
        'IN' => 'India',
        'FR' => 'Prancis',
        'BR' => 'Brasil',
        'KR' => 'Korea Selatan',
        'ID' => 'Indonesia',
        'SG' => 'Singapura',
        'MY' => 'Malaysia',
        'TH' => 'Thailand',
        'AU' => 'Australia',
        'SA' => 'Arab Saudi',
    ];

    public function index(Request $request)
    {
        $indicator = $request->get('indicator', 'NY.GDP.MKTP.CD');
        $economyData = [];
        $error = null;

        $indicators = [
            'NY.GDP.MKTP.CD' => 'GDP (USD)',
            'FP.CPI.TOTL.ZG' => 'Inflasi (%)',
            'SP.POP.TOTL'     => 'Populasi',
            'NE.EXP.GNFS.ZS'  => 'Ekspor (% GDP)',
            'NE.IMP.GNFS.ZS'  => 'Impor (% GDP)',
        ];

        try {
            $codes = implode(';', array_keys($this->countries));

            $response = Http::withoutVerifying()->timeout(30)
                ->get("https://api.worldbank.org/v2/country/{$codes}/indicator/{$indicator}", [
                    'format'   => 'json',
                    'per_page' => 100,
                    'mrv'      => 1, // Most Recent Value
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data[1]) && is_array($data[1])) {
                    foreach ($data[1] as $item) {
                        $countryCode = $item['countryiso3code'] ?? '';
                        $iso2 = $item['country']['id'] ?? '';

                        if ($item['value'] !== null) {
                            $economyData[] = [
                                'country' => $item['country']['value'],
                                'code'    => $iso2,
                                'value'   => $item['value'],
                                'year'    => $item['date'],
                            ];
                        }
                    }

                    // Urutkan dari terbesar
                    usort($economyData, fn($a, $b) => $b['value'] <=> $a['value']);
                }
            }
        } catch (\Exception $e) {
            $error = 'Gagal mengambil data ekonomi: ' . $e->getMessage();
        }

        return view('economy.index', compact('economyData', 'indicator', 'indicators', 'error'));
    }

    private function formatValue($value, $indicator)
    {
        if ($indicator === 'NY.GDP.MKTP.CD') {
            return '$' . number_format($value / 1e12, 2) . ' T';
        }
        if ($indicator === 'SP.POP.TOTL') {
            return number_format($value / 1e6, 1) . ' Juta';
        }
        return number_format($value, 2) . '%';
    }
}