<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->string('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('query');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('risk_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3);
            $table->string('country_name');
            $table->float('risk_score');
            $table->string('risk_level');
            $table->string('alert_type')->default('high_risk');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('currency_history', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 10);
            $table->string('target_currency', 10);
            $table->float('rate');
            $table->date('recorded_date');
            $table->timestamps();
        });

        Schema::create('sentiment_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->enum('type', ['positive', 'negative']);
            $table->integer('weight')->default(1);
            $table->timestamps();
        });

        Schema::create('regional_stats', function (Blueprint $table) {
            $table->id();
            $table->string('region');
            $table->integer('total_ports')->default(0);
            $table->float('avg_risk_score')->default(0);
            $table->string('dominant_risk_level')->default('low');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regional_stats');
        Schema::dropIfExists('sentiment_keywords');
        Schema::dropIfExists('currency_history');
        Schema::dropIfExists('risk_alerts');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('news_categories');
        Schema::dropIfExists('activity_logs');
    }
};