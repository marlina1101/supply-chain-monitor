<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncPortsFromNga extends Command
{
    protected $signature = 'ports:sync';
    protected $description = 'Sinkronisasi data pelabuhan dari World Port Index (NGA). Jika API offline, data lama (cache di DB) tetap dipakai.';

    protected string $url = 'https://msi.nga.mil/api/publications/download?type=view&key=16920959/SFH00000/UpdatedPub150.csv';

    public function handle(): int
    {
        try {
            $response = Http::withoutVerifying()->timeout(15)->retry(2, 3000)->get($this->url);

            if (!$response->successful()) {
                $this->warn('API NGA merespons tapi gagal (HTTP '.$response->status().'). Data lama tetap dipakai.');
                DB::table('system_settings')->updateOrInsert(
                    ['key' => 'ports_sync_status'],
                    ['value' => 'failed_http_'.$response->status(), 'updated_at' => now()]
                );
                return self::FAILURE;
            }

            $rows = array_map('str_getcsv', explode("\n", trim($response->body())));
            $header = array_map('trim', array_shift($rows));

            $count = 0;
            foreach ($rows as $row) {
                if (count($row) !== count($header)) {
                    continue; // baris rusak/kosong, lewati
                }
                $data = array_combine($header, $row);

                $name = $data['Main Port Name'] ?? $data['Port Name'] ?? null;
                $lat  = $data['Latitude'] ?? null;
                $lon  = $data['Longitude'] ?? null;
                $country = $data['Country'] ?? $data['World Water Body'] ?? 'Unknown';
                $ref  = $data['World Port Index Number'] ?? $data['Index Number'] ?? null;

                if (!$name || !is_numeric($lat) || !is_numeric($lon)) {
                    continue; // field wajib tidak lengkap, lewati baris ini saja
                }

                DB::table('ports')->updateOrInsert(
                    ['external_ref' => $ref, 'name' => $name],
                    [
                        'country'    => $country,
                        'region'     => $this->guessRegion($country),
                        'latitude'   => (float) $lat,
                        'longitude'  => (float) $lon,
                        'status'     => DB::table('ports')->where('external_ref', $ref)->value('status') ?? 'active',
                        'volume'     => DB::table('ports')->where('external_ref', $ref)->value('volume') ?? 0,
                        'source'     => 'nga',
                        'synced_at'  => now(),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
                $count++;
            }

            DB::table('system_settings')->updateOrInsert(
                ['key' => 'ports_sync_status'],
                ['value' => 'success', 'updated_at' => now()]
            );
            DB::table('system_settings')->updateOrInsert(
                ['key' => 'ports_last_synced_at'],
                ['value' => now()->toDateTimeString(), 'updated_at' => now()]
            );

            $this->info("Sinkronisasi berhasil: {$count} pelabuhan diperbarui.");
            return self::SUCCESS;

        } catch (\Exception $e) {
            // API benar-benar offline / timeout / tidak terjangkau.
            // TIDAK menghapus atau mengubah data ports yang sudah ada -> halaman
            // /port tetap menampilkan data terakhir yang berhasil disinkronkan.
            Log::warning('Gagal sinkronisasi ports dari NGA: '.$e->getMessage());

            DB::table('system_settings')->updateOrInsert(
                ['key' => 'ports_sync_status'],
                ['value' => 'offline', 'updated_at' => now()]
            );

            $this->error('API NGA offline/tidak terjangkau. Data pelabuhan yang tampil tetap memakai cache terakhir.');
            return self::FAILURE;
        }
    }

    protected function guessRegion(string $country): string
    {
        $map = [
            'Asia'         => ['China', 'Japan', 'Singapore', 'Indonesia', 'Malaysia', 'Thailand', 'India', 'South Korea', 'Hong Kong'],
            'Europe'       => ['Netherlands', 'Belgium', 'Germany', 'UK', 'Spain', 'Greece'],
            'Americas'     => ['USA', 'Brazil', 'Panama'],
            'Middle East'  => ['UAE', 'Oman', 'Saudi Arabia'],
            'Africa'       => ['South Africa', 'Egypt'],
        ];
        foreach ($map as $region => $countries) {
            if (in_array($country, $countries, true)) {
                return $region;
            }
        }
        return 'Other';
    }
}