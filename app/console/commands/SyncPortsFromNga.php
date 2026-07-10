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

    protected array $columnMap = [
        'name'      => ['main_port_', 'Main Port Name', 'Port Name'],
        'country'   => ['countryCode', 'Country', 'Country Code'],
        'region'    => ['regionname', 'Region Name', 'World Water Body'],
        'latitude'  => ['Latitude', 'latitude'],
        'longitude' => ['Longitude', 'longitude'],
        'ref'       => ['wpinumber', 'WPI Number', 'World Port Index Number', 'Index Number'],
    ];

    public function handle(): int
    {
        try {
            $response = Http::withoutVerifying()->timeout(15)->retry(2, 3000)->get($this->url);

            if (!$response->successful()) {
                $this->warn('API NGA merespons tapi gagal (HTTP '.$response->status().'). Data lama tetap dipakai.');
                $this->markStatus('failed_http_'.$response->status());
                return self::FAILURE;
            }

            $body = trim($response->body());

            if ($body === '' || str_starts_with(ltrim($body), '<')) {
                $this->error('Respons dari NGA bukan file CSV. Data lama tetap dipakai.');
                $this->markStatus('failed_invalid_format');
                return self::FAILURE;
            }

            $rows = array_map('str_getcsv', explode("\n", $body));
            $header = array_map('trim', array_shift($rows));

            $count = 0;
            $skipped = 0;

            foreach ($rows as $row) {
                if (count($row) !== count($header)) {
                    $skipped++;
                    continue;
                }
                $data = array_combine($header, $row);

                $name      = $this->pick($data, 'name');
                $lat       = $this->pick($data, 'latitude');
                $lon       = $this->pick($data, 'longitude');
                $country   = $this->pick($data, 'country') ?? 'Unknown';
                $regionRaw = $this->pick($data, 'region');
                $ref       = $this->pick($data, 'ref');

                if (!$name || !is_numeric($lat) || !is_numeric($lon)) {
                    $skipped++;
                    continue;
                }

                DB::table('ports')->updateOrInsert(
                    ['external_ref' => $ref, 'name' => $name],
                    [
                        'country'    => $country,
                        'region'     => $this->guessRegion($country, $regionRaw),
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

            $this->markStatus('success');
            DB::table('system_settings')->updateOrInsert(
                ['key' => 'ports_last_synced_at'],
                ['value' => now()->toDateTimeString(), 'updated_at' => now()]
            );

            $this->info("Sinkronisasi berhasil: {$count} pelabuhan diperbarui, {$skipped} baris dilewati.");
            return self::SUCCESS;

        } catch (\Exception $e) {
            Log::warning('Gagal sinkronisasi ports dari NGA: '.$e->getMessage());
            $this->markStatus('offline');
            $this->error('API NGA offline/tidak terjangkau. Data pelabuhan yang tampil tetap memakai cache terakhir.');
            return self::FAILURE;
        }
    }

    protected function pick(array $data, string $field): ?string
    {
        foreach ($this->columnMap[$field] as $possibleKey) {
            if (array_key_exists($possibleKey, $data) && $data[$possibleKey] !== '') {
                return trim($data[$possibleKey]);
            }
        }
        return null;
    }

    protected function markStatus(string $status): void
    {
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'ports_sync_status'],
            ['value' => $status, 'updated_at' => now()]
        );
    }

    protected function guessRegion(string $country, ?string $regionRaw): string
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
        return $regionRaw ?: 'Other';
    }
}