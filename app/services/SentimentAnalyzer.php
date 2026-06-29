<?php

namespace App\Services;

class SentimentAnalyzer
{
    private static $positiveWords = [
        'growth', 'increase', 'profit', 'stable', 'improve', 'rise',
        'gain', 'recovery', 'boost', 'strong', 'positive', 'expand',
        'surge', 'success', 'benefit', 'opportunity', 'advance', 'progress',
        'deal', 'agreement', 'partnership', 'invest', 'innovation', 'efficient',
    ];

    private static $negativeWords = [
        'war', 'crisis', 'inflation', 'delay', 'disaster', 'conflict',
        'decline', 'fall', 'risk', 'threat', 'shortage', 'disruption',
        'loss', 'recession', 'collapse', 'sanction', 'tariff', 'protest',
        'strike', 'flood', 'earthquake', 'pandemic', 'uncertainty', 'tension',
        'ban', 'embargo', 'corruption', 'fraud', 'attack', 'blockade',
    ];

    public static function analyze($text)
    {
        $text  = strtolower($text);
        $words = preg_split('/\W+/', $text);

        $pos = 0;
        $neg = 0;

        foreach ($words as $word) {
            if (in_array($word, self::$positiveWords)) $pos++;
            if (in_array($word, self::$negativeWords)) $neg++;
        }

        $total = $pos + $neg;
        if ($total === 0) {
            return ['sentiment' => 'neutral', 'score' => 0, 'positive' => 0, 'negative' => 0];
        }

        $score     = round((($pos - $neg) / $total) * 100);
        $sentiment = $score > 10 ? 'positive' : ($score < -10 ? 'negative' : 'neutral');

        // Konversi ke risk score (negative = high risk)
        $riskScore = max(0, min(100, 50 - $score));

        return [
            'sentiment'  => $sentiment,
            'score'      => $score,
            'risk_score' => $riskScore,
            'positive'   => $pos,
            'negative'   => $neg,
        ];
    }
}