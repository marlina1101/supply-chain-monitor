<?php

namespace App\Services;

class RiskCalculator
{
    // Bobot risiko sesuai spesifikasi
    const WEIGHTS = [
        'weather'   => 0.30,
        'inflation' => 0.20,
        'currency'  => 0.10,
        'news'      => 0.40,
    ];

    // Hitung total risk score (0-100)
    public static function calculate($weather, $inflation, $currency, $news)
    {
        $total = ($weather  * self::WEIGHTS['weather'])
               + ($inflation * self::WEIGHTS['inflation'])
               + ($currency  * self::WEIGHTS['currency'])
               + ($news      * self::WEIGHTS['news']);

        return round(min($total, 100), 1);
    }

    // Tentukan level risiko
    public static function getLevel($score)
    {
        if ($score < 35) return ['level' => 'Low Risk',    'class' => 'success', 'color' => '#2e7d32'];
        if ($score < 65) return ['level' => 'Medium Risk', 'class' => 'warning', 'color' => '#e65100'];
        return              ['level' => 'High Risk',   'class' => 'danger',  'color' => '#b71c1c'];
    }

    // Hitung weather risk dari weathercode & angin
    public static function weatherRisk($weatherCode, $windSpeed)
    {
        $base = 0;
        if (in_array($weatherCode, [95, 96, 99])) $base = 90; // Badai
        elseif (in_array($weatherCode, [80,81,82])) $base = 60; // Hujan lebat
        elseif (in_array($weatherCode, [61,63,65])) $base = 40; // Hujan
        elseif (in_array($weatherCode, [51,53,55])) $base = 20; // Gerimis
        else $base = 10;

        $windRisk = min($windSpeed / 100 * 50, 50);
        return min($base + $windRisk, 100);
    }

    // Hitung inflation risk
    public static function inflationRisk($inflationRate)
    {
        if ($inflationRate === null) return 50;
        if ($inflationRate > 20)  return 100;
        if ($inflationRate > 10)  return 80;
        if ($inflationRate > 5)   return 60;
        if ($inflationRate > 3)   return 40;
        if ($inflationRate > 0)   return 20;
        return 10;
    }

    // Hitung currency risk berdasarkan perubahan kurs
    public static function currencyRisk($rate, $baseRate = 1.0)
    {
        if ($baseRate == 0) return 50;
        $change = abs(($rate - $baseRate) / $baseRate) * 100;
        return min($change * 5, 100);
    }
}