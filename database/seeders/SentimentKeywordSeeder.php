<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SentimentKeywordSeeder extends Seeder
{
    public function run(): void
    {
        $positive = ['growth', 'increase', 'profit', 'stable', 'improve',
                     'rise', 'gain', 'recovery', 'boost', 'strong',
                     'positive', 'expand', 'surge', 'success', 'benefit',
                     'opportunity', 'advance', 'progress', 'deal', 'agreement',
                     'partnership', 'invest', 'innovation', 'efficient'];

        $negative = ['war', 'crisis', 'inflation', 'delay', 'disaster',
                     'conflict', 'decline', 'fall', 'risk', 'threat',
                     'shortage', 'disruption', 'loss', 'recession', 'collapse',
                     'sanction', 'tariff', 'protest', 'strike', 'flood',
                     'earthquake', 'pandemic', 'uncertainty', 'tension',
                     'ban', 'embargo', 'corruption', 'fraud', 'attack', 'blockade'];

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

        // Seed news_categories
        $categories = [
            ['key' => 'economy',     'label' => '💰 Ekonomi Global',          'query' => 'global economy trade'],
            ['key' => 'logistics',   'label' => '🚢 Logistik & Rantai Pasok', 'query' => 'supply chain logistics shipping'],
            ['key' => 'geopolitics', 'label' => '🌍 Geopolitik',              'query' => 'geopolitics conflict international'],
            ['key' => 'trade',       'label' => '📦 Perdagangan Internasional','query' => 'international trade export import'],
        ];
        foreach ($categories as $cat) {
            DB::table('news_categories')->insertOrIgnore([
                ...$cat, 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Seed system_settings
        $settings = [
            ['key' => 'app_name',         'value' => 'RiskRadar',       'description' => 'Nama aplikasi'],
            ['key' => 'risk_low_max',     'value' => '35',              'description' => 'Batas maksimal Low Risk'],
            ['key' => 'risk_medium_max',  'value' => '65',              'description' => 'Batas maksimal Medium Risk'],
            ['key' => 'news_refresh_interval', 'value' => '300',        'description' => 'Interval refresh berita (detik)'],
            ['key' => 'weather_cities',   'value' => '8',               'description' => 'Jumlah kota cuaca dipantau'],
        ];
        foreach ($settings as $s) {
            DB::table('system_settings')->insertOrIgnore([
                ...$s, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Seed regional_stats
        $regions = [
            ['region' => 'Asia',        'total_ports' => 12, 'avg_risk_score' => 35.5, 'dominant_risk_level' => 'medium'],
            ['region' => 'Europe',      'total_ports' => 6,  'avg_risk_score' => 28.0, 'dominant_risk_level' => 'low'],
            ['region' => 'Americas',    'total_ports' => 5,  'avg_risk_score' => 30.0, 'dominant_risk_level' => 'low'],
            ['region' => 'Middle East', 'total_ports' => 3,  'avg_risk_score' => 45.0, 'dominant_risk_level' => 'medium'],
            ['region' => 'Africa',      'total_ports' => 2,  'avg_risk_score' => 50.0, 'dominant_risk_level' => 'medium'],
        ];
        foreach ($regions as $r) {
            DB::table('regional_stats')->insertOrIgnore([
                ...$r, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}