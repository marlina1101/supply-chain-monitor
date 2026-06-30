<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('EXCHANGE_RATE_API_KEY');
    }

    public function index(Request $request)
    {
        $base = $request->get('base', 'USD');
        $rates = [];
        $error = null;

        // Mata uang utama dunia yang relevan untuk rantai pasok
        $mainCurrencies = [
            'USD' => ['name' => 'US Dollar',          'country' => 'Amerika Serikat', 'flag' => '🇺🇸'],
            'EUR' => ['name' => 'Euro',                'country' => 'Uni Eropa',       'flag' => '🇪🇺'],
            'CNY' => ['name' => 'Chinese Yuan',        'country' => 'China',           'flag' => '🇨🇳'],
            'JPY' => ['name' => 'Japanese Yen',        'country' => 'Jepang',          'flag' => '🇯🇵'],
            'GBP' => ['name' => 'British Pound',       'country' => 'Inggris',         'flag' => '🇬🇧'],
            'SGD' => ['name' => 'Singapore Dollar',    'country' => 'Singapura',       'flag' => '🇸🇬'],
            'AED' => ['name' => 'UAE Dirham',          'country' => 'UAE',             'flag' => '🇦🇪'],
            'KRW' => ['name' => 'South Korean Won',    'country' => 'Korea Selatan',   'flag' => '🇰🇷'],
            'INR' => ['name' => 'Indian Rupee',        'country' => 'India',           'flag' => '🇮🇳'],
            'IDR' => ['name' => 'Indonesian Rupiah',   'country' => 'Indonesia',       'flag' => '🇮🇩'],
            'MYR' => ['name' => 'Malaysian Ringgit',   'country' => 'Malaysia',        'flag' => '🇲🇾'],
            'THB' => ['name' => 'Thai Baht',           'country' => 'Thailand',        'flag' => '🇹🇭'],
            'AUD' => ['name' => 'Australian Dollar',   'country' => 'Australia',       'flag' => '🇦🇺'],
            'CAD' => ['name' => 'Canadian Dollar',     'country' => 'Kanada',          'flag' => '🇨🇦'],
            'CHF' => ['name' => 'Swiss Franc',         'country' => 'Swiss',           'flag' => '🇨🇭'],
            'SAR' => ['name' => 'Saudi Riyal',         'country' => 'Arab Saudi',      'flag' => '🇸🇦'],
        ];

        try {
            $response = Http::withoutVerifying()->timeout(30)
                ->get("https://v6.exchangerate-api.com/v6/{$this->apiKey}/latest/{$base}");

            if ($response->successful()) {
                $data = $response->json();

                if ($data['result'] === 'success') {
                    $allRates = $data['conversion_rates'];

                    foreach ($mainCurrencies as $code => $info) {
                        if (isset($allRates[$code])) {
                            $rates[] = [
                                'code'    => $code,
                                'name'    => $info['name'],
                                'country' => $info['country'],
                                'flag'    => $info['flag'],
                                'rate'    => $allRates[$code],
                            ];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $error = 'Gagal mengambil data nilai tukar.';
        }

        return view('currency.index', compact('rates', 'base', 'error', 'mainCurrencies'));
    }

    public function api(Request $request)
{
    $base = $request->get('base', 'USD');
    $rates = [];

    try {
        $response = Http::withoutVerifying()->timeout(30)
            ->get("https://v6.exchangerate-api.com/v6/{$this->apiKey}/latest/{$base}");
        if ($response->successful()) {
            $data = $response->json();
            if ($data['result'] === 'success') {
                $rates = $data['conversion_rates'];
            }
        }
    } catch (\Exception $e) {}

    return response()->json([
        'success' => true,
        'base'    => $base,
        'rates'   => $rates,
    ]);
}

}