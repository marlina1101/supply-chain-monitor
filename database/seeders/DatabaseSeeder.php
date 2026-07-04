<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== SENTIMENT KEYWORDS =====
        $positive = [
            'growth', 'increase', 'profit', 'stable', 'improve',
            'rise', 'gain', 'recovery', 'boost', 'strong',
            'positive', 'expand', 'surge', 'success', 'benefit',
            'opportunity', 'advance', 'progress', 'deal', 'agreement',
            'partnership', 'invest', 'innovation', 'efficient',
        ];

        $negative = [
            'war', 'crisis', 'inflation', 'delay', 'disaster',
            'conflict', 'decline', 'fall', 'risk', 'threat',
            'shortage', 'disruption', 'loss', 'recession', 'collapse',
            'sanction', 'tariff', 'protest', 'strike', 'flood',
            'earthquake', 'pandemic', 'uncertainty', 'tension',
            'ban', 'embargo', 'corruption', 'fraud', 'attack', 'blockade',
        ];

        foreach ($positive as $word) {
            DB::table('sentiment_keywords')->insertOrIgnore([
                'word' => $word, 'type' => 'positive', 'weight' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        foreach ($negative as $word) {
            DB::table('sentiment_keywords')->insertOrIgnore([
                'word' => $word, 'type' => 'negative', 'weight' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // ===== NEWS CATEGORIES =====
        $categories = [
            ['key' => 'economy',     'label' => '💰 Ekonomi Global',           'query' => 'global economy trade'],
            ['key' => 'logistics',   'label' => '🚢 Logistik & Rantai Pasok',  'query' => 'supply chain logistics shipping'],
            ['key' => 'geopolitics', 'label' => '🌍 Geopolitik',               'query' => 'geopolitics conflict international'],
            ['key' => 'trade',       'label' => '📦 Perdagangan Internasional', 'query' => 'international trade export import'],
        ];

        foreach ($categories as $cat) {
            DB::table('news_categories')->insertOrIgnore([
                'key'        => $cat['key'],
                'label'      => $cat['label'],
                'query'      => $cat['query'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ===== SYSTEM SETTINGS =====
        $settings = [
            ['key' => 'app_name',              'value' => 'RiskRadar',  'description' => 'Nama aplikasi'],
            ['key' => 'risk_low_max',          'value' => '35',         'description' => 'Batas maksimal Low Risk'],
            ['key' => 'risk_medium_max',       'value' => '65',         'description' => 'Batas maksimal Medium Risk'],
            ['key' => 'news_refresh_interval', 'value' => '300',        'description' => 'Interval refresh berita (detik)'],
            ['key' => 'weather_cities',        'value' => '8',          'description' => 'Jumlah kota cuaca dipantau'],
        ];

        foreach ($settings as $s) {
            DB::table('system_settings')->insertOrIgnore([
                'key'         => $s['key'],
                'value'       => $s['value'],
                'description' => $s['description'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ===== REGIONAL STATS =====
        $regions = [
            ['region' => 'Asia',        'total_ports' => 12, 'avg_risk_score' => 35.5, 'dominant_risk_level' => 'medium'],
            ['region' => 'Europe',      'total_ports' => 6,  'avg_risk_score' => 28.0, 'dominant_risk_level' => 'low'],
            ['region' => 'Americas',    'total_ports' => 5,  'avg_risk_score' => 30.0, 'dominant_risk_level' => 'low'],
            ['region' => 'Middle East', 'total_ports' => 3,  'avg_risk_score' => 45.0, 'dominant_risk_level' => 'medium'],
            ['region' => 'Africa',      'total_ports' => 2,  'avg_risk_score' => 50.0, 'dominant_risk_level' => 'medium'],
        ];

        foreach ($regions as $r) {
            DB::table('regional_stats')->insertOrIgnore([
                'region'               => $r['region'],
                'total_ports'          => $r['total_ports'],
                'avg_risk_score'       => $r['avg_risk_score'],
                'dominant_risk_level'  => $r['dominant_risk_level'],
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }

        echo "✅ Seeding selesai!\n";
    }
}